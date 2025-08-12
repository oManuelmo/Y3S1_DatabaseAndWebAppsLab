<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductImage;
use App\Models\Image;
use App\Models\Artist;
use App\Models\Bid;
use App\Models\Follow;
use App\Models\Report;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class ItemController extends Controller
{

    /**
     * Shows all auctions.
     */
    public function list()
    {
        $items = Item::where('state', 'Auction')->paginate(12); 
    
        return view('pages.main', [
            'items' => $items
        ]);
    }

    /**
     * Creates a new item.
     */
    public function createForm()
    {
        if (Auth::check()) {
            $famousArtists = Artist::where('isfamous', true)->get();
            $styles = DB::select("SELECT e.enumlabel FROM pg_enum e JOIN pg_type t ON e.enumtypid = t.oid WHERE t.typname = 'styles'");
            $themes = DB::select("SELECT e.enumlabel FROM pg_enum e JOIN pg_type t ON e.enumtypid = t.oid WHERE t.typname = 'themes'");
            $techniques = DB::select("SELECT e.enumlabel FROM pg_enum e JOIN pg_type t ON e.enumtypid = t.oid WHERE t.typname = 'techniques'");
            return view('items.create-item', compact('styles', 'themes', 'techniques', 'famousArtists'));
        } else {
            return view('auth.login');
        }
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'initialprice' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'height' => 'required|numeric|min:0',
            'style' => 'required|string',
            'theme' => 'required|string',
            'technique' => 'required|string',
            'description' => 'required|string|max:500',
            'duration_days' => 'required|integer|min:0',
            'duration_hours' => 'required|integer|min:0|max:23',
            'duration_minutes' => 'required|integer|min:0|max:59',
            'ownerid' => 'required|numeric',
            'images' => 'array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'artist' => 'nullable|exists:artists,artistid',
            'new_artist_name' => 'nullable|string|max:255',
        ]);
        $user = User::findOrFail($request->ownerid);
        if (Gate::denies('userSelf-only', [User::findOrFail(Auth::id()), $user])) {
            return back()->withErrors('You cannot see this page.');
        }
        $durationDays = (int)$request->input('duration_days');
        $durationHours = (int)$request->input('duration_hours');
        $durationMinutes = (int)$request->input('duration_minutes');
        $totalDuration = ($durationDays * 24 * 60) + ($durationHours * 60) + $durationMinutes;

        if($totalDuration<60) {
            return redirect()->back()->withErrors(['DurationHours' => 'The duration must be atleast an hour.']);
        }
        if (!$request->artist && (!$request->new_artist_name || trim($request->new_artist_name) === "")) {
            return redirect()->back()->withErrors(['artistid' => 'Please select a famous artist or provide a valid new artist name.']);
        }

        $artistid = $request->artist;

        if (!$artistid && $request->has('new_artist_name') && trim($request->new_artist_name) !== "") {
            $newArtistName = $request->new_artist_name;

            if (empty($newArtistName)) {
                return redirect()->back()->withErrors(['artistid' => 'Please select a famous artist or provide a valid new artist name.']);
            }
        
            $newArtist = Artist::create([
                'name' => $newArtistName,  
                'isfamous' => false,       
            ]);
        
            $artistid = $newArtist->artistid;
        }

        $item = new Item();
        $item->name = $request->name;
        $item->initialprice = $request->initialprice;
        $item->soldprice = null;
        $item->width = $request->width;
        $item->height = $request->height;
        $item->description = $request->description;
        $item->duration = $totalDuration;
        $item->theme = $request->theme;
        $item->technique = $request->technique;
        $item->style = $request->style;
        $item->artistid = $artistid;
        $item->ownerid = $request->ownerid;
        $item->state = 'Pending';
        $item->topbidder = null;
        $item->save();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $croppedImage) {
                $path = $croppedImage->store('product_images', 'public');
                $image = Image::create(['imageurl' => $path]);
                ProductImage::create(['itemid' => $item->itemid, 'imageid' => $image->imageid]);
            }
        }
    
        return redirect()->route('main')->with('success', 'Item created successfully!');
    }

    /**
     * Updates the state of an individual item.
     */
    public function update(Request $request, $id)
    {
        $item = Item::find($id);

        $this->authorize('ediItem', $item);

        $item->done = $request->input('done');

        $item->save();
        return response()->json($item);
    }

    public function bidItem(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric', 
        ]);
        
        $item = Item::find($id);
        $this->authorize('bidItem', $item);
        $currentPrice = $item->soldprice ?? $item->initialprice;

        if ($request->amount <= $currentPrice) {
            return response()->json(['message' => 'Your bid must be higher than the current price.',],422);
        }
        $lastTopBidder = User::find($item->topbidder);
        if($lastTopBidder){
            $lastTopBidder->bidbalance = $lastTopBidder->bidbalance - $item->soldprice;
            $lastTopBidder->save();
        }
        
        $item->soldprice = $request->amount;
        Log::info("Request amount: ", ['request_amount' => $request->amount]);
        Log::info("Before saving item: ", ['item' => $item]);
        $item->save();
        Log::info("After saving item: ", ['item' => $item]);
 
        return response()->json([
            'success' => true,
            'item' => $item,
        ]);

    }

    /**
    * Deletes a specific item.
    */
    public function delete(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        $this->authorize('deleteItem', $item);
        $ownerid = $item->ownerid;
        $item->delete();
        $items = Item::where('ownerid', $ownerid)->get();
        return view('user.items', [
            'items' => $items,
            'currentState' => "nostate",
            'userId' => $ownerid,
        ]);
    }

    public function show($id)
    {
        $item = Item::findOrFail($id);
        $artistName = Artist::find($item->artistid)->name;
        $images = Image::whereIn('imageid', function($query) use ($id) {
            $query->select('imageid')
                  ->from('product_images')
                  ->where('itemid', $id);
        })->get();
        $isFollowing = Follow::where('followerid', Auth::id())
        ->where('itemid', $id)
        ->exists();
        $reportTypes = DB::select("SELECT unnest(enum_range(NULL::ReportType)) AS value");
        return view('items.item', compact('item', 'images', 'artistName','isFollowing', 'reportTypes'));
    }

    public function editItem($itemId)
    {
        $item = Item::findOrFail($itemId);
        $this->authorize('editItem', $item);
        $famousArtists = Artist::where('isfamous', true)->get();
        $styles = DB::select("SELECT e.enumlabel FROM pg_enum e JOIN pg_type t ON e.enumtypid = t.oid WHERE t.typname = 'styles'");
        $themes = DB::select("SELECT e.enumlabel FROM pg_enum e JOIN pg_type t ON e.enumtypid = t.oid WHERE t.typname = 'themes'");
        $techniques = DB::select("SELECT e.enumlabel FROM pg_enum e JOIN pg_type t ON e.enumtypid = t.oid WHERE t.typname = 'techniques'");
        return view('items.edit-item', compact('item', 'styles', 'themes', 'techniques', 'famousArtists'));
    }
    public function updateItem(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'initialprice' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'height' => 'required|numeric|min:0',
            'style' => 'required|string',
            'theme' => 'required|string',
            'technique' => 'required|string',
            'description' => 'required|string',
            'images' => 'array|max:5',  
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', 
            'artistid' => 'nullable|exists:artists,artistid',
            'new_artist_name' => 'nullable|string|max:100',
            'delete_images' => 'array', 
        ]);
        
        $item = Item::findOrFail($id);
        $this->authorize('editItem', $item);
        
        if (!$request->artistid && (!$request->new_artist_name || trim($request->new_artist_name) === "")) {
            return redirect()->back()->withErrors(['artistid' => 'Please select a famous artist or provide a valid new artist name.']);
        }

        $artistid = $request->artistid;
        
        if (!$artistid && $request->has('new_artist_name') && trim($request->new_artist_name) !== "") {
            $newArtistName = $request->new_artist_name;

            if (empty($newArtistName)) {
                return redirect()->back()->withErrors(['artistid' => 'Please select a famous artist or provide a valid new artist name.']);
            }
        
            $newArtist = Artist::create([
                'name' => $newArtistName,  
                'isfamous' => false,       
            ]);
        
            $artistid = $newArtist->artistid;
        }

        $item->update([
            'name' => $validated['name'],
            'initialprice' => $validated['initialprice'],
            'width' => $validated['width'],
            'height' => $validated['height'],
            'style' => $validated['style'],
            'theme' => $validated['theme'],
            'technique' => $validated['technique'],
            'description' => $validated['description'],
            'artistid' => $artistid,
        ]);
    
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                try {
                    $path = $imageFile->store('product_images', 'public');
                } catch (\Exception $e) {
                    return back()->withErrors(['images' => 'There was an error uploading the image.']);
                }
    
                $image = Image::create(['imageurl' => $path]);
    
                ProductImage::create([
                    'itemid' => $item->itemid,
                    'imageid' => $image->imageid,
                ]);
            }
        }
    
        if ($request->has('delete_images') && !empty($request->input('delete_images'))) {
            $imagesToDelete = $request->input('delete_images');
    
            foreach ($imagesToDelete as $imageURL) {
                $image = DB::table('images')->where('imageurl', $imageURL)->first();
    
                if ($image) {
                    DB::table('product_images')
                        ->where('itemid', $item->itemid)
                        ->where('imageid', $image->imageid)
                        ->delete();
    
                    DB::table('images')->where('imageid', $image->imageid)->delete();
    
                    $imagePath = str_replace('/storage/', 'public/', $imageURL);
                    if (Storage::exists($imagePath)) {
                        Storage::delete($imagePath);
                    }
                }
            }
        }
    
        return redirect()->route('item.show', ['id' => $item->itemid])->with('success', 'Item updated successfully!');
    }
    

    public function seeBidHistory($id)
    {
        $item = Item::findOrFail($id);
    
        $bids = Bid::where('itemid', $id)
                    ->with('bidder')
                    ->get();

        return view('items.bid-history', compact('item', 'bids'));
    }

    public function report(Request $request)
    {
        $item = Item::findOrFail($request->reportedauction);
        $this->authorize('reportItem', $item);
        $enumValues = array_column(
            DB::select("SELECT unnest(enum_range(NULL::ReportType)) AS value"),
            'value'
        );
    
        $validated = $request->validate([
            'reportedauction' => 'required|exists:items,itemid',
            'type' => 'required|in:' . implode(',', $enumValues),
            'reportText' => 'nullable|string|max:255', 
        ]);
    

        $report = Report::create([
            'reportedauction' => $validated['reportedauction'],
            'userid' => auth()->id(), 
            'type' => $validated['type'],
            'reporttext' => $validated['reportText'], 
        ]);

        if ($report) {
            return redirect()->back()->with('success', 'Your report has been submitted.');
        }
    
        return redirect()->back()->with('error', 'There was an issue submitting your report.');
    }


}


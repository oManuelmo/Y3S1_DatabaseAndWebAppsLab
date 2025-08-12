<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Item;
use App\Models\Artist;
use App\Models\Follow;
use App\Models\Notification;
use App\Events\ItemNotification;
use App\Models\Bid;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\ProductImage;
use App\Models\Image;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

use Carbon\Carbon;
use Carbon\CarbonInterval;

use Illuminate\Support\Collection;

use IcehouseVentures\LaravelChartjs\Facades\Chartjs;

class AdminController extends Controller
{
    public function dashboard()
    {
        $itemsByState = Item::select('state', DB::raw('COUNT(*) as count'))
            ->groupBy('state')
            ->get();

        $states = $itemsByState->pluck('state')->toArray();
        $counts = $itemsByState->pluck('count')->toArray();

        $chart = Chartjs::build()
            ->name("ItemsByStateChart")
            ->type("bar")
            ->size(["width" => 400, "height" => 200])
            ->labels($states)
            ->datasets([
                [
                    "label" => "Items by State",
                    "backgroundColor" => "rgba(212, 175, 55, 0.5)",
                    "borderColor" => "rgba(212, 175, 55, 1)",
                    "data" => $counts
                ]
            ])
            ->options([
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'ticks' => [
                            'precision' => 0,
                        ],
                    ]
                ],
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Items by State'
                    ]
                ]
            ]);
        
        $stylesCount = Item::whereNotNull('style')->groupBy('style')->selectRaw('style, COUNT(*) as count')->get();
        $themesCount = Item::whereNotNull('theme')->groupBy('theme')->selectRaw('theme, COUNT(*) as count')->get();
        $techniquesCount = Item::whereNotNull('technique')->groupBy('technique')->selectRaw('technique, COUNT(*) as count')->get();

        $styles = $stylesCount->pluck('style')->toArray();
        $stylesCounts = $stylesCount->pluck('count')->toArray();
    
        $themes = $themesCount->pluck('theme')->toArray();
        $themesCounts = $themesCount->pluck('count')->toArray();
    
        $techniques = $techniquesCount->pluck('technique')->toArray();
        $techniquesCounts = $techniquesCount->pluck('count')->toArray();

        $stylesChart = Chartjs::build()
        ->name("StylesChart")
        ->type("pie")
        ->size(["width" => 400, "height" => 400])
        ->labels($styles)
        ->datasets([
            [
                "label" => "Styles",
                "backgroundColor" => ["rgba(54, 162, 235, 0.5)", "rgba(255, 99, 132, 0.5)", "rgba(75, 192, 192, 0.5)", "rgba(153, 102, 255, 0.5)"],
                "borderColor" => ["rgba(54, 162, 235, 1)", "rgba(255, 99, 132, 1)", "rgba(75, 192, 192, 1)", "rgba(153, 102, 255, 1)"],
                "data" => $stylesCounts
            ]
        ])
        ->options([
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => 'Items by Styles'
                ]
            ]
        ]);

        $themesChart = Chartjs::build()
            ->name("ThemesChart")
            ->type("pie")
            ->size(["width" => 400, "height" => 400])
            ->labels($themes)
            ->datasets([
                [
                    "label" => "Themes",
                    "backgroundColor" => ["rgba(54, 162, 235, 0.5)", "rgba(255, 99, 132, 0.5)", "rgba(75, 192, 192, 0.5)", "rgba(153, 102, 255, 0.5)"],
                    "borderColor" => ["rgba(54, 162, 235, 1)", "rgba(255, 99, 132, 1)", "rgba(75, 192, 192, 1)", "rgba(153, 102, 255, 1)"],
                    "data" => $themesCounts
                ]
            ])
            ->options([
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Items by Themes'
                    ]
                ]
            ]);

        $techniquesChart = Chartjs::build()
            ->name("TechniquesChart")
            ->type("pie")
            ->size(["width" => 400, "height" => 400])
            ->labels($techniques)
            ->datasets([
                [
                    "label" => "Techniques",
                    "backgroundColor" => ["rgba(54, 162, 235, 0.5)", "rgba(255, 99, 132, 0.5)", "rgba(75, 192, 192, 0.5)", "rgba(153, 102, 255, 0.5)"],
                    "borderColor" => ["rgba(54, 162, 235, 1)", "rgba(255, 99, 132, 1)", "rgba(75, 192, 192, 1)", "rgba(153, 102, 255, 1)"],
                    "data" => $techniquesCounts
                ]
            ])
            ->options([
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Items by Techniques'
                    ]
                ]
            ]);

        $totalMoney = User::sum('balance');
        $totalBids = Bid::count();

        return view('admin.dashboard', compact(
            'chart', 'stylesChart', 'themesChart', 'techniquesChart', 'totalMoney', 'totalBids'
        ));
    }

    public function users()
    {   
        if (!Gate::allows('admin-only')) {
            abort(403, 'Access denied');
        }
        $users = User::paginate(10);
        return view('admin.users', compact('users'));
    }

    public function showCreateUserForm()
    {
        if (!Gate::allows('admin-only')) {
            abort(403, 'Access denied');
        }
        $countries = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");
        return view('admin.create-user', compact('countries'));
    }

    public function createUser(Request $request)
    {
        if (!Gate::allows('admin-only')) {
            abort(403, 'Access denied');
        }
        $request->validate([
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'birthdate' => 'required|date',
            'email' => 'required|email|max:100|unique:users',
            'password' => 'required|min:8|max:100|confirmed',
            'isadmin' => 'required|boolean'
        ]);
        $birthdate = new \DateTime($request->birthdate);
        $today = new \DateTime();
        $age = $today->diff($birthdate)->y;

        if ($age < 18) {
            return back()->withErrors(['birthdate' => 'User must be 18 yers or older.'])->withInput();
        }

        User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone' => $request->phone_number,
            'password' => Hash::make($request->password),
            'birthdate' => $request->birthdate,
            'isadmin' => $request->isadmin,
            'country' => $request->country,
            'city' => $request->city,
            'postalcode' => $request->postal_code,
            'address' => $request->address,
            'balance' => 0,
            'bidbalance' => 0,
        ]);

        return redirect()->route('admin.users')
            ->withSuccess('User account successfully created.');
    }

    public function searchUsers(Request $request)
    {
        if (!Gate::allows('admin-only')) {
            abort(403, 'Access denied');
        }
        $request->validate([
            'query' => 'nullable|string|max:255',
        ]);

        $query = $request->input('query');

        $queryBuilder = User::query();

        if ($query) {
            $queryBuilder->where(function ($q) use ($query) {
                $q->where('firstname', 'ILIKE', "$query%")
                    ->orWhere('lastname', 'ILIKE', "$query%");
            });
        }

        $results = $queryBuilder->paginate(10);

        return view('admin.users', [
            'users' => $results,
            'query' => $query,
        ]);
    }

    public function editUser($userId)
    {
        $user = User::findOrFail($userId);
        $this->authorize('editUser', $user);
        $countries = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");
        return view('admin.edit-user', compact('user', 'countries'));
    }

    public function updateUser(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $this->authorize('editUser', $user);
        $validated = $request->validate([
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'email' => 'required|email|max:100|unique:users,email,' . $userId . ',userid',
            'password' => 'nullable|string|min:8|max:100',
            'isadmin' => 'required|boolean',
            'rate' => 'nullable|numeric|min:0',
            'address' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postalcode' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'balance' => 'nullable|numeric|min:0',
            'bidbalance' => 'nullable|numeric|min:0',
            'birthdate' => 'nullable|date',
        ]);

        $birthdate = new \DateTime($request->birthdate);
        $today = new \DateTime();
        $age = $today->diff($birthdate)->y;

        if ($age < 18) {
            return back()->withErrors(['birthdate' => 'User must be 18 yers or older.'])->withInput();
        }

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $user->updateProfile($validated);

        return redirect()->route('admin.users')->with('success', 'User updated successfully!');
    }

    public function banUser(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $this->authorize('banUser', $user);
        
        $request->validate([
            'ban_duration' => 'required|string',
            'reason' => 'nullable|string|max:50',
        ]);

        $banUntil = now()->add(CarbonInterval::createFromDateString($request->input('ban_duration')));

        $user->bantime = $banUntil;
        $user->bannedreason = $request->input('reason', 'No reason provided');
        $user->save();

        return response()->json([
            'success' => true,
            'message' => "User banned until {$banUntil->format('Y-m-d H:i:s')}.",
            'data' => [
                'user_id' => $user->id,
                'ban_until' => $banUntil->toDateTimeString(),
                'reason' => $user->bannedreason,
            ],
        ]);
    }

    public function unbanUser($userid)
    {
        $user = User::findOrFail($userid);
        $this->authorize('unbanUser', $user);
        $user->bantime = null;
        $user->bannedreason = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User has been unbanned successfully.',
        ]);
    }

    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);

        if (Gate::denies('delete-user', $user)) {
            return response()->json(['message' => 'You cannot delete yourself.'], 422);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function items()
    {
        if (!Gate::allows('admin-only')) {
            abort(403, 'Access denied');
        }
        $items = Item::paginate(10);
        return view('admin.items', compact('items'));
    }
    
    public function editItem($itemId)
    {
        $item = Item::findOrFail($itemId);
        $this->authorize('editItem', $item);
        if ($item->bids()->exists()) {
            return redirect()->route('admin.items')
                             ->withErrors(['error' => 'Cannot edit an item with bids.'])
                             ->withInput(); 
        }
        $famousArtists = Artist::where('isfamous', true)->get();
        $styles = DB::select("SELECT e.enumlabel FROM pg_enum e JOIN pg_type t ON e.enumtypid = t.oid WHERE t.typname = 'styles'");
        $themes = DB::select("SELECT e.enumlabel FROM pg_enum e JOIN pg_type t ON e.enumtypid = t.oid WHERE t.typname = 'themes'");
        $techniques = DB::select("SELECT e.enumlabel FROM pg_enum e JOIN pg_type t ON e.enumtypid = t.oid WHERE t.typname = 'techniques'");
        return view('admin.edit-item', compact('item', 'styles', 'themes', 'techniques','famousArtists'));
    }

    public function updateItem(Request $request, $itemId)
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
        ]);
    
        $item = Item::findOrFail($itemId);

        
        if ($item->bids()->exists()) {
            return redirect()->route('admin.items')
                             ->withErrors(['error' => 'Cannot edit an item with bids.'])
                             ->withInput(); 
        }

        if (!$request->artistid && (!$request->new_artist_name || trim($request->new_artist_name) === "")) {
            return redirect()->back()->withErrors(['artistid' => 'Please select a famous artist or provide a valid new artist name.']);
        }

        $artistid = $request->artistid;

        if (!$artistid && $request->has('new_artist_name') && trim($request->new_artist_name) !== "") {
            $newArtistName = $request->new_artist_name;

            if (empty($newArtistName)) {
                dd('new_artist_name is empty!');
            }
        
            $newArtist = Artist::create([
                'name' => $newArtistName,  
                'isfamous' => false,       
            ]);
        
            $artistid = $newArtist->artistid;
        }

        $this->authorize('editItem', $item);
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
                $path = $imageFile->store('product_images', 'public');
    
                $image = Image::create(['imageurl' => $path]);
    
                ProductImage::create([
                    'itemid' => $item->itemid,
                    'imageid' => $image->imageid,
                ]);
            }
        }
    
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $deleteIndex) {
                $image = $item->images[$deleteIndex] ?? null;
    
                if ($image) {
                    Storage::disk('public')->delete($image->imageurl);
    
                    $image->delete();
    
                    ProductImage::where('itemid', $item->itemid)
                        ->where('imageid', $image->imageid)
                        ->delete();
                }
            }
        }

        return redirect()->route('admin.items')->with('success', 'Item updated successfully!');
    }

    public function suspendAuction($itemId)
    {
        $item = Item::findOrFail($itemId);

        if (!Gate::allows('admin-only')) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $item->state = 'Suspended';

        if ($item->topbidder) {
            $user = User::findOrFail($item->topbidder);
            $user->bidbalance -= $item->soldprice;
            $user->save();

            $item->topbidder = null;
            $item->soldprice = $item->initialprice;
        }

        $item->save();

        //manda notificacao para todos os seguidores e dono do item a dizer que o item foi suspenso
        $bidders =  Bid::where('itemid', $item->itemid)->with('bidder')->get()->pluck('bidder')->unique();
        $followers = Follow::where('itemid', $item->itemid)->with('user')->get()->pluck('user')->unique();
        $owner = User::find($item->ownerid);
        $participants = $bidders->union($followers)->unique();

        event(new ItemNotification($item,'suspendedowner', $owner->userid));

        Notification::create([    
            'userid' => $owner->userid,
            'type' => 'suspendedowner',
            'bidid' => null,
            'itemid' => $item->itemid,
            'itemname' => null,
            'transactionid' => null,
            'datetime' => Carbon::now(),
        ]);
        
        foreach($participants as $participant){

            event(new ItemNotification($item,'suspended', $participant->userid));

            Notification::create([    
                'userid' => $participant->userid,
                'type' => 'suspended',
                'bidid' => null,
                'itemid' => $item->itemid,
                'itemname' => null,
                'transactionid' => null,
                'datetime' => Carbon::now(),
            ]);
        }

        return response()->json(['message' => 'Item suspended successfully'], 200);
    }
    public function unsuspendItem($itemId)
    {
        $item = Item::findOrFail($itemId);
        if (!Gate::allows('admin-only')) {
            return response()->json(['message' => 'Access denied'], 403);
        }
        $item->state = 'Auction'; 
        $item->save();

        //manda notificacao para todos os seguidores e dono do item a dizer que o item deixou de estar suspenso
        $bidders =  Bid::where('itemid', $item->itemid)->with('bidder')->get()->pluck('bidder')->unique();
        $followers = Follow::where('itemid', $item->itemid)->with('user')->get()->pluck('user')->unique();
        $owner = User::find($item->ownerid);
        $participants = $bidders->union($followers)->unique();

        event(new ItemNotification($item,'unsuspendedowner', $owner->userid));

        Notification::create([    
            'userid' => $owner->userid,
            'type' => 'unsuspendedowner',
            'bidid' => null,
            'itemid' => $item->itemid,
            'itemname' => null,
            'transactionid' => null,
            'datetime' => Carbon::now(),
        ]);
        
        foreach($participants as $participant){

            event(new ItemNotification($item,'unsuspended', $participant->userid));

            Notification::create([    
                'userid' => $participant->userid,
                'type' => 'unsuspended',
                'bidid' => null,
                'itemid' => $item->itemid,
                'itemname' => null,
                'transactionid' => null,
                'datetime' => Carbon::now(),
            ]);
        }

        return response()->json(['message' => 'Item unsuspended successfully.'],200);
    }

    public function deleteItem($itemId)
    {
        $item = Item::findOrFail($itemId);

        if (Gate::denies('deleteItem', $item)) {
            return response()->json(['message' => 'Cannot delete an item with bids.'], 422);
        }

        //manda notificacao para todos os seguidores e dono do item a dizer que o item foi cancelado
        $bidders =  Bid::where('itemid', $item->itemid)->with('bidder')->get()->pluck('bidder')->unique();
        $followers = Follow::where('itemid', $item->itemid)->with('user')->get()->pluck('user')->unique();
        $owner = User::find($item->ownerid);
        $participants = $bidders->union($followers)->unique();

        event(new ItemNotification($item,'canceledowner', $owner->userid));

        $itemName = $item->name;

        Notification::create([    
            'userid' => $owner->userid,
            'type' => 'canceledowner',
            'bidid' => null,
            'itemid' => null,
            'itemname' => $itemName,
            'transactionid' => null,
            'datetime' => Carbon::now(),
        ]);
        
        
        foreach($participants as $participant){

            event(new ItemNotification($item,'canceled', $participant->userid));
            
            Notification::create([    
                'userid' => $participant->userid,
                'type' => 'canceled',
                'bidid' => null,
                'itemid' => null,
                'itemname' => $itemName,
                'transactionid' => null,
                'datetime' => Carbon::now(),
            ]);
            
        }

        $item->delete();

        return response()->json(['message' => 'Item deleted successfully'], 200);
    }

    public function pendingItems()
    {
        if (!Gate::allows('admin-only')) {
            abort(403, 'Access denied');
        }
        $items = Item::where('state', 'Pending')->paginate(10);
        return view('admin.pending-items', compact('items'));
    }

    public function acceptItem($itemId)
    {
        $item = Item::findOrFail($itemId);
        $this->authorize('acceptItem', $item);
        $item->state = 'Auction';
        $startTime = Carbon::now();
        $deadline = $startTime->copy()->addMinutes($item->duration);
        $item->starttime = $startTime;
        $item->deadline = $deadline;
        $item->save();

        return response()->json(['message' => 'Item accepted successfully']);
    }

    public function showCategories(Request $request, $type)
    {
        if (!Gate::allows('admin-only')) {
            abort(403, 'Access denied');
        }

        $validTypes = ['styles', 'themes', 'techniques'];
        if (!in_array($type, $validTypes)) {
            abort(404);
        }

        $items = array_column(DB::select(
            "SELECT e.enumlabel FROM pg_enum e 
            JOIN pg_type t ON e.enumtypid = t.oid 
            WHERE t.typname = ?", [$type]), 'enumlabel');

        // Tem de ser desta forma porque o enum n dá com o ::paginate()
        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $paginatedItems = (new Collection($items))->forPage($currentPage, $perPage);

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            count($items),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.category', [
            'type' => $type,
            'items' => $paginator,
        ]);
    }

    public function addCategory(Request $request, $type)
    {
        if (!Gate::allows('admin-only')) {
            abort(403, 'Access denied');
        }

        $validTypes = ['styles', 'themes', 'techniques'];
        $value = $request->input('value');

        if (!in_array($type, $validTypes)) {
            return response()->json(['error' => 'Invalid category'], 400);
        }

        if (empty($value)) {
            return response()->json(['error' => 'Value cannot be empty'], 400);
        }

        $existingValues = DB::select("SELECT e.enumlabel FROM pg_enum e JOIN pg_type t ON e.enumtypid = t.oid WHERE t.typname = ?", [$type]);

        if (in_array($value, array_column($existingValues, 'enumlabel'))) {
            return response()->json(['error' => 'Value already exists'], 400);
        }

        try {
            DB::statement("ALTER TYPE $type ADD VALUE '$value'");
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add value: ' . $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Value added successfully']);
    }
    public function deleteCategory(Request $request, $type)
    {
        if (!Gate::allows('admin-only')) {
            abort(403, 'Access denied');
        }

        $validTypes = ['styles', 'themes', 'techniques'];
        $value = $request->input('value');

        if (!in_array($type, $validTypes)) {
            return response()->json(['error' => 'Invalid category'], 400);
        }

        if (empty($value)) {
            return response()->json(['error' => 'Value cannot be empty'], 400);
        }

        $existingValues = DB::select(
            "SELECT e.enumlabel FROM pg_enum e JOIN pg_type t ON e.enumtypid = t.oid WHERE t.typname = ?", 
            [$type]
        );

        if (!in_array($value, array_column($existingValues, 'enumlabel'))) {
            return response()->json(['error' => 'Value does not exist'], 400);
        }

        try {
            DB::transaction(function () use ($type, $value) {
                $newType = $type . '_new';
                $existingValues = array_column(
                    DB::select("SELECT e.enumlabel FROM pg_enum e JOIN pg_type t ON e.enumtypid = t.oid WHERE t.typname = ?", [$type]), 
                    'enumlabel'
                );

                $newValues = array_filter($existingValues, fn($label) => $label !== $value);
                $newValuesList = implode("', '", $newValues);

                DB::statement("CREATE TYPE $newType AS ENUM ('$newValuesList')");

                $columnsToUpdate = DB::select("SELECT table_name, column_name FROM information_schema.columns WHERE udt_name = ?", [$type]);

                foreach ($columnsToUpdate as $column) {
                    $table = $column->table_name;
                    $col = $column->column_name;

                    DB::statement("ALTER TABLE $table ALTER COLUMN $col TYPE $newType USING $col::text::$newType");
                }

                DB::statement("DROP TYPE $type");

                DB::statement("ALTER TYPE $newType RENAME TO $type");
            });

            return response()->json(['message' => 'Category deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete category: ' . $e->getMessage()], 500);
        }
    }


    public function showReports()
    {
        if (!Gate::allows('admin-only')) {
            abort(403, 'Access denied');
        }

        $reports = Report::with(['user', 'item'])->paginate(10);

        $reportCount = Report::count();

        return view('admin.reports', compact('reports', 'reportCount'));
    }

    public function deleteReport($id)
    {
        if (!Gate::allows('admin-only')) {
            abort(403, 'Access denied');
        }

        $report = Report::find($id);

        if (!$report) {
            return redirect()->route('admin.reports')->with('error', 'Report not found.');
        }

        $report->delete();

        return response()->json(['message' => 'Report deleted successfully.'], 200);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Rate;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProfileController extends Controller
{
    public function showProfile($userid)
    {
        if (!is_numeric($userid)) {
            return back();
        }
    
        $user = User::with('image')->find($userid);
    
        $previousRating = DB::table('rates')
            ->where('raterid', Auth::id())
            ->where('ratedid', $user->userid)
            ->value('rate');
    
        if ($user) {
            return view('user.profile', compact('user', 'previousRating'));
        }
    
        return back();
    }

    public function itemsBought($userId)
    {
        $items = Item::where('state', 'Sold')->where('topbidder', $userId)->paginate(20);

        return view('user.items-bought', compact('userId', 'items'));
    }

    public function userItems(Request $request, $id) 
    {
        $state = $request->query('state');
        $validStates = ['Pending', 'Auction', 'NotSold', 'Sold','Suspended'];
        if (!in_array($state, $validStates)) {
            $state = null;
        }

        $query = Item::where('ownerid', $id);
        if ($state) {
            $query->where('state', $state);
        }
        $items = $query->paginate(20);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('partials.items-list', ['items' => $items])->render(),
            ]);
        }
        $userName = User::findOrFail($id)->firstname;
        return view('user.items', [
            'items' => $items,
            'currentState' => $state,
            'userId' => $id,
            'userName' => $userName,
        ]);
    }

    public function editEmailPassword($userid)
    {
        if (!is_numeric($userid)) {
            return back();
        }
        $user = User::find($userid);
        $this->authorize('editUser', $user);
        if ($user){
            return view('user.edit-email-password', compact('user'));
        }
        return back();
    }

    public function updateEmailPassword(Request $request, $userid)
    {
        $user = User::findOrFail($userid);
        $this->authorize('editUser', $user);
        $validated = $request->validate([
            'email' => 'required|max:100|email|unique:users,email,' . $user->userid . ',userid',
            'password' => 'nullable|confirmed|min:8|max:100',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $user->updateProfile($validated);

        return redirect()->route('profile.show', $userid) ->with('success', 'Sensitive information updated successfully!');

    }

    public function editOtherInfo($userid)
    {
        if (!is_numeric($userid)) {
            return back();
        }
        $user = User::find($userid);
        $this->authorize('editUser', $user);
        if (!$user){
            return back();
        }
        $countries = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");

        return view('user.edit-other-info', compact('user','countries'));
    }

    public function updateOtherInfo(Request $request, $userid)
    {
        $user = User::findOrFail($userid);
        $this->authorize('editUser', $user);
        $validated = $request->validate([
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'address' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postalcode' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'birthdate' => 'required|date',
        ]);
        $birthdate = new \DateTime($request->birthdate);
        $today = new \DateTime();
        $age = $today->diff($birthdate)->y;

        if ($age < 18) {
            return back()->withErrors(['birthdate' => 'You must be at least 18 years old to register.'])->withInput();
        }
        
        $user->updateProfile($validated);

        return redirect()->route('profile.show', $userid)->with('success', 'Profile updated successfully!');
    }
    public function confirmEmailPassword($userid)
    {
        if (!is_numeric($userid)) {
            return back();
        }
        $user = User::find($userid);
        $this->authorize('editUser', $user);
        if ($user){
            return view('user.confirm-email-password', compact('user'));
        }
        return back();
    }

    public function validateEmailPassword(Request $request, $userid)
    {
        $user = User::findOrFail($userid);
        $this->authorize('editUser', $user);
        $validated = $request->validate([
            'email' => 'required|email|max:100|unique:users,email,' . $userid . ',userid', 
            'password' => 'required|max:100', 
        ]);

        if (Hash::check($request->password, $user->password) && $user->email === $request->email) {
            return redirect()->route('profile.edit.email-password', $user->userid);
        }

        return back()->withErrors(['email' => 'Invalid email or password. Please try again.']);
    }
    
    public function updatePicture(Request $request, $userid)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        $user = User::findOrFail($userid);
        $this->authorize('editUser', $user);
        if ($request->hasFile('profile_picture')) {
            $imagePath = $request->file('profile_picture')->store('profile_pictures', 'public');
    
            $image = $user->image()->updateOrCreate([], ['imageurl' => $imagePath]);
    
            $user->update(['imageid' => $image->imageid]);
    
            return response()->json([
                'success' => true,
                'newImageUrl' => Storage::url($imagePath)
            ]);
        }
    
        return response()->json([
            'success' => false,
            'error' => 'No image file uploaded or invalid file type.'
        ]);
    }

    public function editOptions($userid)
    {
        $user = User::findOrFail($userid);
        $this->authorize('editUser', $user);
        return view('user.edit-options', compact('user'));
    }
    public function rate(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $this->authorize('rate', $user);
        $request->validate([
            'rate' => 'required|numeric|min:1|max:5',
        ]);

        if (auth()->id() == $userId) {
            return back()->with('error', 'You cannot rate your own profile.');
        }

        $raterId = auth()->id();

        Rate::saveOrUpdate($raterId, $userId, $request->rate);

        return back()->with('success', 'Rating submitted successfully.');
    }

    

    public function showAverageRating($userid)
    {
        $user = User::findOrFail($userid);
        $averageRating = $user->averageRating(); 

        return response()->json([
            'user' => $user,
            'average_rating' => $averageRating,
        ]);
    }

    public function destroy($userid)
    {
        $user = User::findOrFail($userid);
        $this->authorize('deleteOwnAccount', $user);
        try {
            $user->delete();

            return redirect()->route('main')->with('success', 'Your account has been successfully deleted.');
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', 'Failed to delete your account. Please try again later.');
        }
    }
}
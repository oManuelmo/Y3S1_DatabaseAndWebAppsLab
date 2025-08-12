<article class="user" @if($user->isadmin) style="background-color: #FFEEB5;" @endif>
    <a href="{{ route('profile.show', ['userid' => $user->userid]) }}" class="item-info">
        <p style="text-transform:none !important">E-mail: {{ $user->email }}</p>
        <p>Name: {{ $user->firstname }} {{ $user->lastname }}</p>
        <p class="check-banned">Banned: {{ $user->isBanned() ? 'Yes (Until ' . $user->bantime . ')' : 'No' }}</p>
        <p>Admin: {{ $user->isadmin ? 'Yes' : 'No' }}</p>
    <a>
    <section class="admin-user-buttons">
        <a href="{{ route('admin.edit-user', $user->userid) }}" 
            class="button" 
            @if(Auth::id() === $user->userid || $user->userid == 0) style="pointer-events: none;" disabled @endif>
            Edit
         </a>
        @if ($user->isBanned())
        <button class="button unban-user-button" data-user-id="{{ $user->userid }}">Unban</button>
        @else
        <form data-user-id="{{ $user->userid }}" class="ban-user-form" >
            @csrf
            <select class="ban-duration" name="ban_duration" required @if(Auth::id() === $user->userid || $user->userid == 0) disabled @endif>
                <option value="1 hour">1 Hour</option>
                <option value="1 day">1 Day</option>
                <option value="1 week">1 Week</option>
                <option value="1 month">1 Month</option>
            </select>
            <button style="font-size: 15px !important;" type="button" class="button ban-user-button" @if(Auth::id() === $user->userid || $user->userid == 0) disabled @endif>Ban</button>
        </form>
        @endif
        <form action="{{ route('admin.users.delete', $user->userid) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button style="font-size: 15px !important;" type="submit" class="button delete-user-button" data-user-id="{{$user->userid}}" @if(Auth::id() === $user->userid || $user->userid == 0) disabled @endif onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
        </form>
    </section>
</article>

<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\{
    ItemController,
    BidController,
    NotificationController,
    Auth\LoginController,
    Auth\RegisterController,
    AdminController,
    ProfileController,
    AboutUsController,
    FeaturesController,
    FollowController,
    ContactsController,
    SearchController,
    MainController,
    TransactionController,
    Auth\ResetPasswordController,
    Auth\ForgotPasswordController,
    ChatController,
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*
|--------------------------------------------------------------------------
| Home Route
|--------------------------------------------------------------------------
*/
Route::redirect('/', '/main');

Route::controller(MainController::class)->group(function () {
    Route::get('/main', 'showMainPage')->name('main');
});

/*
|--------------------------------------------------------------------------
| Pusher Route
|--------------------------------------------------------------------------
*/
Route::post('/pusher/auth', function (Illuminate\Http\Request $request) {
    return Broadcast::auth($request);
});

/*
|--------------------------------------------------------------------------
| Item Routes
|--------------------------------------------------------------------------
*/
Route::controller(ItemController::class)->prefix('item')->name('item.')->group(function () {
    Route::middleware(['auth'])->get('/create', 'createForm')->name('create.form');
    Route::middleware(['auth'])->post('/create', 'create')->name('create');
    Route::get('/{id}', 'show')->name('show');
    Route::middleware(['auth', 'auction.not.ended'])->delete('/delete/{item}', 'delete')->name('delete');
    Route::middleware(['auth', 'auction.not.ended'])->post('/edit/{item}', 'editItem')->name('edit');
    Route::middleware(['auth', 'auction.not.ended'])->put('/update/{item}', 'updateItem')->name('update');
    Route::get('/bid-history/{id}', 'seeBidHistory')->name('bids.history');
    Route::middleware(['auth', 'item.not.suspended', 'auction.not.ended'])->post('/place-bid/{id}', 'bidItem')->name('bidItem');
    Route::middleware(['auth'])->post('/upload-image', 'uploadImage')->name('upload.image');
    Route::post('/place-bid/{id}', 'bidItem')->name('bidItem');
    Route::post('/upload-image', 'uploadImage')->name('upload.image');
    Route::post('/report',  'report')->name('report');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'authenticate');
    Route::get('/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Protected by admin middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Admin Dashboard
    Route::redirect('', '/admin/dashboard');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Admin User Management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'showCreateUserForm'])->name('users.create');
    Route::post('/users', [AdminController::class, 'createUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('edit-user');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
    Route::post('/users/{userid}/ban', [AdminController::class, 'banUser'])->name('users.ban');
    Route::post('/users/{userid}/unban', [AdminController::class, 'unbanUser'])->name('users.unban');
    Route::get('/users/search', [AdminController::class, 'searchUsers'])->name('users.search');

    // Admin Item Management
    Route::get('/items', [AdminController::class, 'items'])->name('items');
    Route::middleware(['auction.not.ended'])->get('/items/{item}/edit', [AdminController::class, 'editItem'])->name('items.edit');
    Route::middleware(['auction.not.ended'])->put('/items/{item}', [AdminController::class, 'updateItem'])->name('items.update');
    Route::post('/items/{itemId}/suspend', [AdminController::class, 'suspendAuction'])->name('items.suspend');
    Route::post('/items/{itemId}/unsuspend', [AdminController::class, 'unsuspendItem'])->name('items.unsuspend');
    Route::middleware(['auction.not.ended'])->delete('/items/{item}', [AdminController::class, 'deleteItem'])->name('items.delete');
    Route::get('/items/pending', [AdminController::class, 'pendingItems'])->name('items.pending');
    Route::post('/items/accept/{itemId}', [AdminController::class, 'acceptItem'])->name('items.accept');

    // Admin Category Management
    Route::get('/categories/{type}', [AdminController::class, 'showCategories'])->name('categories');
    Route::post('/categories/{type}/add', [AdminController::class, 'addCategory'])->name('categories.add');
    Route::post('/categories/{type}/delete', [AdminController::class, 'deleteCategory'])->name('categories.delete');

    // Admin Report Management
    Route::get('/reports', [AdminController::class, 'showReports'])->name('reports');
    Route::delete('/reports/{id}', [AdminController::class, 'deleteReport'])->name('reports.delete');

    //Admin chat
    Route::get('chats', [ChatController::class, 'getChatsForAdmin'])->name('chats');
    Route::get('chat/{chatId}', [ChatController::class, 'viewChatForAdmin'])->name('chat.view');

});

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/
Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
    Route::get('/{userid}', 'showProfile')->name('show');
    Route::middleware(['auth'])->get('/edit-email-password/{userid}', 'editEmailPassword')->name('edit.email-password');
    Route::middleware(['auth'])->put('/edit-email-password/{userid}', 'updateEmailPassword')->name('update.email-password');
    Route::middleware(['auth'])->get('/confirm-email-password/{userid}', 'confirmEmailPassword')->name('confirm.email-password');
    Route::middleware(['auth'])->post('/confirm-email-password/{userid}', 'validateEmailPassword')->name('validate.email-password');
    Route::middleware(['auth'])->get('/{userid}/edit/other-info', 'editOtherInfo')->name('edit.other-info');
    Route::middleware(['auth'])->put('/{userid}/update/other-info', 'updateOtherInfo')->name('update.other-info');
    Route::middleware(['auth'])->get('/{userid}/edit', 'editProfile')->name('edit');
    Route::middleware(['auth'])->put('/{userid}/update', 'updateProfile')->name('update');
    Route::middleware(['auth'])->get('/{userid}/edit-options', 'editOptions')->name('edit.options');
    Route::middleware(['auth'])->post('/{userid}/update-picture', 'updatePicture')->name('update.picture');
    Route::middleware(['auth'])->delete('/{userid}/delete', 'destroy')->name('delete');
    Route::get('/items/bought/{id}', 'itemsBought')->name('items.bought');
    Route::get('/items/{id}', 'userItems')->name('user.items');

    //rate

    Route::get('user/{userid}/average-rating', 'showAverageRating')->name('averageRating');
    Route::middleware(['client'])->post('{userid}/rate', 'rate')->name('rate');
});

/*
|--------------------------------------------------------------------------
| Transaction Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['client'])->controller(TransactionController::class)->group(function () {
    Route::get('/deposit', 'showDepositForm')->name('deposit.form');
    Route::post('/deposit', 'createDeposit')->name('deposit.create');
    Route::get('/withdraw', 'showWithdrawForm')->name('withdraw.form');
    Route::post('/withdraw', 'createWithdraw')->name('withdraw.create');
    Route::get('/transactions', 'showTransactions')->name('transactions.show');
});

/*
|-------------------------------------------------------------------------- 
| Chat Routes
|-------------------------------------------------------------------------- 
*/
Route::middleware(['auth'])->prefix('chat')->name('chat.')->group(function () {
    Route::middleware(['client'])->post('create', [ChatController::class, 'createChat'])->name('create');
    Route::middleware(['client'])->get('{chatId}', [ChatController::class, 'viewChat'])->name('view');
    Route::post('{chatid}/close', [ChatController::class, 'closeChat'])->name('close');
    Route::post('{chatid}/send', [ChatController::class, 'sendMessage'])->name('send');
    Route::get('check-chat-status/{chatid}', [ChatController::class, 'checkChatStatus']);
});


/*
|--------------------------------------------------------------------------
| Follow Routes
|--------------------------------------------------------------------------
*/
Route::controller(FollowController::class)->group(function () {
    Route::get('/followed-items/{id}', 'showFollowedItems')->name('followed-items');
    Route::middleware(['client'])->post('/follow/toggle-follow', 'toggleFollow')->name('toggle-follow');
});

/*
|--------------------------------------------------------------------------
| About, Features, and Contacts Routes
|--------------------------------------------------------------------------
*/
Route::get('/about', [AboutUsController::class, 'index'])->name('about');
Route::get('/features', [FeaturesController::class, 'index'])->name('features');
Route::get('/contacts', [ContactsController::class, 'index'])->name('contacts');

/*
|--------------------------------------------------------------------------
| Search Routes
|--------------------------------------------------------------------------
*/
Route::get('/search', [SearchController::class, 'index'])->name('search.index');

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::get('/api/next-upcoming-items', [MainController::class, 'fetchNextUpcomingItems']);

/*
|--------------------------------------------------------------------------
| Bid Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['client'])->post('/bids/{itemid}', [BidController::class, 'store'])->name('bids.store');


/*
|--------------------------------------------------------------------------
| Password Reset Routes
|--------------------------------------------------------------------------
*/
Route::controller(ForgotPasswordController::class)->group(function () {
    Route::get('/forgot-password', 'showForgotPasswordForm')->name('password.request');
    Route::post('/forgot-password', 'sendResetCode')->name('password.email');
    Route::get('/reset-code', 'showVerifyCodeForm')->name('password.verify');
    Route::post('/reset-code', 'verifyResetCode');
});

Route::controller(ResetPasswordController::class)->group(function () {
    Route::get('/reset-password',  'showResetPasswordForm')->name('password.reset');
    Route::post('/reset-password',  'resetPassword');
});
/*
|--------------------------------------------------------------------------
| Notifications Routes
|--------------------------------------------------------------------------
*/
Route::controller(NotificationController::class)->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/{userid}', 'showNotifications')->name('show');
});



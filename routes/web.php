<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerPanelController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\AuthController;


use App\Http\Controllers\DeveloperPanelController;
use App\Http\Controllers\SuperAdminPanelController;
use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\ResellerPanelController;
use App\Http\Controllers\SubResellerPanelController;
use App\Http\Controllers\ManagerPanelController;
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

Route::get('/', function () {
    // For the demo, let's point to the welcome page with WebAuthn
    return view('welcome');
});


// Account routes
Route::resource('accounts', AccountController::class);

// Authentication routes
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [AuthController::class, 'register']);


/*
|--------------------------------------------------------------------------
| Customer Panel Routes
|--------------------------------------------------------------------------
|
| Routes for the customer self-service panel.
|
*/
Route::middleware('auth')->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerPanelController::class, 'dashboard'])->name('dashboard');
    Route::get('/bills', [CustomerPanelController::class, 'bills'])->name('bills');
    Route::get('/payments', [CustomerPanelController::class, 'payments'])->name('payments');
});

/*
|--------------------------------------------------------------------------
| Developer Panel Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'acl:developer'])->prefix('developer')->name('developer.')->group(function () {
    Route::get('/dashboard', [DeveloperPanelController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Super Admin Panel Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminPanelController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminPanelController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Reseller Panel Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('reseller')->name('reseller.')->group(function () {
    Route::get('/dashboard', [ResellerPanelController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Sub-reseller Panel Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('sub-reseller')->name('sub-reseller.')->group(function () {
    Route::get('/dashboard', [SubResellerPanelController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Manager Panel Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [ManagerPanelController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Card Distributors Panel Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('card-distributors')->name('card-distributors.')->group(function () {
    //
});



// Keep the demo routes for now
Route::prefix('demo')->group(function () {
    Route::get('/1', function () { return view('pages.demo1.index'); });
    Route::get('/2', function () { return view('pages.demo2.index'); });
    Route::get('/3', function () { return view('pages.demo3.index'); });
    Route::get('/4', function () { return view('pages.demo4.index'); });
    Route::get('/5', function () { return view('pages.demo5.index'); });
    Route::get('/6', function () { return view('pages.demo6.index'); });
    Route::get('/7', function () { return view('pages.demo7.index'); });
    Route::get('/8', function () { return view('pages.demo8.index'); });
    Route::get('/9', function () { return view('pages.demo9.index'); })->name('demo9.index');
    Route::get('/9/profile', function () { return view('pages.demo9.profile'); })->name('demo9.profile');
    Route::get('/10', function () { return view('pages.demo10.index'); });
});
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', fn() => redirect()->route('admin.login'));

Route::get('/test-mail', function(){
    // Send an email
    Mail::send(
        'mail.test',
        [],
        function ($m) {
            $m->from('admin@vterminal.ng');

            $m->to('dayoolapeju@gmail.com', "Jeremiah Ekundayo")
                ->subject('Testing vTerminal');
        }
    );
});

Route::prefix('admin')->group(function() {
    Route::get('/login', [AuthController::class, 'getLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'postLogin'])->name('admin.postlogin');
});

Route::prefix('admin')->middleware('auth:sanctum')->group(function(){
    Route::get('/logout', [AuthController::class, 'logout'])->name('admin.logout');
    Route::get('/dashboard', [DashboardController::class, 'displayDashboard'])->name('admin.dashboard');

    // Users
    Route::get('/users', [DashboardController::class, 'getUsers'])->name('admin.users');
    Route::get('/users/{user}', [DashboardController::class, 'getUserDetails'])->name('admin.userdetails');
    Route::get('/users/{user}/status', [DashboardController::class, 'changeUserStatus'])->name('admin.userstatus');

    // Customers
    Route::get('/customers', [DashboardController::class, 'getCustomers'])->name('admin.customers');

    // Merchants
    Route::get('/merchants', [DashboardController::class, 'getMerchants'])->name('admin.merchants');
    Route::get('/merchant_address', [DashboardController::class, 'getImage'])->name('admin.merchant_address');
    Route::get('/verifies/{id}', [DashboardController::class,'verify'])->name('admin.verifies');


});

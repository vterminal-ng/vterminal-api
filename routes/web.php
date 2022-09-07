<?php

use Illuminate\Support\Facades\Route;
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

Route::prefix('admin')->group(function() {
    Route::get('/login', [AuthController::class, 'getLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'postLogin'])->name('admin.postlogin');
});

Route::prefix('admin')->middleware('auth:sanctum')->group(function(){
    Route::get('/logout', [AuthController::class, 'logout'])->name('admin.logout');
    Route::get('/dashboard', [DashboardController::class, 'displayDashboard'])->name('admin.dashboard');
});
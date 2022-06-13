<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\MerchantDetailController;
use App\Http\Controllers\API\UserDetailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// AUTHENTICATED ROUTES
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);

    //Routes to User details CRUD functions
    Route::post('users/user-details', [UserDetailController::class, 'create']);
    Route::get('users/user-details', [UserDetailController::class, 'read']);
    Route::patch('users/user-details', [UserDetailController::class, 'update']);
    Route::put('users/user-details', [UserDetailController::class, 'update']);

    // CRUD functions routes for Merchant Details
    Route::post('users/merchant-details', [MerchantDetailController::class, 'create']);
    Route::get('users/merchant-details', [MerchantDetailController::class, 'read']);
    Route::patch('users/merchant-details', [MerchantDetailController::class, 'update']);
    Route::patch('users/merchant-details', [MerchantDetailController::class, 'update']);
});

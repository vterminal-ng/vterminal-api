<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\MeController;
use App\Http\Controllers\API\MerchantDetailController;
use App\Http\Controllers\API\OtpController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\ResetPasswordController;
use App\Http\Controllers\API\UserDetailController;
use App\Http\Controllers\API\BankDetailController;
use App\Http\Controllers\API\VerificationController;
use App\Http\Controllers\WalletController;
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

// PUBLIC ROUTES
Route::get('me', [MeController::class, 'getMe']);
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('login/i', [AuthController::class, 'emailLogin']);
Route::post('logout', [AuthController::class, 'logout']);

Route::post('password/email', [ResetPasswordController::class, 'sendResetOtpEmail']);
Route::post('password/reset', [ResetPasswordController::class, 'reset']);

// AUTHENTICATED ROUTES
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('phone/otp/send', [OtpController::class, 'sendSmsOtp']);
    Route::post('phone/otp/verify', [OtpController::class, 'verifySmsOtp']);

    Route::post('email/otp/send', [OtpController::class, 'sendEmailOtp']);
    Route::post('email/otp/verify', [OtpController::class, 'verifyEmailOtp']);

    //Update password
    Route::post('users/password-update', [ProfileController::class, 'changePassword']);
    //Update Email
    Route::patch('users/email/update', [ProfileController::class, 'updateEmail']);
    //Routes to User details CRUD functions
    Route::post('users/user-details', [UserDetailController::class, 'create']);
    Route::get('users/user-details', [UserDetailController::class, 'read']);
    Route::patch('users/user-details', [UserDetailController::class, 'update']);
    Route::put('users/user-details', [UserDetailController::class, 'update']);

    Route::group(['middleware' => ['merchant.user']], function () {
        // CRUD functions routes for Merchant Details
        Route::post('users/merchant-details', [MerchantDetailController::class, 'create']);
        Route::get('users/merchant-details', [MerchantDetailController::class, 'read']);
        Route::patch('users/merchant-details', [MerchantDetailController::class, 'update']);
        Route::put('users/merchant-details', [MerchantDetailController::class, 'update']);
    });

    Route::get('users/bank-details', [BankDetailController::class, 'getBankDetail']);
    Route::post('users/bank-details', [BankDetailController::class, 'create']);
    Route::patch('users/bank-details/{bankDetail}', [BankDetailController::class, 'updateBankDetail']);
    Route::delete('users/bank-details/{bankDetail}', [BankDetailController::class, 'deleteBankDetail']);

    Route::post('users/verify-identity', [VerificationController::class, 'verifyBvn']);

    // Wallet
    Route::post('my-wallet/deposit', [WalletController::class, 'deposit']);
    Route::post('my-wallet/withdraw', [WalletController::class, 'withdraw']);
    Route::post('my-wallet/transfer', [WalletController::class, 'verifyBvn']);
});

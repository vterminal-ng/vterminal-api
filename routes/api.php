<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AuthorizedCardController;
use App\Http\Controllers\API\MeController;
use App\Http\Controllers\API\MerchantDetailController;
use App\Http\Controllers\API\OtpController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\ResetPasswordController;
use App\Http\Controllers\API\UserDetailController;
use App\Http\Controllers\API\BankDetailController;
use App\Http\Controllers\API\CodeController;
use App\Http\Controllers\API\PinController;
use App\Http\Controllers\API\StateController;
use App\Http\Controllers\API\VerificationController;
use App\Http\Controllers\API\WalletController;
use App\Http\Controllers\API\SupportTicketController;
use App\Http\Controllers\Paystack\WebhookController;
use App\Models\BankDetail;
use App\Models\Pin;
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
Route::post('login/email', [AuthController::class, 'emailLogin']);
Route::get('states', [StateController::class, 'getStates']);

Route::post('password/email', [ResetPasswordController::class, 'sendResetOtpEmail']);
Route::post('password/reset', [ResetPasswordController::class, 'reset']);

Route::get('/bank-codes', [BankDetailController::class, 'getBanks']);
Route::post('users/nuban-details/', [BankDetailController::class, 'getAccountDetails']);

Route::post('webhook', [WebhookController::class, 'webhook'])->middleware('log.context');

// AUTHENTICATED ROUTES
Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('logout', [AuthController::class, 'logout']);

    Route::post('phone/otp/send', [OtpController::class, 'sendSmsOtp']);
    Route::post('phone/otp/verify', [OtpController::class, 'verifySmsOtp']);

    //Update password
    Route::post('users/password-update', [ProfileController::class, 'changePassword']);

    Route::group(['middleware' => ['verified.phone']], function () {

        // routes that needs user to be merchant
        Route::group(['middleware' => ['merchant.user']], function () {
            // CRUD functions routes for Merchant Details
            Route::post('users/merchant-details', [MerchantDetailController::class, 'create']);
            Route::get('users/merchant-details', [MerchantDetailController::class, 'read']);
            Route::patch('users/merchant-details', [MerchantDetailController::class, 'update']);
            Route::put('users/merchant-details', [MerchantDetailController::class, 'update']);
        });

        // Add or Update Email
        Route::patch('users/email/update', [ProfileController::class, 'updateEmail']);
        Route::post('email/otp/send', [OtpController::class, 'sendEmailOtp']);
        Route::post('email/otp/verify', [OtpController::class, 'verifyEmailOtp']);



        //Routes to User details CRUD functions
        Route::post('users/user-details', [UserDetailController::class, 'create']);
        Route::get('users/user-details', [UserDetailController::class, 'read']);
        Route::patch('users/user-details', [UserDetailController::class, 'update']);
        Route::put('users/user-details', [UserDetailController::class, 'update']);

        Route::post('users/avatar', [UserDetailController::class, 'uploadAvatar']);

        // Wallet
        Route::get('my-wallet/transactions', [WalletController::class, 'getTransactions']);

        // Card
        Route::get('cards/my-card', [AuthorizedCardController::class, 'getCard']);

        // Codes
        Route::get('code', [CodeController::class, 'customerTransactionCodes']);
        Route::get('code/{codeReference}', [CodeController::class, 'customerTransactionCode']);

        // Bank Details
        Route::get('users/bank-details', [BankDetailController::class, 'getBankDetail']);

        // routes that need your email to be verified first
        Route::group(['middleware' => ['verified.email']], function () {
            // Create Transaction Pin
            Route::post('users/create-pin', [PinController::class, 'create']);
            Route::post('users/update-transaction-pin', [PinController::class, 'update']);

            // Bank Details
            Route::post('users/bank-details', [BankDetailController::class, 'create']);
            Route::delete('users/bank-details', [BankDetailController::class, 'deleteBankDetail']);

            Route::post('users/verify-identity', [VerificationController::class, 'verifyDetails']);
            Route::post('users/verify-bvn-with-nuban', [UserDetailController::class, 'verifyBvn']);

            // Wallet
            Route::post('my-wallet/deposit', [WalletController::class, 'deposit']);
            Route::post('my-wallet/withdraw', [WalletController::class, 'withdraw']);

            //Card
            Route::post('cards/add', [AuthorizedCardController::class, 'add']);
            Route::delete('cards/delete', [AuthorizedCardController::class, 'delete']);

            // Routes that require entire profile to be verified before usage
            Route::group(['middleware' => ['profile.verified']], function () {
                // Code
                Route::post('code/generate', [CodeController::class, 'generateCode']);
                Route::post('code/transaction/summary', [CodeController::class, 'transactionSummary']);
                Route::post('code/transaction/activate', [CodeController::class, 'activateCode']);
                Route::post('code/transaction/activate-with-saved-card', [CodeController::class, 'activateCodeWithSavedCard']);
                Route::post('code/transaction/cancel', [CodeController::class, 'cancelCode']);

                // routes that needs user to be merchant
                Route::group(['middleware' => ['merchant.user']], function () {
                    Route::post('code/summary', [CodeController::class, 'codeSummary']);
                    Route::post('code/resolve', [CodeController::class, 'resolveCode']);
                });
            });

            // Dispute Transaction, Create Support Ticket
            Route::post('users/create-ticket/', [SupportTicketController::class, 'createTicket']); //Transaction param to be inncluded later

            // NUBAN Endpoints

        });
    });
});

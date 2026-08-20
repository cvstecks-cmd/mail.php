<?php

use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\SwapController;
use App\Http\Controllers\User\BotController;
use App\Http\Controllers\User\CardController;
use App\Http\Controllers\User\SettingsController;
use App\Http\Controllers\User\CryptoController;
use App\Http\Controllers\User\ReferralController;
use App\Http\Controllers\User\BuyController;
use App\Http\Controllers\User\ReceiveController;
use App\Http\Controllers\User\SendController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\KycController;
use App\Http\Controllers\User\WalletConnectController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Livewire\Auth\PasscodePage;
use App\Livewire\Auth\PasscodeSetup;
use App\Livewire\User\Dashboard;
use App\Http\Middleware\VerifyPasscode;
use App\Http\Middleware\RequireEmailVerification;

/*
|--------------------------------------------------------------------------
| Passcode Routes
|--------------------------------------------------------------------------
|
| These routes MUST NOT use VerifyPasscode because they are the
| destination used when passcode verification is required.
|
*/

Route::post(
    '/bots/load-code',
    [BotController::class, 'loadCode']
)->name('bots.load-code');

Route::middleware(['auth', RequireEmailVerification::class])->group(function () {

    Route::get(
        '/passcode',
        PasscodePage::class
    )->name('passcode.show');

    Route::get(
        '/passcode/setup',
        PasscodeSetup::class
    )->name('passcode.setup');

});

Route::middleware([
    'auth',
    RequireEmailVerification::class,
    VerifyPasscode::class
])->group(function () {

    Route::get(
        '/dashboard',
        Dashboard::class
    )->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Referral
    |--------------------------------------------------------------------------
    */

    Route::prefix('referral')->group(
        function () {

            Route::get(
                '/',
                [ReferralController::class, 'index']
            )->name('referral.index');

            Route::get(
                '/stats',
                [ReferralController::class, 'stats']
            )->name('referral.stats');
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');


    /*
    |--------------------------------------------------------------------------
    | Wallet Connect
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/wallet-connect',
        [WalletConnectController::class, 'index']
    )->name('wallet.connect');

    Route::post(
        '/wallet-connect/connect',
        [WalletConnectController::class, 'connect']
    )->name('wallet.connect.process');

    Route::post(
        '/wallet-connect/disconnect',
        [WalletConnectController::class, 'disconnect']
    )->name('wallet.disconnect');


    /*
    |--------------------------------------------------------------------------
    | Swap
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/swap',
        [SwapController::class, 'index']
    )->name('swap');

    Route::post(
        '/swap/process',
        [SwapController::class, 'swap']
    )->name('swap.process');


    /*
    |--------------------------------------------------------------------------
    | Other existing user routes
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/deposit',
        function () {
            return view('user.fund');
        }
    )->name('deposit');

    Route::get(
        '/withdraw',
        function () {
            return redirect()->route('dashboard');
        }
    )->name('withdraw');

    Route::get(
        '/settings',
        [SettingsController::class, 'index']
    )->name('settings');

    Route::get(
        '/discover',
        function () {
            return view('user.discover');
        }
    )->name('discover');


    /*
    |--------------------------------------------------------------------------
    | Crypto
    |--------------------------------------------------------------------------
    */

    Route::prefix('crypto')->group(
        function () {

            Route::get(
                '/details/{symbol}/{network?}',
                [CryptoController::class, 'details']
            )->name('crypto.details');

            Route::get(
                '/manage',
                [CryptoController::class, 'manage']
            )->name('crypto.manage');

            Route::post(
                '/crypto/update',
                [CryptoController::class, 'updateManage']
            )->name('crypto.manage.update');

            Route::get(
                '/address',
                [CryptoController::class, 'address']
            )->name('crypto.address');
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Buy
    |--------------------------------------------------------------------------
    */

    Route::prefix('buy')->group(
        function () {

            Route::get(
                '/',
                [BuyController::class, 'index']
            )->name('buy.index');

            Route::get(
                '/details/{symbol}/{network?}',
                [BuyController::class, 'details']
            )->name('buy.details');
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Card
    |--------------------------------------------------------------------------
    */

    Route::prefix('card')->group(
        function () {

            Route::get(
                '/',
                [CardController::class, 'index']
            )->name('card.index');

            Route::post(
                '/request',
                [CardController::class, 'request']
            )->name('card.request');

            Route::get(
                '/add-money',
                [CardController::class, 'addMoney']
            )->name('card.add-money');

            Route::post(
                '/fund',
                [CardController::class, 'fund']
            )->name('card.fund');

            Route::post(
                '/{card}/freeze',
                [CardController::class, 'freeze']
            )->name('card.freeze');

            Route::post(
                '/{card}/unfreeze',
                [CardController::class, 'unfreeze']
            )->name('card.unfreeze');

            Route::delete(
                '/{card}',
                [CardController::class, 'delete']
            )->name('card.delete');
        }
    );


    /*
    |--------------------------------------------------------------------------
    | TRADING BOTS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/bots',
        [BotController::class, 'index']
    )->name('bots');


    /*
     * Your existing show route does not take a {bot}
     * parameter. Keep it this way because the current
     * form submits bot_type instead.
     */
    Route::get(
        '/bots/show/{bot}',
        [BotController::class, 'show']
    )->name('bots.show');


    Route::post(
        '/bots/subscribe',
        [BotController::class, 'subscribe']
    )->name('bots.subscribe');


    Route::get(
        '/bots/history',
        [BotController::class, 'history']
    )->name('bots.history');


    Route::get(
        '/bots/trades/update',
        [BotController::class, 'updateTrades']
    )->name('bots.trades.update');


    /*
    |--------------------------------------------------------------------------
    | SIMULATED LIVE TRADING
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/bots/subscriptions/{subscription}/simulation',
        [BotController::class, 'simulation']
    )->name('bots.simulation');
    
    Route::post(
    '/bots/subscriptions/{subscription}/terminate',
    [BotController::class, 'terminate']
)->name('bots.terminate');


    Route::get(
        '/bots/subscriptions/{subscription}/profits',
        [BotController::class, 'profits']
    )->name('bots.profits');


    /*
    |--------------------------------------------------------------------------
    | Send
    |--------------------------------------------------------------------------
    */

    Route::prefix('send')
        ->middleware(['auth'])
        ->group(
            function () {

                Route::get(
                    '/payid',
                    [SendController::class, 'payidAssets']
                )->name('send.payid');

                Route::get(
                    '/payid/{symbol}/{network?}',
                    [SendController::class, 'payidDetails']
                )->name('send.payid.details');

                Route::post(
                    '/payid/verify',
                    [SendController::class, 'verifyPayId']
                )->name('send.payid.verify');

                Route::post(
                    '/send/payid/process/{symbol}/{network?}',
                    [SendController::class, 'processPayId']
                )->name('send.payid.process');

                Route::get(
                    '/payid/{symbol}/{network?}/success/{transaction}',
                    [SendController::class, 'success']
                )->name('send.payid.success');

                Route::get(
                    '/payid/{symbol}/{network?}/failed',
                    [SendController::class, 'failed']
                )->name('send.payid.failed');


                Route::get(
                    '/external',
                    [SendController::class, 'externalAssets']
                )->name('send.external');

                Route::get(
                    '/external/{symbol}/{network?}',
                    [SendController::class, 'externalDetails']
                )->name('send.external.details');

                Route::post(
                    '/external/verify',
                    [SendController::class, 'verifyExternal']
                )->name('send.external.verify');

                Route::post(
                    '/send/external/process/{symbol}/{network?}',
                    [SendController::class, 'processExternal']
                )->name('send.external.process');

                Route::get(
                    '/external/{symbol}/{network?}/success/{transaction}',
                    [SendController::class, 'externalSuccess']
                )->name('send.external.success');

                Route::get(
                    '/external/{symbol}/{network?}/failed',
                    [SendController::class, 'externalFailed']
                )->name('send.external.failed');
            }
        );


    /*
    |--------------------------------------------------------------------------
    | Receive
    |--------------------------------------------------------------------------
    */

    Route::prefix('receive')->group(
        function () {

            Route::get(
                '/payid',
                [ReceiveController::class, 'payidAssets']
            )->name('receive.payid');

            Route::get(
                '/payid/{symbol}/{network?}',
                [ReceiveController::class, 'payidDetails']
            )->name('receive.payid.details');

            Route::get(
                '/external',
                [ReceiveController::class, 'externalAssets']
            )->name('receive.external');

            Route::get(
                '/external/{symbol}/{network?}',
                [ReceiveController::class, 'externalDetails']
            )->name('receive.external.details');
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/notifications',
        [NotificationController::class, 'index']
    )->name('notifications');

    Route::post(
        '/notifications/{id}/read',
        [NotificationController::class, 'markAsRead']
    );

    Route::delete(
        '/notifications/{id}',
        [NotificationController::class, 'destroy']
    );

    Route::post(
        '/notifications/mark-all-read',
        function () {
            auth()
                ->user()
                ->notifications()
                ->update([
                    'is_read' => true
                ]);

            return response()->json([
                'success' => true
            ]);
        }
    );

    Route::get(
        '/notifications/unread-count',
        [NotificationController::class, 'unreadCount']
    );


    /*
    |--------------------------------------------------------------------------
    | KYC
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/kyc-verification',
        [KycController::class, 'index']
    )->name('kyc');

    Route::post(
        '/kyc/upload',
        [KycController::class, 'uploadDocuments']
    )->name('user.kyc.upload');

    Route::get(
        '/kyc/status',
        [KycController::class, 'getDocumentStatus']
    )->name('user.kyc.status');
});

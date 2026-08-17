<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use App\Models\BotSubscription;
use App\Models\Cryptocurrency;
use App\Models\UserCryptoBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::query()->regular();

        // Apply KYC status filter
        if ($request->filled('kyc_status')) {
            $query->where('kyc_status', $request->kyc_status);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('uuid', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()
                      ->withCount('cards')
                      ->paginate(10)
                      ->withQueryString();

        $cryptocurrencies = \App\Models\Cryptocurrency::active()->ordered()->get();

        return view('admin.users.index', compact('users', 'cryptocurrencies'));
    }

    /**
     * Show create user form
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a new user
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users', 
            ],
            
            'password' => [
                'required',
                Password::defaults(), 
            ],
            
            'passcode' => [
                'required',
                'digits:6', 
            ],
            
            'require_kyc' => [
                'sometimes', 
                'boolean',   
            ],
            
            'require_fee' => [
                'sometimes', 
                'boolean',   
            ],
            
            'require_wallet_connect' => [
                'sometimes', 
                'boolean',   
            ],
            
            'enable_swap' => [
                'sometimes', 
                'boolean',   
            ],
        ]);

        
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']), 
            'passcode' => Hash::make($validated['passcode']), 
            'require_kyc' => $validated['require_kyc'] ?? true,
            'require_fee' => $validated['require_fee'] ?? false,
            'require_wallet_connect' => $validated['require_wallet_connect'] ?? false,
            'enable_swap' => $validated['enable_swap'] ?? true,
        ]);
        
        $user->update([
            'email_verified_at' => now(),
        ]);

        // Crypto balances are automatically created by UserObserver

        return redirect()
            ->route('admin.users')
            ->with('status', 'User created successfully.');
    }

    /**
     * Show user details
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show edit user form
     */
   public function update(Request $request, User $user)
{
    // Validate the incoming request data
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        'uuid' => ['required', 'string', 'max:255', 'unique:users,uuid,' . $user->id],
        'kyc_status' => ['required', 'in:not_submitted,pending,approved,rejected'],
        'require_kyc' => ['boolean'],
        'enable_crypto_sending' => ['boolean'],
        'require_fee' => ['boolean'],
        'require_wallet_connect' => ['boolean'],
        'enable_swap' => ['boolean'],
        'email_verified' => ['boolean'],
        'kyc_fee' => ['boolean'],
        'kyc_fee_amount' => ['nullable', 'numeric', 'min:0'],
        'swap_fee' => ['boolean'],
        'passcode' => ['nullable', 'digits:6'],
    ]);
    
    $data = [
        'name' => $validated['name'],
        'email' => $validated['email'],
        'uuid' => $validated['uuid'],
        'kyc_status' => $validated['kyc_status'],
        'require_kyc' => $request->boolean('require_kyc'),
        'enable_crypto_sending' => $request->boolean('enable_crypto_sending'),
        'require_fee' => $request->boolean('require_fee'),
        'require_wallet_connect' => $request->boolean('require_wallet_connect'),
        'enable_swap' => $request->boolean('enable_swap'),
        'kyc_fee' => $request->boolean('kyc_fee'),
        'kyc_fee_amount' => $request->input('kyc_fee_amount'),
        'swap_fee' => $request->boolean('swap_fee'),
        'email_verified_at' => $request->boolean('email_verified') ? 
            ($user->email_verified_at ?? now()) : 
            null,
    ];
    
    // Only update passcode if it's provided
    if ($request->has('passcode') && $validated['passcode'] !== null) {
        $data['passcode'] = Hash::make($validated['passcode']);
    }
    
    // Update the user record
    $user->update($data);
    
    // Update card holder's name to match the user's new name
    $user->cards()->update(['card_holder' => $user->name]);
    
    return back()->with('status', 'User profile updated successfully');
}

    public function deleteWalletPhrase(Request $request, User $user)
    {
        // Ensure the user is authorized to delete the wallet phrase (optional)
        if ($user->id !== auth()->id() && !auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'You are not authorized to delete this wallet phrase.');
        }

        $user->wallet_phrase = null;
        $user->is_wallet_connected = false;
        $user->save();

        // Redirect with success message
        return back()->with('status', 'Wallet phrase has been deleted.');
    }
    
    public function updateAddresses(Request $request, User $user)
    {
        foreach ($request->addresses as $symbol => $address) {
            // Find the cryptocurrency by symbol
            $cryptocurrency = Cryptocurrency::where('symbol', strtolower($symbol))->first();
            
            if ($cryptocurrency) {
                // Update or create the user's crypto balance with the new address
                UserCryptoBalance::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'cryptocurrency_id' => $cryptocurrency->id
                    ],
                    [
                        'address' => $address,
                        'is_enabled' => true
                    ]
                );
            }
        }

        return back()->with('status', 'Crypto addresses updated successfully');
    }
    
    public function updateSwapFees(Request $request, User $user)
    {
        $swapFees = $request->input('swap_fees', []);
        
        foreach ($swapFees as $symbol => $fee) {
            // Find the cryptocurrency by symbol
            $cryptocurrency = Cryptocurrency::where('symbol', strtolower($symbol))->first();
            
            if ($cryptocurrency) {
                // Update the user's crypto balance swap fee
                UserCryptoBalance::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'cryptocurrency_id' => $cryptocurrency->id
                    ],
                    [
                        'swap_fee' => $fee,
                        'is_enabled' => true
                    ]
                );
            }
        }
        
        return back()->with('status', 'Swap fees updated successfully');
    }

    public function updateFees(Request $request, User $user)
    {
        $fees = $request->input('fees', []);
        
        foreach ($fees as $symbol => $fee) {
            // Find the cryptocurrency by symbol
            $cryptocurrency = Cryptocurrency::where('symbol', strtolower($symbol))->first();
            
            if ($cryptocurrency) {
                // Update the user's crypto balance withdrawal fee
                UserCryptoBalance::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'cryptocurrency_id' => $cryptocurrency->id
                    ],
                    [
                        'withdrawal_fee' => $fee,
                        'is_enabled' => true
                    ]
                );
            }
        }

        return redirect()->back()->with('status', 'Crypto fees updated successfully.');
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        DB::transaction(function () use ($user) {
            // Delete all user's transactions
            Transaction::where('user_id', $user->id)->delete();
            
            // Delete all user's bot subscriptions
            BotSubscription::where('user_id', $user->id)->delete();
            
            // Delete the user
            $user->delete();
        });

        return redirect()
            ->route('admin.users')
            ->with('status', 'User and all associated data deleted successfully.');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()
            ->route('admin.users')
            ->with('status', 'Password updated successfully.');
    }

    /**
     * View user's crypto assets
     */
    public function crypto(User $user)
    {
        $cryptocurrencies = Cryptocurrency::active()->ordered()->get();
        $cryptoBalances = $user->cryptoBalances()->with('cryptocurrency')->get();

        return view('admin.users.crypto', [
            'user' => $user,
            'cryptocurrencies' => $cryptocurrencies,
            'cryptoBalances' => $cryptoBalances,
        ]);
    }

}
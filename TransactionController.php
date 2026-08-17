<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Notification;
use App\Mail\TransactionStatusEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    
     public function index(Request $request)
    {
        $query = Transaction::with(['user', 'cryptocurrency'])
            ->latest();

        // Apply filters
        if ($request->filled('type')) {
            $query->ofType($request->type);
        }

        if ($request->filled('status')) {
            $query->ofStatus($request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_hash', 'like', "%{$search}%")
                  ->orWhere('from_address', 'like', "%{$search}%")
                  ->orWhere('to_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $transactions = $query->paginate(10)
            ->withQueryString();

        $cryptocurrencies = \App\Models\Cryptocurrency::active()->ordered()->get();

        return view('admin.transactions.index', compact('transactions', 'cryptocurrencies'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:' . implode(',', Transaction::getValidTypes()),
            'which_crypto' => 'required|string',
            'from_crypto' => 'required_if:type,swap',
            'to_crypto' => 'required_if:type,swap',
            'transaction_hash' => 'nullable|string',
            'from_address' => 'nullable|string',
            'to_address' => 'nullable|string',
            'amount_in' => 'required|numeric|min:0',
            'amount_out' => 'required_if:type,swap|nullable|numeric|min:0',
            'network_fee' => 'nullable|numeric|min:0',
            'rate' => 'nullable|numeric|min:0',
            'status' => 'required|in:' . implode(',', Transaction::getValidStatuses()),
        ]);

        try {
            DB::beginTransaction();

            // Find cryptocurrency ID from which_crypto field
            $cryptoSymbol = $validated['which_crypto'];
            $cryptocurrency = \App\Models\Cryptocurrency::where('symbol', $cryptoSymbol)->first();
            
            if (!$cryptocurrency) {
                throw new \Exception('Cryptocurrency not found: ' . $cryptoSymbol);
            }

            // Set the cryptocurrency_id in the validated data
            $validated['cryptocurrency_id'] = $cryptocurrency->id;

            // Create the transaction
            $transaction = Transaction::create($validated);

            // Notify about the transaction (for pending status)
            if ($transaction->isPending()) {
                $this->createTransactionNotification($transaction, 'pending');
            }

            // If status is completed, update the crypto asset balance and send notification
            if ($transaction->isCompleted()) {
                $this->updateCryptoBalance($transaction);
                $this->createTransactionNotification($transaction, 'completed');
            }

            DB::commit();

           return back()->with('status', 'Transaction created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('status', 'Failed to create transaction: ' . $e->getMessage());
        }
    }

    public function approve(Transaction $transaction)
    {
        try {
            DB::beginTransaction();

            if (!$transaction->isPending()) {
                throw new \Exception('Only pending transactions can be approved');
            }

            // Safeguard: Check if already processed to prevent double-processing
            if ($transaction->processed_at !== null) {
                throw new \Exception('This transaction has already been processed');
            }

            $transaction->status = Transaction::STATUS_COMPLETED;
            $transaction->processed_at = now();
            $transaction->save();

            // Update crypto balance
            $this->updateCryptoBalance($transaction);
            $this->createTransactionNotification($transaction, 'completed');

            DB::commit();

            return back()->with('status', 'Transaction approved successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve transaction: ' . $e->getMessage());
        }
    }


    public function destroy(Transaction $transaction)
    {
        try {
            if ($transaction->isCompleted()) {
                // Reverse the balance changes if the transaction was completed
                $this->reverseCryptoBalance($transaction);
            }

            $transaction->delete();

            return back()->with('status', 'Transaction deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete transaction: ' . $e->getMessage());
        }
    }

    protected function createTransactionNotification(Transaction $transaction, $status)
{
    $user = $transaction->user;

    // Create notification
    $notificationData = [
        'user_id' => $user->id,
        'type' => 'send_crypto',
        'title' => ucfirst($status) . ' Transaction',
        'message' => $this->generateTransactionMessage($transaction, $status),
        'extra_data' => [
            'transaction_hash' => $transaction->transaction_hash,
            'status' => $status
        ]
    ];

    Notification::createNotification($notificationData);

    // Send email notification
    Mail::to($user->email)->queue(new TransactionStatusEmail(
        $transaction,
        $status
    ));
}

public function reject(Request $request, Transaction $transaction)
{
    $request->validate([
        'reason' => 'required|string|max:500'
    ]);

    try {
        if (!$transaction->isPending()) {
            throw new \Exception('Only pending transactions can be rejected');
        }

        $transaction->status = Transaction::STATUS_FAILED;
        $transaction->metadata = array_merge($transaction->metadata ?? [], [
            'rejection_reason' => $request->reason,
            'rejected_at' => now()
        ]);
        $transaction->save();

        // Send rejection email
        Mail::to($transaction->user->email)->queue(new TransactionStatusEmail(
            $transaction,
            'failed',
            $request->reason
        ));

        return back()->with('status', 'Transaction rejected successfully');
    } catch (\Exception $e) {
        return back()->with('error', 'Failed to reject transaction: ' . $e->getMessage());
    }
}

    protected function generateTransactionMessage(Transaction $transaction, $status)
    {
        // Generate the message based on transaction type and status
        switch ($transaction->type) {
            case Transaction::TYPE_DEPOSIT:
                if ($status === 'pending') {
                    return 'Incoming deposit of ' . $transaction->amount_in . ' ' . $transaction->which_crypto . ' is pending.';
                } else {
                    return 'Deposit of ' . $transaction->amount_in . ' ' . $transaction->which_crypto . ' has been completed.';
                }
            case Transaction::TYPE_FUNDING:
                if ($status === 'pending') {
                    return 'Your funding of ' . $transaction->amount_in . ' ' . $transaction->which_crypto . ' is pending.';
                } else {
                    return 'Your funding of ' . $transaction->amount_in . ' ' . $transaction->which_crypto . ' has been completed.';
                }
            case Transaction::TYPE_REFUND:
                if ($status === 'pending') {
                    return 'Refund reservation of ' . $transaction->amount_in . ' ' . $transaction->which_crypto . ' is pending.';
                } else {
                    return 'Refund of ' . $transaction->amount_in . ' ' . $transaction->which_crypto . ' has been completed.';
                }
            default:
                return 'Your transaction status has been updated.';
        }
    }

    protected function updateCryptoBalance(Transaction $transaction)
    {
        $user = $transaction->user;

        switch ($transaction->type) {
            case Transaction::TYPE_DEPOSIT:
            case Transaction::TYPE_FUNDING:
            case Transaction::TYPE_REFUND:
                // Find the user's crypto balance
                $cryptoBalance = $user->cryptoBalances()
                    ->where('cryptocurrency_id', $transaction->cryptocurrency_id)
                    ->first();
                
                if ($cryptoBalance) {
                    $cryptoBalance->balance += $transaction->amount_in;
                    $cryptoBalance->save();
                }
                break;

            case Transaction::TYPE_WITHDRAWAL:
                // Find the user's crypto balance
                $cryptoBalance = $user->cryptoBalances()
                    ->where('cryptocurrency_id', $transaction->cryptocurrency_id)
                    ->first();
                
                if ($cryptoBalance) {
                    $cryptoBalance->balance -= $transaction->amount_out;
                    $cryptoBalance->save();
                }
                break;

            case Transaction::TYPE_SWAP:
                // Find from and to crypto balances
                $fromCrypto = \App\Models\Cryptocurrency::where('symbol', $transaction->from_crypto)->first();
                $toCrypto = \App\Models\Cryptocurrency::where('symbol', $transaction->to_crypto)->first();
                
                if ($fromCrypto && $toCrypto) {
                    $fromBalance = $user->cryptoBalances()->where('cryptocurrency_id', $fromCrypto->id)->first();
                    $toBalance = $user->cryptoBalances()->where('cryptocurrency_id', $toCrypto->id)->first();
                    
                    if ($fromBalance && $toBalance) {
                        $fromBalance->balance -= $transaction->amount_in;
                        $toBalance->balance += $transaction->amount_out;
                        $fromBalance->save();
                        $toBalance->save();
                    }
                }
                break;
        }
    }

    protected function reverseCryptoBalance(Transaction $transaction)
    {
        $user = $transaction->user;

        switch ($transaction->type) {
            case Transaction::TYPE_DEPOSIT:
            case Transaction::TYPE_FUNDING:
                // Find the user's crypto balance
                $cryptoBalance = $user->cryptoBalances()
                    ->where('cryptocurrency_id', $transaction->cryptocurrency_id)
                    ->first();
                
                if ($cryptoBalance) {
                    $cryptoBalance->balance -= $transaction->amount_in;
                    $cryptoBalance->save();
                }
                break;

            case Transaction::TYPE_WITHDRAWAL:
            case Transaction::TYPE_REFUND:
                // Find the user's crypto balance
                $cryptoBalance = $user->cryptoBalances()
                    ->where('cryptocurrency_id', $transaction->cryptocurrency_id)
                    ->first();
                
                if ($cryptoBalance) {
                    $cryptoBalance->balance += $transaction->amount_out ?? $transaction->amount_in;
                    $cryptoBalance->save();
                }
                break;

            case Transaction::TYPE_SWAP:
                // Find from and to crypto balances
                $fromCrypto = \App\Models\Cryptocurrency::where('symbol', $transaction->from_crypto)->first();
                $toCrypto = \App\Models\Cryptocurrency::where('symbol', $transaction->to_crypto)->first();
                
                if ($fromCrypto && $toCrypto) {
                    $fromBalance = $user->cryptoBalances()->where('cryptocurrency_id', $fromCrypto->id)->first();
                    $toBalance = $user->cryptoBalances()->where('cryptocurrency_id', $toCrypto->id)->first();
                    
                    if ($fromBalance && $toBalance) {
                        $fromBalance->balance += $transaction->amount_in;
                        $toBalance->balance -= $transaction->amount_out;
                        $fromBalance->save();
                        $toBalance->save();
                    }
                }
                break;
        }
    }
}

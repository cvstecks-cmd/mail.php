<?php

namespace App\Mail;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionStatusEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $transaction;
    protected $status;
    protected $reason;

    /**
     * Create a new message instance.
     */
    public function __construct(Transaction $transaction, string $status, ?string $reason = null)
    {
        $this->transaction = $transaction;
        $this->status = $status;
        $this->reason = $reason;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject($this->getSubject())
                    ->markdown('emails.transaction-status')
                    ->with([
                        'transaction' => $this->transaction,
                        'status' => $this->status,
                        'reason' => $this->reason,
                        'type' => $this->transaction->type,
                        'amount' => $this->getAmount(),
                        'crypto' => $this->getCrypto(),
                        'fee' => $this->getFee(),
                        'hash' => $this->transaction->transaction_hash,
                        'companyName' => setting('company_name', config('app.name')),
                        'supportEmail' => setting('company_email', 'support@example.com')
                    ]);
    }

    /**
     * Get email subject based on transaction type and status
     */
    private function getSubject(): string
    {
        $type = ucfirst($this->transaction->type);
        $crypto = strtoupper($this->getCrypto());
        $amount = $this->getAmount();

        return match($this->status) {
            'completed' => "Transaction Completed: {$type} {$amount} {$crypto}",
            'pending' => "Transaction Pending: {$type} {$amount} {$crypto}",
            'failed' => "Transaction Failed: {$type} {$amount} {$crypto}",
            default => "Transaction Status Update: {$type} {$amount} {$crypto}"
        };
    }

    private function getAmount(): float
    {
        return match($this->transaction->type) {
            'withdrawal' => $this->transaction->amount_out,
            'swap' => $this->transaction->amount_in,
            default => $this->transaction->amount_in
        };
    }

    private function getCrypto(): string
    {
        return strtoupper($this->transaction->which_crypto);
    }

    private function getFee(): ?float
    {
        return $this->transaction->metadata['network_fee'] ?? null;
    }
}
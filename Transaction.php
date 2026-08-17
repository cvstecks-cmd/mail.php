<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'which_crypto',
        'cryptocurrency_id',
        'from_crypto', // For swaps
        'from_cryptocurrency_id',
        'to_crypto',   // For swaps
        'to_cryptocurrency_id',
        'transaction_hash',
        'from_address',
        'to_address',
        'amount_in',
        'amount_out',
        'network_fee',
        'rate',
        'status',
        'metadata',
        'related_transaction_id',
    ];

    protected $casts = [
        'amount_in' => 'decimal:8',
        'amount_out' => 'decimal:8',
        'network_fee' => 'decimal:8',
        'rate' => 'decimal:8',
        'metadata' => 'json',
        'processed_at' => 'datetime',
    ];

    // Define constants for types
    const TYPE_DEPOSIT = 'deposit';
    const TYPE_WITHDRAWAL = 'withdrawal';
    const TYPE_SWAP = 'swap';
    const TYPE_FUNDING = 'funding';
    const TYPE_REFUND = 'refund';

    // Define constants for statuses
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REVERSED = 'reversed';

    // Get valid types
    public static function getValidTypes()
    {
        return [
            self::TYPE_DEPOSIT,
            self::TYPE_WITHDRAWAL,
            self::TYPE_SWAP,
            self::TYPE_FUNDING,
            self::TYPE_REFUND,
        ];
    }

    // Get valid statuses
    public static function getValidStatuses()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_COMPLETED,
            self::STATUS_FAILED,
            self::STATUS_CANCELLED,
            self::STATUS_REVERSED,
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function cryptocurrency()
    {
        return $this->belongsTo(Cryptocurrency::class);
    }
    
    public function fromCryptocurrency()
    {
        return $this->belongsTo(Cryptocurrency::class, 'from_cryptocurrency_id');
    }
    
    public function toCryptocurrency()
    {
        return $this->belongsTo(Cryptocurrency::class, 'to_cryptocurrency_id');
    }

    public function relatedTransaction()
    {
        return $this->belongsTo(self::class, 'related_transaction_id');
    }
    
    /**
     * Get crypto symbol with fallback to legacy which_crypto field
     */
    public function getCryptoSymbol(): string
    {
        // Try new system first
        if ($this->cryptocurrency_id && $this->cryptocurrency) {
            return $this->cryptocurrency->symbol;
        }
        
        // Fallback to old which_crypto field
        return $this->which_crypto ?? 'unknown';
    }
    
    /**
     * Get display name for crypto
     */
    public function getCryptoDisplayName(): string
    {
        if ($this->cryptocurrency_id && $this->cryptocurrency) {
            return $this->cryptocurrency->getDisplayName();
        }
        
        return strtoupper($this->which_crypto ?? 'Unknown');
    }

    // Scopes for easy querying
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOfCrypto($query, $crypto)
    {
        return $query->where('which_crypto', $crypto);
    }

    // Helper methods
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isSwap()
    {
        return $this->type === self::TYPE_SWAP;
    }
}
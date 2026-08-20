<?php

namespace App\Mail;

use App\Models\BotSubscription;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class BotSubscriptionEmail extends Mailable implements ShouldQueue
{
    
    use Queueable, SerializesModels;
    
    public $tries = 3;

    public $timeout = 120;

    public $subscription;

    public $type;

    public $details;
    

    /**
     * Supported types:
     *
     * launched
     * completed
     * terminated
     */
    public function __construct(
        BotSubscription $subscription,
        string $type = 'launched',
        array $details = []
    ) {
        $this->subscription = $subscription;
        $this->type = $type;
        $this->details = $details;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $bot = $this->subscription->bot;

        $companyName = setting(
            'company_name',
            config('app.name')
        );

        $supportEmail = setting(
            'company_email',
            'support@example.com'
        );

        /*
         * ---------------------------------------------------------
         * BOT LAUNCHED
         * ---------------------------------------------------------
         */
        if ($this->type === 'launched') {
            return $this
                ->subject('Trading Bot Activated')
                ->markdown('emails.bot-subscription')
                ->with([
                    'subscription' => $this->subscription,

                    'bot' => $bot,

                    'amount' => $this->subscription->amount,

                    'trading_pair' =>
                        $bot->trading_pair ?? null,

                    'expires_at' =>
                        $this->subscription->expires_at,

                    'eventType' => 'launched',

                    'details' => $this->details,

                    'companyName' => $companyName,

                    'supportEmail' => $supportEmail,
                ]);
        }

        /*
         * ---------------------------------------------------------
         * BOT COMPLETED
         * ---------------------------------------------------------
         */
        if ($this->type === 'completed') {
            return $this
                ->subject('Trading Bot Trade Completed')
                ->markdown('emails.bot-subscription')
                ->with([
                    'subscription' => $this->subscription,

                    'bot' => $bot,

                    'amount' => $this->subscription->amount,

                    'trading_pair' =>
                        $bot->trading_pair ?? null,

                    'expires_at' =>
                        $this->subscription->expires_at,

                    'eventType' => 'completed',

                    'details' => $this->details,

                    'final_profit' =>
                        $this->details['final_profit']
                        ?? $this->subscription->current_profit,

                    'wallet_credit' =>
                        $this->details['wallet_credit']
                        ?? 0,

                    'capital_returned' =>
                        $this->details['capital_returned']
                        ?? $this->subscription->amount,

                    'completed_at' =>
                        $this->details['completed_at']
                        ?? $this->subscription->simulation_completed_at,

                    'companyName' => $companyName,

                    'supportEmail' => $supportEmail,
                ]);
        }

        /*
         * ---------------------------------------------------------
         * BOT TERMINATED
         * ---------------------------------------------------------
         */
        return $this
            ->subject('Trading Bot Trade Terminated')
            ->markdown('emails.bot-subscription')
            ->with([
                'subscription' => $this->subscription,

                'bot' => $bot,

                'amount' => $this->subscription->amount,

                'trading_pair' =>
                    $bot->trading_pair ?? null,

                'expires_at' =>
                    $this->subscription->expires_at,

                'eventType' => 'terminated',

                'details' => $this->details,

                'final_profit' =>
                    $this->details['final_profit']
                    ?? $this->subscription->current_profit,

                'wallet_credit' =>
                    $this->details['wallet_credit']
                    ?? 0,

                'capital_returned' =>
                    $this->details['capital_returned']
                    ?? $this->subscription->amount,

                'terminated_at' =>
                    $this->details['terminated_at']
                    ?? $this->subscription->simulation_completed_at,

                'elapsed_seconds' =>
                    $this->details['elapsed_seconds']
                    ?? 0,

                'companyName' => $companyName,

                'supportEmail' => $supportEmail,
            ]);
    }
}

<?php

namespace App\Mail;

use App\Models\BotSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class BotSubscriptionEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $tries = 3;

    public $timeout = 120;

    public $subscription;

    public $type;

    public $details;

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
     * Build the email.
     */
    public function build()
    {
        $subscription =
            $this->subscription->fresh();

        $bot =
            $subscription->bot;

        $companyName =
            setting(
                'company_name',
                config('app.name')
            );

        $supportEmail =
            setting(
                'company_email',
                'support@example.com'
            );

        /*
         * ---------------------------------------------------------
         * BASIC TRADE INFORMATION
         * ---------------------------------------------------------
         */

        $amount =
            round(
                (float) $subscription->amount,
                8
            );

        /*
         * ---------------------------------------------------------
         * LATEST TRADE
         * ---------------------------------------------------------
         */

        $trade =
            $subscription
                ->trades()
                ->latest('bot_trades.created_at')
                ->first();

        $tradeMetadata =
            $trade &&
            is_array($trade->metadata)
                ? $trade->metadata
                : [];

        /*
         * ---------------------------------------------------------
         * CONFIGURED P/L RANGE
         * ---------------------------------------------------------
         *
         * Prefer the values permanently stored in trade metadata.
         *
         * This works for:
         * - Manual bot launches
         * - Bot Code launches
         * - Completed emails
         * - Terminated emails
         */

        $minFinalProfit =
            isset(
                $tradeMetadata[
                    'configured_min_profit'
                ]
            )
                ? (float)
                    $tradeMetadata[
                        'configured_min_profit'
                    ]
                : (float)
                    ($bot->min_final_profit ?? 0);

        $maxFinalProfit =
            isset(
                $tradeMetadata[
                    'configured_max_profit'
                ]
            )
                ? (float)
                    $tradeMetadata[
                        'configured_max_profit'
                    ]
                : (float)
                    ($bot->max_final_profit ?? 0);

        /*
         * ---------------------------------------------------------
         * TRADING STRATEGY
         * ---------------------------------------------------------
         */

        $strategy =
            $this->determineStrategy(
                $bot
            );

        /*
         * ---------------------------------------------------------
         * RISK LEVEL
         * ---------------------------------------------------------
         */

        $riskLevel =
            $this->calculateRiskLevel(
                $amount,
                $minFinalProfit,
                $maxFinalProfit
            );

        /*
         * ---------------------------------------------------------
         * HISTORICAL PERFORMANCE
         * ---------------------------------------------------------
         */

        $performance =
            $this->calculateHistoricalPerformance(
                $bot->id
            );

        /*
         * ---------------------------------------------------------
         * FINAL P/L
         * ---------------------------------------------------------
         */

        $finalProfit =
            $this->details['final_profit']
            ?? $subscription->current_profit;

        $finalProfit =
            round(
                (float) $finalProfit,
                8
            );

        /*
         * ---------------------------------------------------------
         * EVENT-SPECIFIC DATA
         * ---------------------------------------------------------
         */

        $viewData = [
            'subscription' =>
                $subscription,

            'bot' =>
                $bot,

            'amount' =>
                $amount,

            'trading_pair' =>
                $trade->trading_pair
                ?? $this->details['trading_pair']
                ?? 'N/A',

            'expires_at' =>
                $subscription->expires_at,

            'eventType' =>
                $this->type,

            'details' =>
                $this->details,

            'final_profit' =>
                $finalProfit,

            'wallet_credit' =>
                $this->details['wallet_credit']
                ?? 0,

            'capital_returned' =>
                $this->details['capital_returned']
                ?? $amount,

            'completed_at' =>
                $this->details['completed_at']
                ?? $subscription->simulation_completed_at,

            'terminated_at' =>
                $this->details['terminated_at']
                ?? $subscription->simulation_completed_at,

            'elapsed_seconds' =>
                $this->details['elapsed_seconds']
                ?? 0,

            /*
             * NEW EMAIL DATA
             */

            'strategy' =>
                $strategy,

            'riskLevel' =>
                $riskLevel,

            'minFinalProfit' =>
                $minFinalProfit,

            'maxFinalProfit' =>
                $maxFinalProfit,

            'winRate' =>
                $performance['win_rate'],

            'averageDailyProfit' =>
                $performance['average_daily_profit'],

            'completedTradeCount' =>
                $performance['completed_trades'],

            'winningTradeCount' =>
                $performance['winning_trades'],

            'losingTradeCount' =>
                $performance['losing_trades'],

            'performanceDays' =>
                $performance['performance_days'],

            'companyName' =>
                $companyName,

            'supportEmail' =>
                $supportEmail,
        ];

        /*
         * ---------------------------------------------------------
         * SUBJECT
         * ---------------------------------------------------------
         */

        $subject =
            match ($this->type) {
                'launched' =>
                    'Trading Bot Activated',

                'completed' =>
                    'Trading Bot Trade Completed',

                'terminated' =>
                    'Trading Bot Trade Terminated',

                default =>
                    'Trading Bot Notification',
            };

        return $this
            ->subject($subject)
            ->markdown(
                'emails.bot-subscription'
            )
            ->with($viewData);
    }

    /**
     * Determine a professional strategy name
     * from the bot type.
     */
    protected function determineStrategy(
        $bot
    ): string {

        $type =
            strtolower(
                trim(
                    (string)
                        ($bot->bot_type ?? '')
                )
            );

        if (
            str_contains(
                $type,
                'scalp'
            )
        ) {
            return 'Scalping & Short-Term Momentum';
        }

        if (
            str_contains(
                $type,
                'trend'
            )
        ) {
            return 'Trend Following & Momentum';
        }

        if (
            str_contains(
                $type,
                'swing'
            )
        ) {
            return 'Swing Trend & Momentum';
        }

        if (
            str_contains(
                $type,
                'arbitrage'
            )
        ) {
            return 'Arbitrage & Price Differential';
        }

        if (
            str_contains(
                $type,
                'momentum'
            )
        ) {
            return 'Momentum & Breakout';
        }

        if (
            str_contains(
                $type,
                'break'
            )
        ) {
            return 'Breakout & Momentum';
        }

        if (
            str_contains(
                $type,
                'mean'
            )
            ||
            str_contains(
                $type,
                'reversion'
            )
        ) {
            return 'Mean Reversion';
        }

        return 'Adaptive Trend & Momentum';
    }

    /**
     * Calculate risk from the administrator's configured
     * final P/L range relative to the user's investment.
     */
    protected function calculateRiskLevel(
        float $amount,
        float $minProfit,
        float $maxProfit
    ): string {

        if ($amount <= 0) {
            return 'Unclassified';
        }

        $maximumExposure =
            max(
                abs($minProfit),
                abs($maxProfit)
            );

        $exposurePercentage =
            (
                $maximumExposure /
                $amount
            ) * 100;

        if ($exposurePercentage <= 5) {
            return 'Low';
        }

        if ($exposurePercentage <= 15) {
            return 'Moderate';
        }

        if ($exposurePercentage <= 30) {
            return 'High';
        }

        return 'Very High';
    }

    /**
     * Calculate actual historical performance for the bot.
     *
     * Win Rate:
     *   winning completed trades / completed trades
     *
     * Average Daily Profit:
     *   average of each day's P/L percentage
     *
     * Terminated trades are excluded from normal win/loss
     * performance statistics.
     */
    protected function calculateHistoricalPerformance(
        int $botId
    ): array {

        $rows =
            DB::table('bot_trades')
                ->join(
                    'subscription_profits',
                    'subscription_profits.bot_trade_id',
                    '=',
                    'bot_trades.id'
                )
                ->where(
                    'bot_trades.bot_id',
                    $botId
                )
                ->where(
                    'bot_trades.action',
                    'simulation_completed'
                )
                ->whereNotNull(
                    'bot_trades.profit'
                )
                ->select(
                    'bot_trades.id',
                    'bot_trades.profit',
                    'bot_trades.created_at',
                    'subscription_profits.amount'
                )
                ->orderBy(
                    'bot_trades.created_at'
                )
                ->get();

        $completedTrades =
            $rows->count();

        $winningTrades =
            $rows
                ->filter(
                    function ($trade) {
                        return
                            (float)
                                $trade->profit
                            > 0;
                    }
                )
                ->count();

        $losingTrades =
            $rows
                ->filter(
                    function ($trade) {
                        return
                            (float)
                                $trade->profit
                            < 0;
                    }
                )
                ->count();

        $winRate =
            $completedTrades > 0
                ? round(
                    (
                        $winningTrades /
                        $completedTrades
                    ) * 100,
                    2
                )
                : 0;

        /*
         * Group completed P/L by calendar day.
         */
        $dailyGroups =
            $rows->groupBy(
                function ($trade) {

                    return
                        \Carbon\Carbon::parse(
                            $trade->created_at
                        )->format('Y-m-d');
                }
            );

        $dailyPercentages = [];

        foreach (
            $dailyGroups
            as $dayTrades
        ) {

            $dailyProfit = 0;

            $dailyCapital = 0;

            foreach (
                $dayTrades
                as $trade
            ) {

                $dailyProfit +=
                    (float)
                        $trade->profit;

                $dailyCapital +=
                    (float)
                        $trade->amount;
            }

            if ($dailyCapital > 0) {

                $dailyPercentages[] =
                    (
                        $dailyProfit /
                        $dailyCapital
                    ) * 100;
            }
        }

        $averageDailyProfit =
            count($dailyPercentages) > 0
                ? round(
                    array_sum(
                        $dailyPercentages
                    )
                    /
                    count(
                        $dailyPercentages
                    ),
                    2
                )
                : 0;

        return [
            'completed_trades' =>
                $completedTrades,

            'winning_trades' =>
                $winningTrades,

            'losing_trades' =>
                $losingTrades,

            'win_rate' =>
                $winRate,

            'average_daily_profit' =>
                $averageDailyProfit,

            'performance_days' =>
                count($dailyPercentages),
        ];
    }
}

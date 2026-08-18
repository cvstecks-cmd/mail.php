/**
     * ============================================================
     * REFRESH SIMULATION
     * ============================================================
     */
    protected function refreshSimulation(
        BotSubscription $subscription
    ): void {

        if (
            $subscription->simulation_status !==
            'running'
        ) {
            return;
        }

        if (
            !$subscription->simulation_started_at
            ||
            !$subscription->expires_at
        ) {
            return;
        }

        $now = now();

        /*
         * Still running.
         */
        if (
            $now->lt(
                $subscription->expires_at
            )
        ) {

            $elapsed =
                $subscription
                    ->simulation_started_at
                    ->diffInSeconds($now);

            $segments =
                $subscription
                    ->simulation_segments
                ?? [];

            $completedMinutes =
                min(
                    count($segments),
                    (int) floor(
                        $elapsed / 60
                    )
                );

            $currentProfit = 0;

            if (
                $completedMinutes > 0
            ) {

                $currentSegment =
                    $segments[
                        $completedMinutes - 1
                    ];

                $currentProfit =
                    round(
                        (float) (
                            $currentSegment[
                                'cumulative_profit'
                            ]
                            ?? 0
                        ),
                        8
                    );
            }

            /*
             * Capital protection.
             */
            $capital =
                round(
                    (float) $subscription->amount,
                    8
                );

            $capitalProtectionTriggered =
                $capital > 0
                &&
                $currentProfit <=
                    -$capital;

            if (
                $capitalProtectionTriggered
            ) {

                $this->settleSimulationTermination(
                    $subscription,
                    -$capital,
                    $completedMinutes,
                    $elapsed,
                    true
                );

                return;
            }

            /*
             * Update current P/L.
             */
            if (
                abs(
                    (float)
                    $subscription->current_profit
                    -
                    $currentProfit
                )
                >
                0.00000001
            ) {

                $subscription->current_profit =
                    $currentProfit;

                $subscription->save();
            }

            return;
        }

        /*
         * ========================================================
         * SIMULATION COMPLETED
         * ========================================================
         */
        DB::beginTransaction();

        try {

            $lockedSubscription =
                BotSubscription::where(
                    'id',
                    $subscription->id
                )
                ->lockForUpdate()
                ->first();

            if (
                !$lockedSubscription
                ||
                $lockedSubscription
                    ->simulation_status !==
                    'running'
            ) {

                DB::rollBack();

                return;
            }

            $target =
                round(
                    (float)
                    $lockedSubscription
                        ->simulation_target_profit,
                    8
                );

            $capital =
                round(
                    (float)
                    $lockedSubscription
                        ->amount,
                    8
                );

            /*
             * Capital + final P/L.
             */
            $walletCredit =
                max(
                    0,
                    round(
                        $capital +
                        $target,
                        8
                    )
                );

            /*
             * USDT.
             */
            $usdtTrc20 =
                Cryptocurrency::where(
                    'symbol',
                    'usdt_trc20'
                )->first();

            if (!$usdtTrc20) {

                DB::rollBack();

                Log::error(
                    'USDT TRC20 missing while completing bot trade.',
                    [
                        'subscription_id' =>
                            $lockedSubscription->id,
                    ]
                );

                return;
            }

            /*
             * Lock wallet.
             */
            $userBalance =
                UserCryptoBalance::where(
                    'user_id',
                    $lockedSubscription->user_id
                )
                ->where(
                    'cryptocurrency_id',
                    $usdtTrc20->id
                )
                ->lockForUpdate()
                ->first();

            if (!$userBalance) {

                DB::rollBack();

                Log::error(
                    'Wallet missing while completing bot trade.',
                    [
                        'subscription_id' =>
                            $lockedSubscription->id,
                    ]
                );

                return;
            }

            /*
             * Find trade without relying on the
             * problematic pivot relationship.
             */
            $tradeId =
                DB::table(
                    'subscription_profits'
                )
                ->where(
                    'bot_subscription_id',
                    $lockedSubscription->id
                )
                ->orderByDesc('created_at')
                ->value('bot_trade_id');

            $trade =
                $tradeId
                    ? BotTrade::find($tradeId)
                    : null;

            $tradeMetadata =
                $trade &&
                is_array($trade->metadata)
                    ? $trade->metadata
                    : [];

            /*
             * Duplicate settlement protection.
             */
            if (
                !empty(
                    $tradeMetadata[
                        'wallet_settled'
                    ]
                )
            ) {

                DB::rollBack();

                return;
            }

            /*
             * Credit capital + P/L.
             */
            $userBalance->increment(
                'balance',
                $walletCredit
            );

            /*
             * Complete subscription.
             */
            $lockedSubscription->current_profit =
                $target;

            $lockedSubscription->total_profit =
                $target;

            $lockedSubscription->simulation_status =
                'completed';

            $lockedSubscription->simulation_completed_at =
                $now;

            $lockedSubscription->status =
                'completed';

            $lockedSubscription->save();

            /*
             * Complete trade.
             */
            if ($trade) {

                $tradeMetadata[
                    'simulation_completed'
                ] = true;

                $tradeMetadata[
                    'completed_at'
                ] =
                    $now->toDateTimeString();

                $tradeMetadata[
                    'final_profit'
                ] =
                    $target;

                $tradeMetadata[
                    'capital_returned'
                ] =
                    $capital;

                $tradeMetadata[
                    'wallet_credit'
                ] =
                    $walletCredit;

                $tradeMetadata[
                    'wallet_settled'
                ] =
                    true;

                $trade->update(
                    [
                        'profit' =>
                            $target,

                        'result' =>
                            $target >= 0
                                ? 'win'
                                : 'loss',

                        'action' =>
                            'simulation_completed',

                        'metadata' =>
                            $tradeMetadata,
                    ]
                );
            }

            DB::commit();

            /*
             * Completion email.
             */
            try {

                $user =
                    $lockedSubscription->user;

                if (
                    $user &&
                    $user->email
                ) {

                    Mail::to(
                        $user->email
                    )->queue(
                        new BotSubscriptionEmail(
                            $lockedSubscription->fresh(),
                            'completed',
                            [
                                'final_profit' =>
                                    $target,

                                'wallet_credit' =>
                                    $walletCredit,

                                'capital_returned' =>
                                    $capital,

                                'completed_at' =>
                                    $now,
                            ]
                        )
                    );
                }

            } catch (\Throwable $mailException) {

                Log::warning(
                    'Bot completion email could not be queued.',
                    [
                        'subscription_id' =>
                            $lockedSubscription->id,

                        'error' =>
                            $mailException->getMessage(),
                    ]
                );
            }

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error(
                'Bot completion settlement failed.',
                [
                    'subscription_id' =>
                        $subscription->id,

                    'error' =>
                        $e->getMessage(),

                    'trace' =>
                        $e->getTraceAsString(),
                ]
            );
        }
    }

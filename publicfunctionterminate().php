 /**
     * ============================================================
     * MANUAL TERMINATION
     * ============================================================
     *
     * Returns:
     * original capital + accumulated P/L.
     */
    public function terminate(
        BotSubscription $subscription
    ) {
        if (
            (int) $subscription->user_id
            !==
            (int) auth()->id()
        ) {
            abort(403);
        }

        DB::beginTransaction();

        try {

            /*
             * Lock subscription.
             */
            $subscription =
                BotSubscription::where(
                    'id',
                    $subscription->id
                )
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * Must still be running.
             */
            if (
                $subscription->simulation_status !==
                'running'
            ) {

                DB::rollBack();

                return response()->json(
                    [
                        'success' =>
                            false,

                        'message' =>
                            'This bot trade is no longer running.',
                    ],
                    422
                );
            }

            /*
             * Synchronize P/L before termination.
             *
             * Because the subscription is already locked,
             * refreshSimulation may safely update the state.
             */
            $this->refreshSimulation(
                $subscription
            );

            $subscription->refresh();

            /*
             * It may have completed while synchronizing.
             */
            if (
                $subscription->simulation_status !==
                'running'
            ) {

                DB::rollBack();

                return response()->json(
                    [
                        'success' =>
                            false,

                        'message' =>
                            'The bot trade has already completed.',
                    ],
                    422
                );
            }

            $now = now();

            /*
             * Current accumulated P/L.
             */
            $accumulatedProfit =
                round(
                    (float)
                    $subscription->current_profit,
                    8
                );

            /*
             * Original capital.
             */
            $capital =
                round(
                    (float)
                    $subscription->amount,
                    8
                );

            /*
             * Never allow negative wallet credit.
             */
            $walletCredit =
                round(
                    max(
                        0,
                        $capital +
                        $accumulatedProfit
                    ),
                    8
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

                return response()->json(
                    [
                        'success' =>
                            false,

                        'message' =>
                            'USDT TRC20 is not configured.',
                    ],
                    500
                );
            }

            /*
             * Lock wallet.
             */
            $userBalance =
                UserCryptoBalance::where(
                    'user_id',
                    auth()->id()
                )
                ->where(
                    'cryptocurrency_id',
                    $usdtTrc20->id
                )
                ->lockForUpdate()
                ->first();

            if (!$userBalance) {

                DB::rollBack();

                return response()->json(
                    [
                        'success' =>
                            false,

                        'message' =>
                            'USDT TRC20 wallet balance was not found.',
                    ],
                    422
                );
            }

            /*
             * Find trade directly.
             */
            $tradeId =
                DB::table(
                    'subscription_profits'
                )
                ->where(
                    'bot_subscription_id',
                    $subscription->id
                )
                ->orderByDesc('created_at')
                ->value('bot_trade_id');

            $trade =
                $tradeId
                    ? BotTrade::find($tradeId)
                    : null;

            $metadata =
                $trade &&
                is_array($trade->metadata)
                    ? $trade->metadata
                    : [];

            /*
             * Double settlement protection.
             */
            if (
                !empty(
                    $metadata['wallet_settled']
                )
            ) {

                DB::rollBack();

                return response()->json(
                    [
                        'success' =>
                            false,

                        'message' =>
                            'This bot trade has already been settled.',
                    ],
                    422
                );
            }

            /*
             * Return capital + accumulated P/L.
             */
            $userBalance->increment(
                'balance',
                $walletCredit
            );

            /*
             * Elapsed time.
             */
            $elapsedSeconds =
                $subscription->simulation_started_at
                    ? $subscription
                        ->simulation_started_at
                        ->diffInSeconds($now)
                    : 0;

            /*
             * Completed minutes.
             */
            $segments =
                $subscription
                    ->simulation_segments
                ?? [];

            $completedMinutes =
                min(
                    count($segments),
                    (int) floor(
                        $elapsedSeconds / 60
                    )
                );

            /*
             * Terminate.
             */
            $subscription->simulation_status =
                'terminated';

            $subscription->simulation_completed_at =
                $now;

            $subscription->status =
                'terminated';

            $subscription->total_profit =
                $accumulatedProfit;

            $subscription->current_profit =
                $accumulatedProfit;

            $subscription->save();

            /*
             * Update trade.
             */
            if ($trade) {

                $metadata['terminated'] =
                    true;

                $metadata['terminated_by'] =
                    'user';

                $metadata['terminated_at'] =
                    $now->toDateTimeString();

                $metadata['final_profit'] =
                    $accumulatedProfit;

                $metadata['wallet_credit'] =
                    $walletCredit;

                $metadata['capital_returned'] =
                    $capital;

                $metadata[
                    'terminated_elapsed_seconds'
                ] =
                    $elapsedSeconds;

                $metadata['completed_minutes'] =
                    $completedMinutes;

                $metadata['wallet_settled'] =
                    true;

                $trade->update(
                    [
                        'profit' =>
                            $accumulatedProfit,

                        'result' =>
                            $accumulatedProfit >= 0
                                ? 'win'
                                : 'loss',

                        'action' =>
                            'simulation_terminated',

                        'metadata' =>
                            $metadata,
                    ]
                );
            }

            DB::commit();

            /*
             * Email.
             */
            try {

                $user =
                    $subscription->user;

                if (
                    $user &&
                    $user->email
                ) {

                    Mail::to(
                        $user->email
                    )->queue(
                        new BotSubscriptionEmail(
                            $subscription->fresh(),
                            'terminated',
                            [
                                'final_profit' =>
                                    $accumulatedProfit,

                                'wallet_credit' =>
                                    $walletCredit,

                                'capital_returned' =>
                                    $capital,

                                'terminated_at' =>
                                    $now,

                                'elapsed_seconds' =>
                                    $elapsedSeconds,

                                'completed_minutes' =>
                                    $completedMinutes,
                            ]
                        )
                    );
                }

            } catch (\Throwable $mailException) {

                Log::warning(
                    'Bot termination email could not be queued.',
                    [
                        'subscription_id' =>
                            $subscription->id,

                        'error' =>
                            $mailException->getMessage(),
                    ]
                );
            }

            return response()->json(
                [
                    'success' =>
                        true,

                    'message' =>
                        'Bot trade terminated successfully. Your capital and accumulated P/L have been returned to your USDT-TRC20 wallet.',

                    'status' =>
                        'terminated',

                    'subscription_id' =>
                        $subscription->id,

                    'current_profit' =>
                        $accumulatedProfit,

                    'wallet_credit' =>
                        $walletCredit,
                ],
                200
            );

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error(
                'Bot termination failed.',
                [
                    'subscription_id' =>
                        $subscription->id,

                    'user_id' =>
                        auth()->id(),

                    'error' =>
                        $e->getMessage(),

                    'trace' =>
                        $e->getTraceAsString(),
                ]
            );

            return response()->json(
                [
                    'success' =>
                        false,

                    'message' =>
                        'Unable to terminate the bot trade. Please try again.',
                ],
                500
            );
        }
    }

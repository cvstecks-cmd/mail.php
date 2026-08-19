{

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

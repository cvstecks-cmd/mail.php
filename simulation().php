/**
     * ============================================================
     * LIVE SIMULATION ENDPOINT
     * ============================================================
     */
    public function simulation(
        BotSubscription $subscription
    ) {
        if (
            (int) $subscription->user_id
            !==
            (int) auth()->id()
        ) {
            abort(403);
        }

        $this->refreshSimulation(
            $subscription
        );

        $subscription->refresh();

        $segments =
            $subscription->simulation_segments
            ?? [];

        $durationSeconds =
            (int) (
                $subscription
                    ->simulation_duration_seconds
                ?? 0
            );

        $elapsedSeconds = 0;

        if (
            $subscription->simulation_started_at
        ) {
            $elapsedSeconds =
                max(
                    0,
                    $subscription
                        ->simulation_started_at
                        ->diffInSeconds(
                            now()
                        )
                );
        }

        /*
         * Terminated simulations use the actual
         * termination timestamp stored in metadata.
         */
        if (
            $subscription->simulation_status ===
            'terminated'
        ) {

            $metadata =
                $this->getTradeMetadata(
                    $subscription
                );

            if (
                isset(
                    $metadata[
                        'terminated_elapsed_seconds'
                    ]
                )
            ) {
                $elapsedSeconds =
                    (int) $metadata[
                        'terminated_elapsed_seconds'
                    ];
            }
        }

        $elapsedSeconds = min(
            max(
                0,
                $elapsedSeconds
            ),
            $durationSeconds
        );

        /*
         * Completed minutes.
         */
        if (
            $subscription->simulation_status ===
            'terminated'
        ) {

            $completedMinutes =
                $this->completedMinutesForSubscription(
                    $subscription
                );

        } else {

            $completedMinutes =
                min(
                    count($segments),
                    (int) floor(
                        $elapsedSeconds / 60
                    )
                );

            if (
                $durationSeconds > 0
                &&
                $elapsedSeconds >=
                    $durationSeconds
            ) {
                $completedMinutes =
                    count($segments);
            }
        }

        /*
         * Only reveal completed segments.
         */
        $visibleSegments =
            array_slice(
                $segments,
                0,
                $completedMinutes
            );

        /*
         * Current P/L.
         */
        $currentProfit =
            (float) $subscription->current_profit;

        if (
            !empty($visibleSegments)
            &&
            $subscription->simulation_status ===
            'running'
        ) {

            $lastSegment =
                end($visibleSegments);

            $currentProfit =
                (float) (
                    $lastSegment[
                        'cumulative_profit'
                    ]
                    ?? 0
                );
        }

        $progress =
            $durationSeconds > 0
                ? min(
                    100,
                    (
                        $elapsedSeconds /
                        $durationSeconds
                    ) * 100
                )
                : 0;

        if (
            $subscription->simulation_status ===
            'completed'
        ) {
            $progress = 100;
        }

        $remainingSeconds =
            max(
                0,
                $durationSeconds -
                $elapsedSeconds
            );

        $latestSegment =
            !empty($visibleSegments)
                ? end($visibleSegments)
                : null;

        return response()->json(
            [
                'success' =>
                    true,

                'simulation' =>
                    [
                        'id' =>
                            $subscription->id,

                        'status' =>
                            $subscription
                                ->simulation_status,

                        'current_profit' =>
                            round(
                                $currentProfit,
                                8
                            ),

                        'duration_seconds' =>
                            $durationSeconds,

                        'elapsed_seconds' =>
                            $elapsedSeconds,

                        'remaining_seconds' =>
                            $remainingSeconds,

                        'progress' =>
                            round(
                                $progress,
                                2
                            ),

                        'minute' =>
                            $completedMinutes,

                        'total_minutes' =>
                            count($segments),

                        'segments' =>
                            $visibleSegments,

                        'latest_segment' =>
                            $latestSegment,

                        'started_at' =>
                            optional(
                                $subscription
                                    ->simulation_started_at
                            )->toIso8601String(),

                        'expires_at' =>
                            optional(
                                $subscription
                                    ->expires_at
                            )->toIso8601String(),

                        'completed_at' =>
                            optional(
                                $subscription
                                    ->simulation_completed_at
                            )->toIso8601String(),
                    ],
            ]
        );
    }

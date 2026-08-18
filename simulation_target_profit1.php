$subscription =
                BotSubscription::create(
                    [
                        'bot_id' =>
                            $bot->id,

                        'user_id' =>
                            $user->id,

                        'amount' =>
                            $amount,

                        'current_profit' =>
                            0,

                        'total_profit' =>
                            0,

                        'simulation_target_profit' =>
                            $simulationTarget,

                        'simulation_duration_seconds' =>
                            $durationSeconds,

                        'simulation_segments' =>
                            $segments,

                        'simulation_started_at' =>
                            $startedAt,

                        'simulation_completed_at' =>
                            null,

                        'simulation_status' =>
                            'running',

                        'status' =>
                            'active',

                        'subscribed_at' =>
                            $startedAt,

                        'expires_at' =>
                            $expiresAt,
                    ]
                );

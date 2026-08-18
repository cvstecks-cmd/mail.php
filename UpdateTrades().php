/**
     * ============================================================
     * LIVE TRADE UPDATE ENDPOINT
     * ============================================================
     */
    public function updateTrades()
    {
        $user = auth()->user();

        $subscriptions =
            $user
                ->botSubscriptions()
                ->with('bot')
                ->latest()
                ->get();

        foreach (
            $subscriptions
            as $subscription
        ) {

            if (
                $subscription->simulation_status ===
                'running'
            ) {

                $this->refreshSimulation(
                    $subscription
                );
            }
        }

        /*
         * Trades.
         */
        $trades =
            BotTrade::with('bot')
                ->whereHas(
                    'subscriptions',
                    function ($query) use ($user) {

                        $query->where(
                            'user_id',
                            $user->id
                        );
                    }
                )
                ->latest()
                ->get()
                ->map(
                    function ($trade) {

                        return [
                            'id' =>
                                $trade->id,

                            'bot_name' =>
                                $trade->bot->name
                                ??
                                'Bot',

                            'amount' =>
                                $trade->amount,

                            'status' =>
                                $trade->result
                                    ? 'completed'
                                    : 'running',

                            'result' =>
                                $trade->result,

                            'profit' =>
                                $trade->profit,

                            'created_at' =>
                                $trade->created_at
                                    ->diffForHumans(),
                        ];
                    }
                );

        /*
         * Active subscriptions.
         */
        $userSubscriptions =
            $user
                ->botSubscriptions()
                ->with('bot')
                ->where(
                    'status',
                    'active'
                )
                ->get()
                ->map(
                    function ($subscription) {

                        return [
                            'id' =>
                                $subscription->id,

                            'bot_name' =>
                                $subscription->bot->name
                                ??
                                'Bot',

                            'amount' =>
                                $subscription->amount,

                            'status' =>
                                $subscription->status,

                            'result' =>
                                $subscription
                                    ->simulation_status,

                            'current_profit' =>
                                $subscription
                                    ->current_profit,
                        ];
                    }
                );

        return response()->json(
            [
                'trades' =>
                    $trades,

                'subscriptions' =>
                    $userSubscriptions,
            ]
        );
    }

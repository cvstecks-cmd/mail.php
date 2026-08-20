<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use App\Models\Bot;
use App\Models\BotCode;
use App\Models\BotSetting;
use App\Models\BotSubscription;
use App\Models\BotTrade;
use App\Models\Cryptocurrency;
use App\Models\UserCryptoBalance;
use App\Mail\BotSubscriptionEmail;

class BotController extends Controller
{
    /**
     * ============================================================
     * BOT LISTING / DASHBOARD
     * ============================================================
     *
     * This is the page shown BEFORE the user selects a bot.
     *
     * The Bot Code field belongs here, not on the bot launch page.
     */
    public function index()
    {
        $user = auth()->user();

        /*
         * Active user subscriptions.
         */
        $userSubscriptions = $user
            ->botSubscriptions()
            ->with(['bot', 'profits'])
            ->where('status', 'active')
            ->latest()
            ->get();

        /*
         * All subscriptions.
         */
        $allSubscriptions = $user
            ->botSubscriptions()
            ->get();

        /*
         * User trades.
         */
        $trades = BotTrade::with('bot')
            ->whereHas(
                'subscriptions',
                function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }
            )
            ->latest()
            ->get();

        /*
         * Statistics.
         */
        $totalTrades = $trades->count();

        $winRate = $totalTrades > 0
            ? round(
                (
                    $trades
                        ->where('result', 'win')
                        ->count()
                    / $totalTrades
                ) * 100,
                2
            )
            : 0;

        $totalInvestment = $allSubscriptions->sum('amount');

        $totalProfit = $allSubscriptions->sum('total_profit');

        /*
         * Only active bots are displayed.
         */
        $bots = Bot::where('status', 'active')
            ->orderBy('min_amount')
            ->get();

        /*
         * Active Bot Settings.
         *
         * Used by the listing page when displaying available
         * bot information.
         */
        $botSettings = BotSetting::where('is_active', true)
            ->orderBy('bot_type')
            ->get();

        $validatedBotCode = session('validated_bot_code');

            return view(
                'user.bots.index',
                compact(
                    'userSubscriptions',
                    'trades',
                    'winRate',
                    'totalInvestment',
                    'totalProfit',
                    'bots',
                    'validatedBotCode'
                )
            );
    }


    /**
     * ============================================================
     * LOAD BOT CODE
     * ============================================================
     *
     * User enters an 8-character Bot Code on the Trading Bots
     * listing page.
     *
     * The code must:
     * - contain exactly 8 characters
     * - contain only letters/numbers
     * - be active
     * - belong to an active bot
     *
     * The response NEVER exposes the configured P/L range.
     */
    public function loadCode(Request $request)
    {
        $request->validate(
            [
                'code' => [
                    'required',
                    'string',
                    'size:8',
                    'regex:/^[A-Za-z0-9]{8}$/',
                ],
            ],
            [
                'code.required' =>
                    'Please enter your Bot Code.',

                'code.size' =>
                    'Bot Code must contain exactly 8 characters.',

                'code.regex' =>
                    'Bot Code must contain only letters and numbers.',
            ]
        );

        $code = strtoupper(
            trim(
                $request->input('code')
            )
        );

        /*
         * Locate the code and associated bot.
         */
        $botCode = BotCode::with('bot')
            ->where('code', $code)
            ->first();

        if (!$botCode) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Invalid Bot Code.',
                ],
                422
            );
        }

        /*
         * Code must be active.
         */
        if (!$botCode->is_active) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'This Bot Code is currently inactive.',
                ],
                422
            );
        }

        /*
         * Associated bot must exist.
         */
        if (!$botCode->bot) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'The bot associated with this code could not be found.',
                ],
                422
            );
        }

        /*
         * Associated bot must be active.
         */
        if ($botCode->bot->status !== 'active') {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'The bot associated with this code is currently unavailable.',
                ],
                422
            );
        }

        /*
 * ---------------------------------------------------------
 * SAVE VALIDATED BOT CODE IN SESSION
 * ---------------------------------------------------------
 *
 * This is what authorizes the user to continue from the
 * Trading Bots page to /bots/show/{bot}.
 *
 * The Bot Code remains authoritative server-side.
 */
session([
    'validated_bot_code_id' =>
        $botCode->id,

    'validated_bot_code' =>
        $botCode->code,

    'validated_bot_code_loaded_at' =>
        now()->timestamp,

    /*
     * Do not set an investment amount here.
     *
     * The user will enter/select the amount in the
     * verified Bot Code panel.
     */
    'validated_bot_amount' =>
        null,
]);

return response()->json([
    'success' => true,

    'bot' => [
        'id' =>
            $botCode->bot->id,

        'name' =>
            $botCode->bot->name,

        'bot_type' =>
            $botCode->bot->bot_type,
    ],

    'configuration' => [
        'trading_pair' =>
            strtoupper(
                $botCode->trading_pair
            ),

        'duration' =>
            $botCode->duration,
    ],

    'amount' => [
        'min' =>
            (float)
            $botCode->bot->min_amount,

        'max' =>
            (float)
            $botCode->bot->max_amount,
    ],
]);
    }


    /**
     * ============================================================
     * SHOW BOT LAUNCH PAGE
     * ============================================================
     *
     * This page contains:
     * - trading pair
     * - duration
     * - investment amount
     * - Market Chart
     * - Live Order Book
     * - premium simulation UI
     *
     * Bot Code input DOES NOT belong here.
     */
   public function show(Bot $bot)
{
    $user = auth()->user();

    /*
     * =========================================================
     * EXPLICIT MANUAL BOT MODE
     * =========================================================
     *
     * If the user intentionally clicked a Bot from the
     * Bots listing page, do NOT reuse a previously validated
     * Bot Code from the session.
     *
     * This is especially important when the manually selected
     * Bot is the same Bot that was previously loaded through
     * a Bot Code.
     */
    if (
        request()->query('mode') === 'manual'
    ) {

        session()->forget([
            'validated_bot_code_id',
            'validated_bot_code',
            'validated_bot_code_loaded_at',
            'validated_bot_amount',
        ]);

    }

    $prefilledAmount =
        request()->query('amount');

    if ($prefilledAmount !== null) {

        $prefilledAmount =
            is_numeric($prefilledAmount)
                ? (float) $prefilledAmount
                : null;

        if (
            $prefilledAmount !== null &&
            $prefilledAmount >= (float) $bot->min_amount &&
            $prefilledAmount <= (float) $bot->max_amount
        ) {
            session([
                'validated_bot_amount' =>
                    $prefilledAmount,
            ]);
        }
    }

    $prefilledAmount =
        session('validated_bot_amount');

    $bot->refresh();
    
    /*
 * Get the BotSetting belonging to this Bot.
 */
$botType = strtolower(
    trim(
        (string) $bot->bot_type
    )
);

$botSetting = BotSetting::where(
    'bot_type',
    $botType
)
    ->where(
        'is_active',
        true
    )
    ->first();

/*
 * Administrator-configured trading pairs.
 */
$supportedPairs = $botSetting
    ? $botSetting->supported_pairs
    : [];

/*
 * Administrator-configured durations.
 */
$durationOptions = $botSetting
    ? $botSetting->duration_options
    : [];

    /*
     * Selected bot subscription.
     */
    $userSubscription = $user
        ->botSubscriptions()
        ->where('bot_id', $bot->id)
        ->where('status', 'active')
        ->latest()
        ->first();

    /*
     * Refresh running simulation.
     */
    $runningSubscription = $user
        ->botSubscriptions()
        ->where(
            'simulation_status',
            'running'
        )
        ->latest('simulation_started_at')
        ->first();

    if ($runningSubscription) {

        $this->refreshSimulation(
            $runningSubscription
        );

        $runningSubscription->refresh();
    }

    /*
     * Only ONE simulation card is supplied to Blade.
     */
    $simulationSubscription = $user
        ->botSubscriptions()
        ->whereIn(
            'simulation_status',
            [
                'running',
                'completed',
                'terminated',
            ]
        )
        ->with('bot')
        ->latest('simulation_started_at')
        ->first();

    /*
     * USDT TRC20 balance.
     */
    $usdtBalanceRecord = $user
        ->cryptoBalances()
        ->whereHas(
            'cryptocurrency',
            function ($query) {
                $query->where(
                    'symbol',
                    'usdt_trc20'
                );
            }
        )
        ->first();

    $usdtBalance =
        $usdtBalanceRecord
            ? $usdtBalanceRecord->balance
            : 0;

    /*
     * Active bot settings.
     */
    $botSettings = BotSetting::where(
    'is_active',
    true
)
    ->where(
        'bot_type',
        $botType
    )
    ->get();

    /*
     * Get validated Bot Code, if one exists.
     *
     * Do NOT redirect if there isn't one.
     */
    $validatedBotCodeId =
        session('validated_bot_code_id');

    $validatedBotCode = null;

    if ($validatedBotCodeId) {

        $validatedBotCode =
            BotCode::with('bot')
                ->where(
                    'id',
                    $validatedBotCodeId
                )
                ->where(
                    'is_active',
                    true
                )
                ->first();

        /*
         * If the stored code does not belong
         * to this bot, don't use it.
         */
        if (
            !$validatedBotCode ||
            !$validatedBotCode->isUsable() ||
            (int) $validatedBotCode->bot_id !==
                (int) $bot->id
        ) {

            $validatedBotCode = null;

            session()->forget([
                'validated_bot_code_id',
                'validated_bot_code',
                'validated_bot_code_loaded_at',
                'validated_bot_amount',
            ]);
        }
    }
    
    $isBotCodeMode =
    $validatedBotCode !== null;

    return view(
    'user.bots.show',
    [
        'bot' =>
            $bot,

        'userSubscription' =>
            $userSubscription,

        'userBalance' =>
            $usdtBalance,

        'simulationSubscription' =>
            $simulationSubscription,

        'botSettings' =>
            $botSetting,

        'supportedPairs' =>
            $supportedPairs,

        'durationOptions' =>
            $durationOptions,

        'botCode' =>
            $validatedBotCode,

        'prefilledAmount' =>
            $prefilledAmount,
    ]
);
}


    /**
     * ============================================================
     * SUBSCRIBE / LAUNCH BOT
     * ============================================================
     */
    public function subscribe(Request $request)
    {
        Log::info(
            'Starting bot subscription process',
            [
                'user_id' =>
                    auth()->id(),

                'request_data' =>
                    $request->all(),
            ]
        );

        $data = $request->validate(
            [
                'bot_type' =>
                    'required|string',

                'duration' =>
                    'required|string',

                'amount' =>
                    'required|numeric|min:0.00000001',

                'trading_pair' =>
                    'required|string',
            ]
        );

        $user = auth()->user();

        /*
         * Normalize bot type.
         */
        $botType = strtolower(
            trim(
                $data['bot_type']
            )
        );

        /*
         * Normalize pair.
         */
        $normalizedPair = strtoupper(
            trim(
                $data['trading_pair']
            )
        );

        /*
         * Bot setting.
         */
        $botSetting = BotSetting::where(
            'bot_type',
            $botType
        )
            ->where(
                'is_active',
                true
            )
            ->first();

        if (!$botSetting) {
            return response()->json(
                [
                    'success' => false,
                    'message' =>
                        'This bot is currently unavailable.',
                ],
                422
            );
        }

        /*
         * Active bot.
         */
        $bot = Bot::where(
            'bot_type',
            $botType
        )
            ->where(
                'status',
                'active'
            )
            ->first();

        if (!$bot) {
            return response()->json(
                [
                    'success' => false,
                    'message' =>
                        'This bot is currently unavailable.',
                ],
                422
            );
        }

        /*
         * Supported pairs.
         */
        $supportedPairs =
            $botSetting->supported_pairs ?? [];

        $normalizedSupportedPairs = collect(
            $supportedPairs
        )
            ->map(
                function ($pair) {
                    return strtoupper(
                        trim(
                            (string) $pair
                        )
                    );
                }
            )
            ->values()
            ->all();

        if (
            !empty($normalizedSupportedPairs)
            &&
            !in_array(
                $normalizedPair,
                $normalizedSupportedPairs,
                true
            )
        ) {
            return response()->json(
                [
                    'success' => false,
                    'message' =>
                        'The selected trading pair is not supported by this bot.',
                ],
                422
            );
        }

        /*
         * Duration validation.
         */
        $durationOptions =
            $botSetting->duration_options ?? [];

        $durationExists = collect(
            $durationOptions
        )
            ->contains(
                function ($option) use ($data) {

                    if (is_string($option)) {
                        return $option ===
                            $data['duration'];
                    }

                    if (is_array($option)) {
                        return
                            (
                                ($option['value'] ?? null)
                                ===
                                $data['duration']
                            )
                            ||
                            (
                                ($option['duration'] ?? null)
                                ===
                                $data['duration']
                            );
                    }

                    return false;
                }
            );

        if (
            !$durationExists
            &&
            !empty($durationOptions)
        ) {
            return response()->json(
                [
                    'success' => false,
                    'message' =>
                        'The selected duration is not supported by this bot.',
                ],
                422
            );
        }

        /*
         * Amount.
         */
        $amount = (float) $data['amount'];

        if (
            $amount < (float) $bot->min_amount
            ||
            $amount > (float) $bot->max_amount
        ) {
            return response()->json(
                [
                    'success' => false,
                    'message' =>
                        "Amount must be between {$bot->min_amount} and {$bot->max_amount}.",
                ],
                422
            );
        }

        /*
         * USDT TRC20.
         */
        $usdtTrc20 = Cryptocurrency::where(
            'symbol',
            'usdt_trc20'
        )->first();

        if (!$usdtTrc20) {
            return response()->json(
                [
                    'success' => false,
                    'message' =>
                        'USDT TRC20 is not configured.',
                ],
                500
            );
        }

        /*
         * Duration.
         */
        $durationSeconds =
            $this->durationToSeconds(
                $data['duration']
            );

        if ($durationSeconds <= 0) {
            return response()->json(
                [
                    'success' => false,
                    'message' =>
                        'Invalid bot duration.',
                ],
                422
            );
        }

        DB::beginTransaction();

        try {

            /*
             * Only ONE running simulation.
             */
            $runningSimulation = $user
                ->botSubscriptions()
                ->where(
                    'simulation_status',
                    'running'
                )
                ->where(
                    function ($query) {
                        $query
                            ->whereNull('expires_at')
                            ->orWhere(
                                'expires_at',
                                '>',
                                now()
                            );
                    }
                )
                ->latest('simulation_started_at')
                ->lockForUpdate()
                ->first();

            if ($runningSimulation) {

                DB::rollBack();

                return response()->json(
                    [
                        'success' => false,

                        'message' =>
                            'You already have an active bot trade running. Please wait until it is completed or terminated before launching another bot.',

                        'subscription_id' =>
                            $runningSimulation->id,
                    ],
                    422
                );
            }

            /*
             * Lock wallet.
             */
            $userBalance = UserCryptoBalance::where(
                'user_id',
                $user->id
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
                        'success' => false,
                        'message' =>
                            'USDT TRC20 wallet balance was not found.',
                    ],
                    422
                );
            }

            if (
                (float) $userBalance->balance
                <
                $amount
            ) {

                DB::rollBack();

                return response()->json(
                    [
                        'success' => false,
                        'message' =>
                            'Insufficient USDT balance.',
                    ],
                    422
                );
            }

            /*
             * Start / expiry.
             */
            $startedAt = now();

            $expiresAt = $startedAt
                ->copy()
                ->addSeconds(
                    $durationSeconds
                );

            
            /*
             * ====================================================
             * SERVER-SIDE FINAL P/L
             * ====================================================
             *
             * MANUAL MODE
             * -----------
             * Use the P/L range configured on the Bot.
             *
             * BOT CODE MODE
             * -------------
             * Use the P/L range configured on the validated
             * Bot Code.
             *
             * IMPORTANT:
             * The P/L range is NEVER accepted from the browser.
             * It is loaded directly from the database.
             */
            
            
            /*
             * ----------------------------------------------------
             * CHECK FOR A VALIDATED BOT CODE
             * ----------------------------------------------------
             */
            
            $validatedBotCodeId =
                session('validated_bot_code_id');
            
            $validatedBotCode = null;
            
            
            if ($validatedBotCodeId) {
            
                $validatedBotCode =
                    BotCode::where(
                        'id',
                        $validatedBotCodeId
                    )
                    ->where(
                        'is_active',
                        true
                    )
                    ->where(
                        'bot_id',
                        $bot->id
                    )
                    ->first();
            
            
                /*
                 * The code must still be usable.
                 */
                if (
                    $validatedBotCode &&
                    !$validatedBotCode->isUsable()
                ) {
            
                    $validatedBotCode = null;
            
                }
            
            }
            
            
            /*
             * ----------------------------------------------------
             * DETERMINE THE AUTHORITATIVE P/L RANGE
             * ----------------------------------------------------
             *
             * Bot Code mode:
             *     BotCode.min_final_profit
             *     BotCode.max_final_profit
             *
             * Manual mode:
             *     Bot.min_final_profit
             *     Bot.max_final_profit
             */
            
            if ($validatedBotCode) {
            
                /*
                 * BOT CODE MODE
                 */
            
                $minFinalProfit =
                    (float)
                    $validatedBotCode->min_final_profit;
            
                $maxFinalProfit =
                    (float)
                    $validatedBotCode->max_final_profit;
            
            } else {
            
                /*
                 * MANUAL MODE
                 */
            
                $minFinalProfit =
                    (float)
                    $bot->min_final_profit;
            
                $maxFinalProfit =
                    (float)
                    $bot->max_final_profit;
            
            }
            
            
            /*
             * ----------------------------------------------------
             * VALIDATE P/L RANGE
             * ----------------------------------------------------
             */
            
            if (
                $maxFinalProfit <
                $minFinalProfit
            ) {
            
                throw new \RuntimeException(
                    'Invalid final profit range configured for this bot.'
                );
            
            }
            
            
            /*
             * ----------------------------------------------------
             * GENERATE RANDOM FINAL P/L
             * ----------------------------------------------------
             *
             * Use integer units so that the generated result
             * remains precise to 8 decimal places.
             */
            
            $minUnits =
                (int)
                round(
                    $minFinalProfit *
                    100000000
                );
            
            
            $maxUnits =
                (int)
                round(
                    $maxFinalProfit *
                    100000000
                );
            
            
            $simulationTarget =
                round(
                    random_int(
                        $minUnits,
                        $maxUnits
                    ) /
                    100000000,
                    8
                );
            

            /*
             * Generate irregular minute movement.
             */
            $segments =
                $this->generateSimulationSegments(
                    $simulationTarget,
                    $durationSeconds,
                    $maxFinalProfit > 0
                        ? $amount
                        : null
                );

            /*
             * Deduct capital immediately.
             */
            $userBalance->decrement(
                'balance',
                $amount
            );

            /*
             * Create subscription.
             */
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

            /*
             * Create trade.
             */
            $botTrade =
                BotTrade::create(
                    [
                        'bot_id' =>
                            $bot->id,

                        'trading_pair' =>
                            $normalizedPair,

                        'action' =>
                            'simulation_start',

                        'amount' =>
                            $amount,

                        'price' =>
                            0,

                        'profit' =>
                            0,

                        'result' =>
                            null,

                        'metadata' =>
                        [
                            'simulation' =>
                                true,
                    
                            'duration' =>
                                $data['duration'],
                    
                            'duration_seconds' =>
                                $durationSeconds,
                    
                            'started_at' =>
                                $startedAt
                                    ->toDateTimeString(),
                    
                            /*
                             * Preserve the authoritative administrator
                             * P/L range used for this trade.
                             *
                             * This is important for Bot Code trades because
                             * the validated Bot Code session is cleared after
                             * the transaction commits.
                             */
                            'configured_min_profit' =>
                                round(
                                    (float) $minFinalProfit,
                                    8
                                ),
                    
                            'configured_max_profit' =>
                                round(
                                    (float) $maxFinalProfit,
                                    8
                                ),
                    
                            'simulation_target_profit' =>
                                round(
                                    (float) $simulationTarget,
                                    8
                                ),
                    
                            'notes' =>
                                'Simulated trading session.',
                    
                            'wallet_capital_deducted' =>
                                true,
                        ],
                    ]
                );

            /*
 * Attach trade to subscription.
 *
 * The subscription_profits table requires both
 * amount and profit to be populated.
 */
$subscription
    ->trades()
    ->attach(
        $botTrade->id,
        [
            'amount' =>
                round(
                    (float) $amount,
                    8
                ),

            'profit' =>
                0,
        ]
    );

            /*
             * Subscriber count.
             */
            $bot->increment(
                'total_subscribers'
            );

            DB::commit();

            /*
             * ====================================================
             * BOT CODE SESSION CLEANUP
             * ====================================================
             *
             * The subscription and trade have now been successfully
             * created and the database transaction has committed.
             *
             * Therefore, if this launch came through a validated
             * Bot Code, remove the temporary Bot Code session data.
             *
             * This is intentionally AFTER DB::commit() so that the
             * Bot Code is not cleared if the launch transaction fails.
             */
            session()->forget([
                'validated_bot_code_id',
                'validated_bot_code',
                'validated_bot_code_loaded_at',
                'validated_bot_amount',
            ]);
            
            
            /*
             * Launch email.
             */
            try {

                Mail::to(
                    $user->email
                )->queue(
                    new BotSubscriptionEmail(
                        $subscription,
                        'launched'
                    )
                );

            } catch (\Throwable $mailException) {

                Log::warning(
                    'Bot launch email could not be queued.',
                    [
                        'subscription_id' =>
                            $subscription->id,

                        'error' =>
                            $mailException->getMessage(),
                    ]
                );
            }

            $botName =
                $bot->name
                ??
                ucfirst($botType) . ' Bot';

            return response()->json(
                [
                    'success' =>
                        true,

                    'message' =>
                        "{$botName} has been activated successfully for {$data['duration']}.",

                    'subscription_id' =>
                        $subscription->id,
                ],
                200
            );

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error(
                'Subscription process failed.',
                [
                    'user_id' =>
                        $user->id,

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
                        'Failed to launch bot. Please try again.',
                ],
                500
            );
        }
    }


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


    /**
     * ============================================================
     * GENERATE IRREGULAR SIMULATION SEGMENTS
     * ============================================================
     */
    protected function generateSimulationSegments(
        float $targetProfit,
        int $durationSeconds,
        ?float $capitalBoundary = null
    ): array {

        $minutes =
            max(
                1,
                (int) ceil(
                    $durationSeconds / 60
                )
            );

        /*
         * One-minute simulation.
         */
        if ($minutes === 1) {

            return [
                [
                    'minute' =>
                        1,

                    'profit' =>
                        round(
                            $targetProfit,
                            8
                        ),

                    'cumulative_profit' =>
                        round(
                            $targetProfit,
                            8
                        ),
                ],
            ];
        }

        $raw = [];

        $direction =
            mt_rand(0, 1) === 1
                ? 1
                : -1;

        $streakLength = 0;

        for (
            $i = 0;
            $i < $minutes;
            $i++
        ) {

            /*
             * Change direction occasionally.
             */
            if (
                $i > 0
                &&
                (
                    mt_rand(1, 100) <= 28
                    ||
                    $streakLength >=
                        mt_rand(3, 6)
                )
            ) {

                $direction *= -1;

                $streakLength = 0;
            }

            /*
             * Base movement.
             */
            $magnitude =
                mt_rand(20, 140) / 100;

            /*
             * Larger movement sometimes.
             */
            if (
                mt_rand(1, 100) <= 15
            ) {

                $magnitude *=
                    mt_rand(140, 260) / 100;
            }

            /*
             * Slight noise.
             */
            $noise =
                mt_rand(80, 120) / 100;

            $raw[] =
                $magnitude *
                $noise *
                $direction;

            $streakLength++;
        }

        /*
         * Guarantee visible variation.
         */
        $raw[0] =
            abs($raw[0]);

        if ($minutes > 1) {
            $raw[1] =
                -abs($raw[1]);
        }

        if ($minutes >= 4) {

            $raw[$minutes - 2] =
                abs(
                    $raw[$minutes - 2]
                );

            $raw[$minutes - 1] =
                -abs(
                    $raw[$minutes - 1]
                );
        }

        $rawTotal =
            array_sum($raw);

        /*
         * Avoid zero raw total.
         */
        if (
            abs($rawTotal) <
            0.00000001
        ) {

            $raw[$minutes - 1] +=
                $targetProfit >= 0
                    ? 10
                    : -10;

            $rawTotal =
                array_sum($raw);
        }

        /*
         * Align overall direction with target.
         */
        if (
            $targetProfit > 0
            &&
            $rawTotal < 0
        ) {

            $raw =
                array_map(
                    fn ($value) =>
                        -$value,
                    $raw
                );

            $rawTotal =
                -$rawTotal;
        }

        if (
            $targetProfit < 0
            &&
            $rawTotal > 0
        ) {

            $raw =
                array_map(
                    fn ($value) =>
                        -$value,
                    $raw
                );

            $rawTotal =
                -$rawTotal;
        }

        /*
         * Zero target.
         */
        if (
            abs($targetProfit) <
            0.00000001
        ) {

            $rawTotal =
                array_sum($raw);

            if (
                abs($rawTotal) >
                0.00000001
            ) {

                $raw[$minutes - 1] -=
                    $rawTotal;
            }

            $rawTotal = 0;
        }

        /*
         * Work in integer 1e-8 units.
         */
        $targetUnits =
            (int) round(
                $targetProfit *
                100000000
            );

        $rawTotalUnits =
            array_sum(
                array_map(
                    function ($value) {
                        return (int) round(
                            $value *
                            100000000
                        );
                    },
                    $raw
                )
            );

        if (
            $rawTotalUnits === 0
        ) {

            $rawTotalUnits =
                $targetUnits !== 0
                    ? $targetUnits
                    : 1;
        }

        $segments = [];

        $cumulativeUnits = 0;

        $allocatedUnits = 0;

        for (
            $i = 0;
            $i < $minutes;
            $i++
        ) {

            if (
                $i ===
                $minutes - 1
            ) {

                /*
                 * Exact final remainder.
                 */
                $segmentUnits =
                    $targetUnits -
                    $allocatedUnits;

            } else {

                $segmentUnits =
                    (int) round(
                        (
                            $raw[$i]
                            /
                            (
                                $rawTotalUnits /
                                100000000
                            )
                        )
                        *
                        $targetUnits
                    );

                $allocatedUnits +=
                    $segmentUnits;
            }
            
            /*
             * ============================================================
             * CAPITAL BOUNDARY FOR PROFIT-CAPABLE SIMULATIONS
             * ============================================================
             *
             * When the configured final P/L range contains a positive
             * value, the simulation may fluctuate freely.
             *
             * The only hard restriction is that neither:
             *
             *   1. an individual minute P/L, nor
             *   2. the accumulated/current P/L
             *
             * may reach the capital-exhaustion termination point.
             */
            if (
                $capitalBoundary !== null
                &&
                $capitalBoundary > 0
            ) {
            
                /*
                 * Keep the cumulative P/L slightly ABOVE the exact
                 * capital-exhaustion threshold.
                 *
                 * Example:
                 *
                 * Investment = 20
                 *
                 * Capital termination:
                 *     <= -20.00000000
                 *
                 * Safe simulation floor:
                 *     -19.99999999
                 */
                $capitalFloorUnits =
                    (int) round(
                        -$capitalBoundary *
                        100000000
                    ) + 1;
            
                /*
                 * ========================================================
                 * INDIVIDUAL MINUTE P/L
                 * ========================================================
                 *
                 * A single minute cannot lose the entire investment or
                 * more.
                 */
                if (
                    $segmentUnits <
                    $capitalFloorUnits
                ) {
            
                    $segmentUnits =
                        $capitalFloorUnits;
                }
            
                /*
                 * ========================================================
                 * ACCUMULATED / CURRENT P/L
                 * ========================================================
                 */
                $proposedCumulativeUnits =
                    $cumulativeUnits +
                    $segmentUnits;
            
                /*
                 * Prevent the accumulated P/L from reaching the
                 * capital-exhaustion boundary.
                 */
                if (
                    $proposedCumulativeUnits <
                    $capitalFloorUnits
                ) {
            
                    $segmentUnits =
                        $capitalFloorUnits -
                        $cumulativeUnits;
                }
            }

            $cumulativeUnits +=
                $segmentUnits;

            $segments[] =
                [
                    'minute' =>
                        $i + 1,

                    'profit' =>
                        round(
                            $segmentUnits /
                            100000000,
                            8
                        ),

                    'cumulative_profit' =>
                        round(
                            $cumulativeUnits /
                            100000000,
                            8
                        ),
                ];
        }

        /*
         * Absolute final guarantee.
         */
        if (!empty($segments)) {

            $lastIndex =
                count($segments) - 1;

            $previousCumulative =
                $lastIndex > 0
                    ? $segments[
                        $lastIndex - 1
                    ][
                        'cumulative_profit'
                    ]
                    : 0;

            $finalProfit =
                $targetProfit -
                $previousCumulative;

            $segments[$lastIndex]['profit'] =
                round(
                    $finalProfit,
                    8
                );

            $segments[$lastIndex][
                'cumulative_profit'
            ] =
                round(
                    $targetProfit,
                    8
                );
        }

        return $segments;
    }


    /**
     * ============================================================
     * AUTOMATIC CAPITAL EXHAUSTION TERMINATION
     * ============================================================
     */
    protected function settleSimulationTermination(
        BotSubscription $subscription,
        float $finalProfit,
        int $completedMinutes,
        int $elapsedSeconds,
        bool $capitalExhausted = false
    ): void {

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

            $finalProfit =
                round(
                    $finalProfit,
                    8
                );

            /*
             * Find trade.
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

            $metadata =
                $trade &&
                is_array($trade->metadata)
                    ? $trade->metadata
                    : [];

            /*
             * Duplicate settlement protection.
             */
            if (
                !empty(
                    $metadata['wallet_settled']
                )
            ) {

                DB::rollBack();

                return;
            }

            /*
             * Capital is already exhausted.
             *
             * Therefore wallet credit = 0.
             */
            $walletCredit = 0;

            /*
             * Mark subscription terminated.
             */
            $lockedSubscription->current_profit =
                $finalProfit;

            $lockedSubscription->total_profit =
                $finalProfit;

            $lockedSubscription->simulation_status =
                'terminated';

            $lockedSubscription->simulation_completed_at =
                now();

            $lockedSubscription->status =
                'terminated';

            $lockedSubscription->save();

            /*
             * Update trade.
             */
            if ($trade) {

                $metadata['terminated'] =
                    true;

                $metadata['terminated_by'] =
                    'capital_exhaustion';

                $metadata['capital_exhausted'] =
                    $capitalExhausted;

                $metadata['terminated_at'] =
                    now()->toDateTimeString();

                $metadata['final_profit'] =
                    $finalProfit;

                $metadata['wallet_credit'] =
                    $walletCredit;

                $metadata['capital_returned'] =
                    0;

                $metadata[
                    'terminated_elapsed_seconds'
                ] =
                    $elapsedSeconds;

                $metadata['completed_minutes'] =
                    $completedMinutes;

                $metadata['wallet_settled'] =
                    true;

                $metadata['capital_loss'] =
                    true;

                $trade->update(
                    [
                        'profit' =>
                            $finalProfit,

                        'result' =>
                            'loss',

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
                            'terminated',
                            [
                                'final_profit' =>
                                    $finalProfit,

                                'wallet_credit' =>
                                    0,

                                'capital_returned' =>
                                    0,

                                'capital_exhausted' =>
                                    true,

                                'terminated_at' =>
                                    now(),

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
                    'Capital exhaustion termination email could not be queued.',
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
                'Automatic capital exhaustion settlement failed.',
                [
                    'subscription_id' =>
                        $subscription->id,

                    'user_id' =>
                        $subscription->user_id,

                    'error' =>
                        $e->getMessage(),

                    'trace' =>
                        $e->getTraceAsString(),
                ]
            );
        }
    }


    /**
     * ============================================================
     * GET TRADE METADATA
     * ============================================================
     */
    protected function getTradeMetadata(
        BotSubscription $subscription
    ): array {

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

        if (!$tradeId) {
            return [];
        }

        $trade =
            BotTrade::find($tradeId);

        if (!$trade) {
            return [];
        }

        return is_array($trade->metadata)
            ? $trade->metadata
            : [];
    }


    /**
     * ============================================================
     * COMPLETED MINUTES FOR TERMINATED TRADE
     * ============================================================
     */
    protected function completedMinutesForSubscription(
        BotSubscription $subscription
    ): int {

        $metadata =
            $this->getTradeMetadata(
                $subscription
            );

        if (
            isset(
                $metadata[
                    'completed_minutes'
                ]
            )
        ) {

            return (int)
                $metadata[
                    'completed_minutes'
                ];
        }

        if (
            !$subscription->simulation_started_at
        ) {
            return 0;
        }

        $elapsed =
            $subscription
                ->simulation_started_at
                ->diffInSeconds(
                    $subscription
                        ->simulation_completed_at
                    ?? now()
                );

        return min(
            count(
                $subscription
                    ->simulation_segments
                ?? []
            ),
            (int) floor(
                $elapsed / 60
            )
        );
    }


    /**
     * ============================================================
     * DURATION CONVERTER
     * ============================================================
     */
    protected function durationToSeconds(
        string $duration
    ): int {

        $duration =
            strtolower(
                trim($duration)
            );

        if (
            !preg_match(
                '/^(\d+)\s*([mhd])$/',
                $duration,
                $matches
            )
        ) {
            return 0;
        }

        $value =
            (int) $matches[1];

        $unit =
            $matches[2];

        return match ($unit) {

            'm' =>
                $value * 60,

            'h' =>
                $value * 60 * 60,

            'd' =>
                $value * 24 * 60 * 60,

            default =>
                0,
        };
    }


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


    /**
     * ============================================================
     * SUBSCRIPTION PROFITS
     * ============================================================
     */
    public function profits(
        BotSubscription $subscription
    ) {
        if (
            (int) $subscription->user_id
            !==
            (int) auth()->id()
        ) {
            abort(403);
        }

        $profits =
            $subscription
                ->profits()
                ->with('trade')
                ->latest()
                ->paginate(20);

        return view(
            'user.bots.profits',
            compact(
                'subscription',
                'profits'
            )
        );
    }


    /**
     * ============================================================
     * BOT HISTORY
     * ============================================================
     */
    public function history()
    {
        $trades =
            BotTrade::with('bot')
                ->whereHas(
                    'subscriptions',
                    function ($query) {

                        $query->where(
                            'user_id',
                            auth()->id()
                        );
                    }
                )
                ->latest()
                ->paginate(10);

        return view(
            'user.bots.history',
            compact(
                'trades'
            )
        );
    }


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


    /**
     * ============================================================
     * BOT STATISTICS
     * ============================================================
     */
    public function stats(
        Bot $bot
    ) {

        $stats =
            [
                'total_profit' =>
                    $bot->total_profit,

                'total_subscribers' =>
                    $bot->total_subscribers,

                'last_trade' =>
                    $bot->last_trade_at
                        ?->diffForHumans(),

                'current_pair' =>
                    $bot->trading_pair,

                'recent_trades' =>
                    $bot
                        ->trades()
                        ->latest()
                        ->take(5)
                        ->get()
                        ->map(
                            fn ($trade) => [
                                'action' =>
                                    $trade->action,

                                'amount' =>
                                    $trade->amount,

                                'price' =>
                                    $trade->price,

                                'profit' =>
                                    $trade->profit,

                                'time' =>
                                    $trade
                                        ->created_at
                                        ->diffForHumans(),
                            ]
                        ),
            ];

        return response()->json(
            $stats
        );
    }
}

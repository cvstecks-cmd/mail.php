<x-user-layout :title="'Bots'">
    <!-- Header -->
    <div class="top">
        <div class="left-icons">
            <a href="{{ route('dashboard') }}" class="icon-btn"><i class='bx bx-arrow-back'></i></a>
        </div>
        
       
        
        <div class="title">Trading Bots</div>
        <div class="icon-btn"></div>
    </div>
    
     <!-- =========================================================
     BOT CODE LOADER
========================================================== -->

<div
    class="bg-[#1b1b1d] border border-white/5 rounded-2xl p-5 mb-5"
    id="botCodeLoader"
>

    <div class="flex items-center justify-between gap-3 mb-4">

        <div>

            <div class="flex items-center gap-2">

                <div
                    class="w-9 h-9 rounded-xl flex items-center justify-center
                           bg-[#00ff85]/10 border border-[#00ff85]/20"
                >
                    <i
                        class="bx bx-key text-[#00ff85] text-lg"
                    ></i>
                </div>

                <div>

                    <h3
                        class="text-sm font-bold text-white"
                    >
                        Activate Bot Code
                    </h3>

                    <p
                        class="text-[10px] text-gray-500 mt-1"
                    >
                        Enter your authorized Bot Code to unlock a bot configuration.
                    </p>

                </div>

            </div>

        </div>

        <span
            id="botCodeStatusBadge"
            class="hidden text-[9px] uppercase font-bold
                   px-2.5 py-1 rounded-lg"
        ></span>

    </div>


    <form
        id="botCodeForm"
        autocomplete="off"
    >

        @csrf

        <div class="flex flex-col md:flex-row gap-3">

            <div class="flex-1">

                <div class="relative">

                    <i
                        class="bx bx-barcode-reader absolute left-4 top-1/2
                               -translate-y-1/2 text-gray-500"
                    ></i>

                    <input
                        type="text"
                        id="botCodeInput"
                        name="code"
                        maxlength="8"
                        spellcheck="false"
                        autocomplete="off"
                        autocapitalize="characters"
                        placeholder="Enter Bot Code"
                        class="w-full bg-[#262628] text-white
                               border border-white/5 rounded-xl
                               pl-11 pr-4 py-3 text-sm font-semibold
                               tracking-wider outline-none
                               transition-all duration-200
                               focus:border-[#00ff85]/50
                               focus:ring-1 focus:ring-[#00ff85]/20"
                    >

                </div>


                <div
                    id="botCodeFeedback"
                    class="min-h-[18px] mt-2 text-[10px]"
                ></div>

            </div>


            <button
                type="submit"
                id="loadBotCodeButton"
                disabled
                class="md:w-40 h-[46px] rounded-xl
                       bg-gray-700 text-gray-400
                       font-bold text-xs uppercase
                       tracking-wide
                       transition-all duration-200
                       cursor-not-allowed
                       disabled:opacity-60"
            >

                <span
                    id="loadBotCodeButtonText"
                >
                    Load Code
                </span>

            </button>

        </div>

    </form>


    <!-- CODE RESULT -->

    <div
        id="loadedBotCodePanel"
        class="hidden mt-5"
    >

        <div
            class="rounded-2xl border border-[#00ff85]/20
                   bg-[#00ff85]/5 p-4"
        >

            <div
                class="flex items-center gap-2 mb-4"
            >

                <div
                    class="w-8 h-8 rounded-full
                           bg-[#00ff85]/10
                           flex items-center justify-center"
                >
                    <i
                        class="bx bx-check text-[#00ff85]"
                    ></i>
                </div>

                <div>

                    <div
                        class="text-xs font-bold text-[#00ff85]"
                    >
                        BOT CODE VERIFIED
                    </div>

                    <div
                        id="loadedBotCode"
                        class="text-[10px] text-gray-400 mt-0.5"
                    ></div>

                </div>

            </div>


            <div
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3"
            >

                <div
                    class="bg-[#1b1b1d] rounded-xl p-3"
                >

                    <div
                        class="text-[9px] uppercase text-gray-500"
                    >
                        Bot
                    </div>

                    <div
                        id="loadedBotName"
                        class="text-xs font-bold text-white mt-1"
                    >
                        —
                    </div>

                </div>


                <div
                    class="bg-[#1b1b1d] rounded-xl p-3"
                >

                    <div
                        class="text-[9px] uppercase text-gray-500"
                    >
                        Trading Pair
                    </div>

                    <div
                        id="loadedBotPair"
                        class="text-xs font-bold text-white mt-1"
                    >
                        —
                    </div>

                </div>


                <div
                    class="bg-[#1b1b1d] rounded-xl p-3"
                >

                    <div
                        class="text-[9px] uppercase text-gray-500"
                    >
                        Duration
                    </div>

                    <div
                        id="loadedBotDuration"
                        class="text-xs font-bold text-white mt-1"
                    >
                        —
                    </div>

                </div>
                
                <div class="bg-[#1b1b1d] rounded-xl p-3">

                    <div class="text-[9px] uppercase text-gray-500">
                        Investment Amount
                    </div>
                
                    <div class="relative mt-1">
                
                        <span class="absolute left-3 top-1/2 -translate-y-1/2
                                     text-[#00ff85] text-xs font-bold">
                            $
                        </span>
                
                        <input
                            type="number"
                            id="loadedBotAmount"
                            min="0.00000001"
                            step="0.00000001"
                            placeholder="Enter amount"
                            class="w-full bg-[#262628]
                                   border border-white/5
                                   rounded-lg
                                   pl-7 pr-2 py-2
                                   text-xs font-bold text-white
                                   outline-none
                                   focus:border-[#00ff85]/50"
                        >
                
                    </div>

    <div
        id="loadedBotAmountHint"
        class="text-[9px] text-gray-500 mt-1"
    ></div>

</div>

            </div>


            <a
                id="continueToBotButton"
                href="#"
                class="mt-4 w-full flex items-center justify-center
                       gap-2 py-3 rounded-xl
                       bg-[#00ff85] text-black
                       font-extrabold text-xs uppercase
                       tracking-wide
                       hover:brightness-110
                       transition-all"
            >

                <span>
                    Continue to Bot
                </span>

                <i
                    class="bx bx-right-arrow-alt text-lg"
                ></i>

            </a>

        </div>

    </div>

</div>

    <main class="w-full mx-auto p-4 pb-24 md:pb-4 text-white" style="padding-top: 0;">

        @php
            $botGradients = [
                'grid'      => ['from'=>'#f7931a','to'=>'#e8750a','icon'=>'bx-grid-alt'],
                'dca'       => ['from'=>'#627eea','to'=>'#3a55c4','icon'=>'bx-trending-down'],
                'arbitrage' => ['from'=>'#9945ff','to'=>'#14f195','icon'=>'bx-transfer'],
                'scalping'  => ['from'=>'#00c9a7','to'=>'#00879a','icon'=>'bx-fast-forward'],
                'momentum'  => ['from'=>'#f3ba2f','to'=>'#c89b08','icon'=>'bx-rocket'],
            ];
        @endphp

        <!-- Bot Packages -->
        <div style="margin-bottom: 20px;">
            <div class="section-title" style="margin: 0 0 12px 0;">Choose a Bot</div>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach($bots as $bot)
                    @php
                        $grad = $botGradients[$bot->bot_type] ?? ['from'=>'#444','to'=>'#222','icon'=>'bx-bot'];
                        $profitRate = $bot->total_profit > 0 ? number_format($bot->total_profit, 2) : '—';
                    @endphp
                    <div style="background: var(--card); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px; padding: 16px; display: flex; align-items: center; gap: 14px;">
                        <!-- Icon -->
                        <div style="width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, {{ $grad['from'] }}, {{ $grad['to'] }}); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class='bx {{ $grad['icon'] }}' style="font-size: 26px; color: #fff;"></i>
                        </div>
                        <!-- Info -->
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-weight: 700; font-size: 15px;">{{ $bot->name }}</div>
                            <div style="color: var(--muted); font-size: 12px; margin-top: 2px;">{{ strtoupper($bot->bot_type) }} · {{ $bot->trading_pair }}</div>
                            <div style="display: flex; gap: 10px; margin-top: 8px; flex-wrap: wrap;">
                                <span style="font-size: 11px; background: rgba(0,255,133,0.08); color: var(--accent); padding: 3px 8px; border-radius: 6px; font-weight: 600;">
                                    ${{ number_format($bot->min_amount, 0) }} – ${{ number_format($bot->max_amount, 0) }}
                                </span>
                                <span style="font-size: 11px; background: var(--chip); color: var(--muted); padding: 3px 8px; border-radius: 6px;">
                                    {{ $bot->duration }}
                                </span>
                                <span style="font-size: 11px; background: var(--chip); color: var(--muted); padding: 3px 8px; border-radius: 6px;">
                                    {{ $bot->total_subscribers }} traders
                                </span>
                            </div>
                        </div>
                        <!-- Launch -->
                        <a href="{{ route('bots.show', $bot->id) }}" style="background: var(--accent); color: #000; font-weight: 700; font-size: 12px; padding: 8px 14px; border-radius: 10px; text-decoration: none; white-space: nowrap; flex-shrink: 0;">
                            Launch
                        </a>
                    </div>
                @endforeach
                @if($bots->isEmpty())
                    <div style="text-align: center; padding: 30px; color: var(--muted); font-size: 13px;">
                        <i class='bx bx-bot' style="font-size: 40px; display: block; margin-bottom: 10px;"></i>
                        No active bots available right now.
                    </div>
                @endif
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 gap-3 mb-6">
            <div class="bg-[#1b1b1d] border border-white/5 rounded-2xl p-4 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-muted">Active Subs</span>
                    <i class='bx bx-play-circle text-accent text-lg'></i>
                </div>
                <div class="text-lg font-bold text-white">{{ $userSubscriptions->count() }}</div>
            </div>
            <div class="bg-[#1b1b1d] border border-white/5 rounded-2xl p-4 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-muted">Total Invested</span>
                    <i class='bx bx-wallet text-accent text-lg'></i>
                </div>
                <div class="text-lg font-bold text-white">${{ number_format($totalInvestment, 2) }}</div>
            </div>
            <div class="bg-[#1b1b1d] border border-white/5 rounded-2xl p-4 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-muted">Win Rate</span>
                    <i class='bx bx-bullseye text-accent text-lg'></i>
                </div>
                <div class="text-lg font-bold text-white">{{ $winRate ?? '0' }}%</div>
            </div>
            <div class="bg-[#1b1b1d] border border-white/5 rounded-2xl p-4 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-muted">Total Profit</span>
                    <i class='bx bx-trending-up text-accent text-lg'></i>
                </div>
                <div class="text-lg font-bold text-accent">${{ number_format($totalProfit, 2) }}</div>
            </div>
        </div>



        <!-- Filter and Header Section -->
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
            <h3 style="font-size:14px; font-weight:700; color:var(--text); margin:0;">Bot History</h3>
            <div style="display:flex; gap:6px;">
                <button onclick="filterTrades(event, 'all')" class="trade-filter-btn" style="padding:4px 10px; border-radius:8px; font-size:11px; font-weight:700; background:var(--accent); color:#0b0c0d; border:none; cursor:pointer;">All</button>
                <button onclick="filterTrades(event, 'active')" class="trade-filter-btn" style="padding:4px 10px; border-radius:8px; font-size:11px; font-weight:600; background:var(--chip); color:var(--muted); border:none; cursor:pointer;">Active</button>
                <button onclick="filterTrades(event, 'completed')" class="trade-filter-btn" style="padding:4px 10px; border-radius:8px; font-size:11px; font-weight:600; background:var(--chip); color:var(--muted); border:none; cursor:pointer;">Completed</button>
            </div>
        </div>

        <!-- Trade History List Container -->
        <div class="space-y-3" id="tradesContainer">
            <!-- Active Subscriptions -->
            @foreach($userSubscriptions as $subscription)
                <div class="bg-[#1b1b1d] border border-white/5 p-4 rounded-xl trade-row" data-type="active">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h4 class="text-sm font-semibold text-white">{{ $subscription->bot->name }}</h4>
                            <span class="text-[11px] text-muted">{{ $subscription->subscribed_at ? $subscription->subscribed_at->format('M d, Y') : 'Active' }}</span>
                        </div>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20">Active</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mt-2 pt-2 border-t border-white/5">
                        <div>
                            <span class="text-[10px] text-muted uppercase">Invested</span>
                            <p class="text-xs font-medium text-white">${{ number_format($subscription->amount, 2) }}</p>
                        </div>
                        <div>
                            <span class="text-[10px] text-muted uppercase">Result</span>
                            <p class="text-xs font-medium text-yellow-500 flex items-center gap-1">
                                <i class='bx bx-loader animate-spin'></i> Pending
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Completed Trades -->
            @foreach($trades as $trade)
                <div class="bg-[#1b1b1d] border border-white/5 p-4 rounded-xl trade-row" data-type="completed">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h4 class="text-sm font-semibold text-white">{{ $trade->bot->name }}</h4>
                            <span class="text-[11px] text-muted">{{ $trade->created_at->format('M d, Y') }}</span>
                        </div>
                        <span style="font-size:10px; font-weight:700; padding:2px 8px; border-radius:20px; background:rgba(0,255,133,0.1); color:var(--accent); border:1px solid rgba(0,255,133,0.2);">Completed</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mt-2 pt-2 border-t border-white/5">
                        <div>
                            <span class="text-[10px] text-muted uppercase">Invested</span>
                            <p class="text-xs font-medium text-white">${{ number_format($trade->amount, 2) }}</p>
                        </div>
                        <div>
                            <span class="text-[10px] text-muted uppercase">Profit/Loss</span>
                            <p style="font-size:12px; font-weight:600; color:{{ $trade->result === 'win' ? 'var(--accent)' : 'var(--danger)' }}">
                                {{ $trade->result === 'win' ? '+' : '-' }}${{ number_format(abs($trade->profit), 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach

            @if($userSubscriptions->isEmpty() && $trades->isEmpty())
                <div class="bg-[#1b1b1d] border border-white/5 p-8 rounded-xl text-center text-muted text-xs">
                    No trading bot history found.
                </div>
            @endif
        </div>
    </main>

    @push('scripts')
    <script>
        function filterTrades(event, type) {
            const rows = document.querySelectorAll('.trade-row');
            rows.forEach(row => {
                if (type === 'all') {
                    row.style.display = 'block';
                } else {
                    if (row.dataset.type === type) {
                        row.style.display = 'block';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });

            document.querySelectorAll('.trade-filter-btn').forEach(btn => {
                btn.style.background = 'var(--chip)';
                btn.style.color = 'var(--muted)';
                btn.style.fontWeight = '600';
            });
            
            if (event && event.currentTarget) {
                event.currentTarget.style.background = 'var(--accent)';
                event.currentTarget.style.color = '#0b0c0d';
                event.currentTarget.style.fontWeight = '700';
            }
        }

        // Auto-update trades every 30 seconds
        function updateTrades() {
            fetch('{{ route("bots.trades.update") }}')
                .then(response => response.json())
                .then(data => {
                    console.log('Trades updated dynamically', data);
                })
                .catch(error => console.error('Error updating trades:', error));
        }

        // Set interval for auto-update
        setInterval(updateTrades, 30000);
    </script>
    @endpush
    
    
    <script>
(function () {

    'use strict';

    /*
     * ============================================================
     * BOT CODE LOADER
     * ============================================================
     *
     * This script handles ONLY the Bot Code loader on the
     * Trading Bots listing page.
     *
     * Requirements:
     *
     * - Exactly 8 alphanumeric characters
     * - Automatically converts input to uppercase
     * - Displays READY when format is valid
     * - Enables Load Code only when format is valid
     * - Sends code securely to BotController@loadCode()
     * - Displays returned Bot / Pair / Duration / Amount
     * - Validates investment amount before Continue
     * - Passes the amount to /bots/show/{bot}
     *
     * ============================================================
     */


    /*
     * ------------------------------------------------------------
     * ELEMENT REFERENCES
     * ------------------------------------------------------------
     */

    const form =
        document.getElementById(
            'botCodeForm'
        );

    const input =
        document.getElementById(
            'botCodeInput'
        );

    const button =
        document.getElementById(
            'loadBotCodeButton'
        );

    const buttonText =
        document.getElementById(
            'loadBotCodeButtonText'
        );

    const feedback =
        document.getElementById(
            'botCodeFeedback'
        );

    const badge =
        document.getElementById(
            'botCodeStatusBadge'
        );

    const resultPanel =
        document.getElementById(
            'loadedBotCodePanel'
        );


    /*
     * ------------------------------------------------------------
     * SAFETY CHECK
     * ------------------------------------------------------------
     *
     * If the Bot Code panel is not present on the page,
     * simply stop this script.
     *
     * This prevents the loader from breaking the rest
     * of the Trading Bots page.
     * ------------------------------------------------------------
     */

    if (
        !form ||
        !input ||
        !button
    ) {

        console.warn(
            'Bot Code Loader: required elements were not found.'
        );

        return;
    }


    /*
     * ------------------------------------------------------------
     * BOT CODE FORMAT VALIDATION
     * ------------------------------------------------------------
     *
     * Exactly:
     *
     * 8 characters
     * Letters A-Z
     * Numbers 0-9
     *
     * Examples:
     *
     * ABCD1234  = VALID
     * AB12CD34  = VALID
     * ABC123    = INVALID
     * ABCDE12345 = INVALID
     * ABCD-123  = INVALID
     *
     * ------------------------------------------------------------
     */

    function isValidBotCodeFormat(
        value
    ) {

        const code =
            String(
                value || ''
            )
            .trim()
            .toUpperCase();

        return /^[A-Z0-9]{8}$/.test(
            code
        );
    }


    /*
     * ------------------------------------------------------------
     * READY STATE
     * ------------------------------------------------------------
     */

    function setReadyState() {

        /*
         * Input styling.
         */

        input.classList.remove(
            'border-red-500/60'
        );

        input.classList.add(
            'border-[#00ff85]/60'
        );

        input.style.boxShadow =
            '0 0 0 1px rgba(0,255,133,.12), 0 0 25px rgba(0,255,133,.08)';


        /*
         * Feedback.
         */

        if (feedback) {

            feedback.innerHTML = `
                <span class="text-[#00ff85] font-bold">
                    ✓ READY
                </span>

                <span class="text-gray-500 ml-1">
                    Code format is valid.
                </span>
            `;
        }


        /*
         * Status badge.
         */

        if (badge) {

            badge.classList.remove(
                'hidden'
            );

            badge.className =
                'text-[9px] uppercase font-bold px-2.5 py-1 rounded-lg bg-[#00ff85]/10 text-[#00ff85]';

            badge.textContent =
                'READY';
        }


        /*
         * Enable Load Code.
         */

        button.disabled =
            false;

        button.className =
            'md:w-40 h-[46px] rounded-xl bg-[#00ff85] text-black font-bold text-xs uppercase tracking-wide transition-all duration-200 hover:brightness-110';


        if (buttonText) {

            buttonText.textContent =
                'Load Code';
        }
    }


    /*
     * ------------------------------------------------------------
     * INVALID STATE
     * ------------------------------------------------------------
     */

    function setInvalidState(
        message
    ) {

        input.classList.remove(
            'border-[#00ff85]/60'
        );

        input.classList.add(
            'border-red-500/60'
        );

        input.style.boxShadow =
            '0 0 0 1px rgba(239,68,68,.08)';


        if (feedback) {

            feedback.innerHTML = `
                <span class="text-red-400 font-semibold">
                    ✕ ${message}
                </span>
            `;
        }


        if (badge) {

            badge.classList.remove(
                'hidden'
            );

            badge.className =
                'text-[9px] uppercase font-bold px-2.5 py-1 rounded-lg bg-red-500/10 text-red-400';

            badge.textContent =
                'INVALID';
        }


        /*
         * Keep Load Code disabled.
         */

        button.disabled =
            true;

        button.className =
            'md:w-40 h-[46px] rounded-xl bg-gray-700 text-gray-400 font-bold text-xs uppercase tracking-wide transition-all duration-200 cursor-not-allowed opacity-60';


        if (buttonText) {

            buttonText.textContent =
                'Load Code';
        }
    }


    /*
     * ------------------------------------------------------------
     * RESET STATE
     * ------------------------------------------------------------
     */

    function resetState() {

        input.classList.remove(
            'border-red-500/60',
            'border-[#00ff85]/60'
        );

        input.style.boxShadow =
            '';


        if (feedback) {

            feedback.innerHTML =
                '';
        }


        if (badge) {

            badge.classList.add(
                'hidden'
            );
        }


        button.disabled =
            true;

        button.className =
            'md:w-40 h-[46px] rounded-xl bg-gray-700 text-gray-400 font-bold text-xs uppercase tracking-wide transition-all duration-200 cursor-not-allowed opacity-60';


        if (buttonText) {

            buttonText.textContent =
                'Load Code';
        }
    }


    /*
     * ------------------------------------------------------------
     * INPUT EVENT
     * ------------------------------------------------------------
     */

    input.addEventListener(
        'input',
        function () {

            /*
             * Convert to uppercase and remove spaces.
             */

            const normalized =
                this.value
                    .toUpperCase()
                    .replace(
                        /\s+/g,
                        ''
                    );


            /*
             * Enforce maximum of 8 characters
             * even if the HTML maxlength is changed.
             */

            this.value =
                normalized.substring(
                    0,
                    8
                );


            /*
             * Hide previous successful result
             * whenever the user changes the code.
             */

            if (resultPanel) {

                resultPanel.classList.add(
                    'hidden'
                );
            }


            /*
             * Empty input.
             */

            if (
                !this.value
            ) {

                resetState();

                return;
            }


            /*
             * Valid 8-character code.
             */

            if (
                isValidBotCodeFormat(
                    this.value
                )
            ) {

                setReadyState();

                return;
            }


            /*
             * Invalid / incomplete code.
             */

            setInvalidState(
                'Bot Code must contain exactly 8 letters or numbers.'
            );

        }
    );


    /*
     * ------------------------------------------------------------
     * LOAD BOT CODE
     * ------------------------------------------------------------
     */

    form.addEventListener(
        'submit',
        async function (
            event
        ) {

            event.preventDefault();


            /*
             * Read and normalize code.
             */

            const code =
                input.value
                    .trim()
                    .toUpperCase();


            /*
             * Final client-side validation.
             */

            if (
                !isValidBotCodeFormat(
                    code
                )
            ) {

                setInvalidState(
                    'Please enter a valid 8-character Bot Code.'
                );

                return;
            }


            /*
             * Disable button while verifying.
             */

            button.disabled =
                true;


            button.className =
                'md:w-40 h-[46px] rounded-xl bg-gray-700 text-gray-400 font-bold text-xs uppercase tracking-wide transition-all duration-200 cursor-wait opacity-70';


            if (buttonText) {

                buttonText.innerHTML = `
                    <i class="bx bx-loader-alt bx-spin mr-1"></i>
                    Verifying...
                `;
            }


            try {

                /*
                 * ------------------------------------------------
                 * SEND CODE TO CONTROLLER
                 * ------------------------------------------------
                 */

                const response =
                    await fetch(
                        @json(
                            route(
                                'bots.load-code'
                            )
                        ),
                        {
                            method:
                                'POST',

                            headers:
                                {
                                    'Content-Type':
                                        'application/json',

                                    'Accept':
                                        'application/json',

                                    'X-Requested-With':
                                        'XMLHttpRequest',

                                    'X-CSRF-TOKEN':
                                        @json(
                                            csrf_token()
                                        )
                                },

                            body:
                                JSON.stringify(
                                    {
                                        code:
                                            code
                                    }
                                )
                        }
                    );


                /*
                 * Read JSON safely.
                 */

                let data;

                try {

                    data =
                        await response.json();

                } catch (
                    jsonError
                ) {

                    throw new Error(
                        'The server returned an invalid response.'
                    );
                }


                /*
                 * Server rejected code.
                 */

                if (
                    !response.ok ||
                    !data.success
                ) {

                    throw new Error(
                        data.message
                        ||
                        'Invalid or inactive Bot Code.'
                    );
                }


                /*
                 * ------------------------------------------------
                 * VERIFY REQUIRED RESPONSE DATA
                 * ------------------------------------------------
                 */

                if (
                    !data.bot ||
                    !data.bot.id
                ) {

                    throw new Error(
                        'The Bot Code response did not contain a valid bot.'
                    );
                }


                if (
                    !data.configuration
                ) {

                    throw new Error(
                        'The Bot Code response did not contain its configuration.'
                    );
                }


                /*
                 * ------------------------------------------------
                 * DISPLAY BOT CODE
                 * ------------------------------------------------
                 */

                const loadedBotCode =
                    document.getElementById(
                        'loadedBotCode'
                    );

                if (loadedBotCode) {

                    loadedBotCode.textContent =
                        code;
                }


                /*
                 * ------------------------------------------------
                 * DISPLAY BOT NAME
                 * ------------------------------------------------
                 */

                const loadedBotName =
                    document.getElementById(
                        'loadedBotName'
                    );

                if (loadedBotName) {

                    loadedBotName.textContent =
                        data.bot.name
                        ||
                        'Bot';
                }


                /*
                 * ------------------------------------------------
                 * DISPLAY TRADING PAIR
                 * ------------------------------------------------
                 */

                const loadedBotPair =
                    document.getElementById(
                        'loadedBotPair'
                    );

                if (loadedBotPair) {

                    loadedBotPair.textContent =
                        data.configuration
                            .trading_pair
                        ||
                        '—';
                }


                /*
                 * ------------------------------------------------
                 * DISPLAY DURATION
                 * ------------------------------------------------
                 */

                const loadedBotDuration =
                    document.getElementById(
                        'loadedBotDuration'
                    );

                if (loadedBotDuration) {

                    loadedBotDuration.textContent =
                        data.configuration
                            .duration
                        ||
                        '—';
                }


                /*
                 * ------------------------------------------------
                 * INVESTMENT AMOUNT
                 * ------------------------------------------------
                 */

                const amountInput =
                    document.getElementById(
                        'loadedBotAmount'
                    );

                const amountHint =
                    document.getElementById(
                        'loadedBotAmountHint'
                    );


                let minAmount =
                    Number(
                        data.amount?.min
                        ??
                        0
                    );

                let maxAmount =
                    Number(
                        data.amount?.max
                        ??
                        0
                    );


                /*
                 * Make sure invalid values don't break
                 * the client-side amount validation.
                 */

                if (
                    !Number.isFinite(
                        minAmount
                    )
                ) {

                    minAmount =
                        0;
                }


                if (
                    !Number.isFinite(
                        maxAmount
                    )
                ) {

                    maxAmount =
                        0;
                }


                if (amountInput) {

                    amountInput.min =
                        minAmount;

                    amountInput.max =
                        maxAmount;

                    /*
                     * Do not overwrite an amount the user may
                     * already have entered.
                     */

                    if (
                        !amountInput.value
                    ) {

                        amountInput.value =
                            '';
                    }
                }


                if (
                    amountHint
                ) {

                    if (
                        maxAmount > 0
                    ) {

                        amountHint.innerHTML =
                            `
                            <span class="text-gray-500">
                                Allowed:
                                <span class="text-[#00ff85] font-semibold">
                                    $${minAmount.toFixed(2)}
                                </span>
                                -
                                <span class="text-[#00ff85] font-semibold">
                                    $${maxAmount.toFixed(2)}
                                </span>
                            </span>
                            `;

                    } else {

                        amountHint.innerHTML =
                            '';
                    }
                }


                /*
                 * ------------------------------------------------
                 * CONTINUE TO BOT BUTTON
                 * ------------------------------------------------
                 */

                const continueButton =
                    document.getElementById(
                        'continueToBotButton'
                    );


                if (
                    continueButton
                ) {

                    /*
                     * Base URL.
                     */

                    continueButton.href =
                        @json(
                            url('/bots/show')
                        )
                        +
                        '/'
                        +
                        encodeURIComponent(
                            data.bot.id
                        );


                    /*
                     * Remove any previous click handler
                     * before assigning a fresh one.
                     */

                    continueButton.onclick =
                        null;


                    continueButton.onclick =
                        function (
                            event
                        ) {

                            /*
                             * Read amount at the moment the
                             * user clicks Continue.
                             */

                            const currentAmount =
                                amountInput
                                    ? Number(
                                        amountInput.value
                                    )
                                    : 0;


                            /*
                             * Amount is required.
                             */

                            if (
                                !Number.isFinite(
                                    currentAmount
                                )
                                ||
                                currentAmount <=
                                    0
                            ) {

                                event.preventDefault();


                                if (
                                    amountInput
                                ) {

                                    amountInput.focus();

                                    amountInput.classList.add(
                                        'border-red-500/60'
                                    );
                                }


                                if (
                                    amountHint
                                ) {

                                    amountHint.innerHTML =
                                        `
                                        <span class="text-red-400 font-semibold">
                                            Please enter your investment amount.
                                        </span>
                                        `;
                                }

                                return false;
                            }


                            /*
                             * Validate minimum amount.
                             */

                            if (
                                currentAmount <
                                minAmount
                            ) {

                                event.preventDefault();


                                if (
                                    amountInput
                                ) {

                                    amountInput.focus();

                                    amountInput.classList.add(
                                        'border-red-500/60'
                                    );
                                }


                                if (
                                    amountHint
                                ) {

                                    amountHint.innerHTML =
                                        `
                                        <span class="text-red-400 font-semibold">
                                            Minimum investment:
                                            $${minAmount.toFixed(2)}
                                        </span>
                                        `;
                                }

                                return false;
                            }


                            /*
                             * Validate maximum amount.
                             */

                            if (
                                maxAmount > 0
                                &&
                                currentAmount >
                                    maxAmount
                            ) {

                                event.preventDefault();


                                if (
                                    amountInput
                                ) {

                                    amountInput.focus();

                                    amountInput.classList.add(
                                        'border-red-500/60'
                                    );
                                }


                                if (
                                    amountHint
                                ) {

                                    amountHint.innerHTML =
                                        `
                                        <span class="text-red-400 font-semibold">
                                            Maximum investment:
                                            $${maxAmount.toFixed(2)}
                                        </span>
                                        `;
                                }

                                return false;
                            }


                            /*
                             * Amount is valid.
                             *
                             * Pass it to bots/show/{bot}.
                             */

                            const separator =
                                continueButton.href.includes(
                                    '?'
                                )
                                    ? '&'
                                    : '?';


                            /*
                             * Prevent duplicate amount parameters
                             * if Continue is clicked more than once.
                             */

                            const cleanHref =
                                continueButton.href
                                    .replace(
                                        /([?&])amount=[^&]*/g,
                                        ''
                                    );


                            continueButton.href =
                                cleanHref
                                +
                                separator
                                +
                                'amount='
                                +
                                encodeURIComponent(
                                    currentAmount.toFixed(
                                        8
                                    )
                                );


                            /*
                             * Allow navigation.
                             */

                            return true;
                        };
                }


                /*
                 * ------------------------------------------------
                 * SHOW RESULT PANEL
                 * ------------------------------------------------
                 */

                if (
                    resultPanel
                ) {

                    resultPanel.classList.remove(
                        'hidden'
                    );
                }


                /*
                 * Restore READY state.
                 */

                setReadyState();


                /*
                 * Make sure button remains enabled.
                 */

                button.disabled =
                    false;


                if (
                    buttonText
                ) {

                    buttonText.textContent =
                        'Load Code';
                }


            } catch (
                error
            ) {

                console.error(
                    'Bot Code Loader Error:',
                    error
                );


                /*
                 * Hide stale result.
                 */

                if (
                    resultPanel
                ) {

                    resultPanel.classList.add(
                        'hidden'
                    );
                }


                /*
                 * Show error.
                 */

                setInvalidState(
                    error.message
                    ||
                    'Unable to load Bot Code.'
                );


                /*
                 * Allow user to try again.
                 */

                button.disabled =
                    false;


                if (
                    buttonText
                ) {

                    buttonText.textContent =
                        'Load Code';
                }
            }

        }
    );


    /*
     * ------------------------------------------------------------
     * INITIAL STATE
     * ------------------------------------------------------------
     *
     * Always start disabled until a valid 8-character code
     * is entered.
     * ------------------------------------------------------------
     */

    if (
        input.value
    ) {

        const existingCode =
            input.value
                .trim()
                .toUpperCase();


        input.value =
            existingCode.substring(
                0,
                8
            );


        if (
            isValidBotCodeFormat(
                input.value
            )
        ) {

            setReadyState();

        } else {

            setInvalidState(
                'Invalid Bot Code format.'
            );
        }

    } else {

        resetState();
    }


})();
</script>
    
</x-user-layout>

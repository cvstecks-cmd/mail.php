<x-user-layout :title="'Launch Bots'">

    <style>

        .minute-pl-popup {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 100000;
            width: min(380px, calc(100vw - 32px));
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px;
            border-radius: 16px;
            color: #fff;
            transform: translateX(calc(100% + 40px));
            opacity: 0;
            transition:
                transform .3s ease,
                opacity .3s ease;
            pointer-events: auto;
        }

        .minute-pl-popup.minute-pl-visible {
            transform: translateX(0);
            opacity: 1;
        }

        .minute-pl-profit {
            background:
                linear-gradient(
                    135deg,
                    rgba(5, 55, 34, .98),
                    rgba(3, 27, 19, .98)
                );

            border:
                1px solid
                rgba(0, 255, 133, .42);

            box-shadow:
                0 0 12px
                rgba(0, 255, 133, .18),
                0 20px 60px
                rgba(0, 0, 0, .45);
        }

        .minute-pl-loss {
            background:
                linear-gradient(
                    135deg,
                    rgba(78, 8, 8, .98),
                    rgba(34, 4, 4, .98)
                );

            border:
                1px solid
                rgba(255, 60, 60, .42);

            box-shadow:
                0 0 12px
                rgba(190, 0, 0, .28),
                0 20px 60px
                rgba(0, 0, 0, .45);
        }

        .minute-pl-icon {
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: rgba(255,255,255,.08);
            font-size: 22px;
        }

        .minute-pl-content {
            min-width: 0;
            flex: 1;
        }

        .minute-pl-title {
            font-size: 13px;
            font-weight: 800;
        }

        .minute-pl-minute {
            margin-top: 2px;
            font-size: 10px;
            opacity: .65;
        }

        .minute-pl-amount {
            margin-top: 3px;
            font-size: 16px;
            font-weight: 900;
        }

        .minute-pl-close {
            margin-left: auto;
            align-self: flex-start;
            border: none;
            background: transparent;
            color: rgba(255,255,255,.65);
            font-size: 22px;
            line-height: 1;
            cursor: pointer;
        }

        #tradingview_chart {
            width: 100%;
            height: 430px;
            min-height: 350px;
            border-radius: 14px;
            overflow: hidden;
        }

        .order-row {
            display: grid;
            grid-template-columns:
                1fr
                1fr
                1fr;
            gap: 8px;
            padding: 5px 8px;
            font-size: 10px;
            border-radius: 6px;
        }

        .order-row:hover {
            background: rgba(255,255,255,.04);
        }

        .order-bid {
            color: #00ff85;
        }

        .order-ask {
            color: #ff5b6e;
        }

        .orderbook-empty {
            padding: 28px 10px;
            text-align: center;
            font-size: 10px;
            color: rgba(255,255,255,.4);
        }

        @media(max-width:640px) {

            .minute-pl-popup {
                top: 12px;
                right: 12px;
                left: 12px;
                width: auto;
            }

        }
        
        .terminate-bot-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 13px 16px;
            border: 1px solid rgba(255, 70, 70, .35);
            border-radius: 12px;
            background: rgba(120, 10, 10, .18);
            color: #ff6b6b;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            transition:
                background .2s ease,
                border-color .2s ease,
                opacity .2s ease;
        }
        
        .terminate-bot-btn:hover {
            background: rgba(150, 10, 10, .28);
            border-color: rgba(255, 70, 70, .55);
        }
        
        .terminate-bot-btn:disabled {
            opacity: .55;
            cursor: not-allowed;
        }

    </style>
    <style>

/* =========================================================
   PREMIUM SIMULATION DISPLAY
   ========================================================= */

#botSimulations {
    position: relative;
    scroll-margin-top: 90px;
}


/*
 * MAIN SIMULATION CARD
 */

#botSimulations .simulation-card {

    position: relative;

    overflow: hidden;

    border-radius: 22px !important;

    border: 1px solid
        rgba(0, 255, 133, .28) !important;

    background:
        radial-gradient(
            circle at 100% 0%,
            rgba(0, 255, 133, .10),
            transparent 38%
        ),
        radial-gradient(
            circle at 0% 100%,
            rgba(0, 190, 255, .07),
            transparent 38%
        ),
        linear-gradient(
            145deg,
            #171a1b 0%,
            #111315 55%,
            #151819 100%
        ) !important;

    box-shadow:
        0 0 0 1px
            rgba(0, 255, 133, .04),

        0 10px 35px
            rgba(0, 0, 0, .40),

        0 0 45px
            rgba(0, 255, 133, .07);

    transition:
        border-color .3s ease,
        box-shadow .3s ease,
        transform .3s ease;
}


/*
 * TOP ACCENT LINE
 */

#botSimulations .simulation-card::before {

    content: "";

    position: absolute;

    top: 0;
    left: 0;
    right: 0;

    height: 3px;

    background:
        linear-gradient(
            90deg,
            transparent,
            #00ff85,
            #00d9ff,
            #00ff85,
            transparent
        );

    background-size: 200% 100%;

    animation:
        simulationAccentMove
        4s linear infinite;

    opacity: .9;
}


/*
 * SUBTLE BACKGROUND GLOW
 */

#botSimulations .simulation-card::after {

    content: "";

    position: absolute;

    width: 180px;
    height: 180px;

    top: -90px;
    right: -70px;

    border-radius: 50%;

    background:
        rgba(0, 255, 133, .08);

    filter: blur(45px);

    pointer-events: none;
}


/*
 * ACTIVE SIMULATION
 */

#botSimulations .simulation-card[data-completed-minute] {

    animation:
        simulationCardGlow
        4s ease-in-out infinite;
}


/*
 * SIMULATION HEADER
 */

#botSimulations .simulation-card
> div:first-child {

    position: relative;

    z-index: 2;

    padding-bottom: 14px;

    border-bottom:
        1px solid
        rgba(255, 255, 255, .06);
}


/*
 * SIMULATION TITLE
 */

#botSimulations .simulation-card h3 {

    letter-spacing: .02em;

    font-size: 15px !important;

    font-weight: 900 !important;

    text-transform: uppercase;
}


/*
 * SIMULATION LABEL
 */

#botSimulations .simulation-card
.text-muted {

    color:
        rgba(255, 255, 255, .48);
}


/*
 * CURRENT P/L PANEL
 */

#botSimulations
.simulation-current-profit {

    position: relative;

    padding: 18px;

    border-radius: 16px;

    border:
        1px solid
        rgba(0, 255, 133, .18);

    background:
        linear-gradient(
            135deg,
            rgba(0, 255, 133, .10),
            rgba(0, 255, 133, .025)
        );

    box-shadow:
        inset 0 1px 0
            rgba(255,255,255,.035),

        0 0 25px
            rgba(0,255,133,.05);

    font-size: 28px !important;

    font-weight: 950 !important;

    letter-spacing: -.02em;

    transition:
        color .25s ease,
        transform .25s ease;
}


/*
 * TARGET P/L
 */

#botSimulations
.simulation-target-profit {

    font-weight: 800;

    color: #ffffff;

}


/*
 * STATUS BADGE
 */

#botSimulations
.simulation-status {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    min-width: 78px;

    padding: 6px 10px;

    border-radius: 999px;

    background:
        rgba(0, 255, 133, .09);

    border:
        1px solid
        rgba(0, 255, 133, .22);

    color:
        #00ff85;

    font-size: 9px;

    font-weight: 900;

    text-transform: uppercase;

    letter-spacing: .08em;
}


/*
 * RUNNING STATUS DOT
 */

#botSimulations
.simulation-status::before {

    content: "";

    width: 6px;
    height: 6px;

    margin-right: 6px;

    border-radius: 50%;

    background: #00ff85;

    box-shadow:
        0 0 0 0
        rgba(0,255,133,.45);

    animation:
        simulationStatusPulse
        1.8s infinite;
}


/*
 * PROGRESS TRACK
 */

#botSimulations
.simulation-progress-bar {

    position: relative;

    height: 7px !important;

    border-radius: 999px;

    background:
        linear-gradient(
            90deg,
            #00ff85,
            #00d9ff
        );

    box-shadow:
        0 0 12px
        rgba(0,255,133,.28),

        0 0 24px
        rgba(0,217,255,.12);

    transition:
        width .5s linear;
}


/*
 * PROGRESS CONTAINER
 */

#botSimulations
.simulation-progress-bar
.parent {

    overflow: hidden;

    border-radius: 999px;
}


/*
 * MINUTE TRADE ROWS
 */

#botSimulations
.simulation-segments > div {

    border:
        1px solid
        rgba(255,255,255,.045) !important;

    background:
        linear-gradient(
            90deg,
            rgba(255,255,255,.035),
            rgba(255,255,255,.018)
        ) !important;

    transition:
        transform .2s ease,
        border-color .2s ease,
        background .2s ease;
}


#botSimulations
.simulation-segments > div:hover {

    transform:
        translateX(3px);

    border-color:
        rgba(0,255,133,.18) !important;

    background:
        rgba(0,255,133,.045) !important;
}


/*
 * TERMINATE BUTTON
 */

#botSimulations
.terminate-bot-btn {

    position: relative;

    overflow: hidden;

    width: 100%;

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 9px;

    padding: 14px 18px;

    border:
        1px solid
        rgba(255, 70, 70, .42);

    border-radius: 14px;

    background:
        linear-gradient(
            135deg,
            rgba(125, 12, 12, .25),
            rgba(65, 5, 5, .20)
        );

    color:
        #ff7272;

    font-size: 12px;

    font-weight: 900;

    letter-spacing: .03em;

    text-transform: uppercase;

    cursor: pointer;

    transition:
        transform .2s ease,
        background .2s ease,
        border-color .2s ease,
        box-shadow .2s ease;
}

.simulation-status-running {
    color: var(--accent);
    white-space: nowrap;
    display: inline-block;
}


.simulation-status-completed {
    color: #9ca3af;
    white-space: nowrap;
    display: inline-block;
}


#botSimulations
.terminate-bot-btn:hover {

    transform:
        translateY(-1px);

    background:
        linear-gradient(
            135deg,
            rgba(170, 15, 15, .35),
            rgba(80, 5, 5, .28)
        );

    border-color:
        rgba(255, 90, 90, .75);

    box-shadow:
        0 0 20px
        rgba(255, 60, 60, .12);
}


#botSimulations
.terminate-bot-btn:active {

    transform:
        translateY(1px);
}


#botSimulations
.terminate-bot-btn i {

    font-size: 18px;
}


/*
 * TERMINATED BUTTON
 */

#botSimulations
.terminate-bot-btn:disabled {

    cursor: not-allowed;

    opacity: .75;

    color:
        #00ff85;

    border-color:
        rgba(0,255,133,.28);

    background:
        rgba(0,255,133,.07);

    box-shadow:
        none;
}


/*
 * MOBILE
 */

@media (max-width: 640px) {

    #botSimulations
    .simulation-card {

        border-radius: 18px !important;

        padding: 16px !important;

    }


    #botSimulations
    .simulation-current-profit {

        font-size: 24px !important;

        padding: 15px;

    }


    #botSimulations
    .terminate-bot-btn {

        padding: 13px 12px;

        font-size: 11px;

    }

}


/*
 * ANIMATIONS
 */

@keyframes simulationAccentMove {

    0% {
        background-position: 200% 0;
    }

    100% {
        background-position: -200% 0;
    }

}


@keyframes simulationCardGlow {

    0%,
    100% {

        box-shadow:
            0 0 0 1px
                rgba(0,255,133,.04),

            0 10px 35px
                rgba(0,0,0,.40),

            0 0 35px
                rgba(0,255,133,.05);

    }

    50% {

        box-shadow:
            0 0 0 1px
                rgba(0,255,133,.08),

            0 10px 35px
                rgba(0,0,0,.40),

            0 0 50px
                rgba(0,255,133,.10);

    }

}


@keyframes simulationStatusPulse {

    0% {

        box-shadow:
            0 0 0 0
            rgba(0,255,133,.45);

    }

    70% {

        box-shadow:
            0 0 0 7px
            rgba(0,255,133,0);

    }

    100% {

        box-shadow:
            0 0 0 0
            rgba(0,255,133,0);

    }

}

/* =========================================================
   SIMULATION AUTO-SCROLL FOCUS
   ========================================================= */

#botSimulations
.simulation-focus {

    animation:
        simulationFocusFlash
        1.8s ease-out;
}


@keyframes simulationFocusFlash {

    0% {

        transform:
            scale(.985);

        border-color:
            rgba(0,255,133,.90) !important;

        box-shadow:
            0 0 0 2px
                rgba(0,255,133,.18),

            0 0 55px
                rgba(0,255,133,.30);

    }

    45% {

        transform:
            scale(1.005);

        border-color:
            rgba(0,255,133,.75) !important;

        box-shadow:
            0 0 0 2px
                rgba(0,255,133,.12),

            0 0 65px
                rgba(0,255,133,.22);

    }

    100% {

        transform:
            scale(1);

        border-color:
            rgba(0,255,133,.28) !important;

    }

}

</style>


    <main
        class="w-full mx-auto p-4 pb-24 md:pb-4 text-white"
    >

        <!-- =========================================================
             HEADER
        ========================================================== -->

        <div class="top">

            <div class="left-icons">

                <a
                    href="{{ route('bots') }}"
                    class="icon-btn"
                >
                    <i class='bx bx-arrow-back'></i>
                </a>

            </div>

            <div class="title">
                Launch Bot
            </div>

            <div class="icon-btn"></div>

        </div>


        <!-- =========================================================
             TRADING PAIR + MARKET DATA
        ========================================================== -->

        <div
            class="bg-[#1b1b1d] border border-white/5 rounded-2xl p-4 mb-4"
        >

            <div
                class="flex items-center justify-between gap-3"
            >

                <div class="relative">

                    <button
                        type="button"
                        id="pairSelectorButton"
                        class="bg-[#262628] text-white px-4 py-2 rounded-xl flex items-center justify-between gap-2 border border-white/5 font-semibold text-xs transition-all hover:bg-white/5"
                    >

                        <img
                            id="pairIcon"
                            src="/btc-icon.png"
                            alt="BTC"
                            class="w-5 h-5 rounded-full"
                            onerror="
                                this.src='/images/crypto/default.png'
                            "
                        >

                        <span
                            id="selectedPairText"
                            class="text-xs"
                        >
                            BTC/USDT
                        </span>

                        <i
                            class='bx bx-chevron-down text-base'
                        ></i>

                    </button>


                    <div
                        id="pairDropdown"
                        class="hidden absolute left-0 mt-2 w-48 bg-[#1b1b1d] border border-white/5 rounded-xl shadow-2xl z-50"
                    >

                        <div
                            class="py-1.5 max-h-48 overflow-y-auto"
                        >

                            @php

                                $cryptos =
                                    auth()->user()
                                        ->getFormattedCryptoAssets();

                                $seenBaseSymbols = [];

                            @endphp


                            @foreach(
                                $cryptos
                                as $crypto
                            )

                                @php

                                    $fullSymbol =
                                        strtoupper(
                                            $crypto['symbol']
                                        );

                                    $baseSymbol =
                                        strpos(
                                            $fullSymbol,
                                            '_'
                                        ) !== false
                                            ? substr(
                                                $fullSymbol,
                                                0,
                                                strpos(
                                                    $fullSymbol,
                                                    '_'
                                                )
                                            )
                                            : $fullSymbol;

                                    if (
                                        $baseSymbol ===
                                        'USDT'
                                    ) {
                                        continue;
                                    }

                                    if (
                                        in_array(
                                            $baseSymbol,
                                            $seenBaseSymbols,
                                            true
                                        )
                                    ) {
                                        continue;
                                    }

                                    $seenBaseSymbols[] =
                                        $baseSymbol;

                                @endphp


                                <button
                                    type="button"
                                    class="pair-option w-full text-left px-4 py-2 text-xs text-gray-300 hover:text-white hover:bg-white/5 flex items-center gap-2"
                                    data-symbol="{{ $baseSymbol }}"
                                    data-icon="{{ $crypto['icon_url'] }}"
                                >

                                    <img
                                        src="{{ $crypto['icon_url'] }}"
                                        alt="{{ $baseSymbol }}"
                                        class="w-4 h-4 rounded-full"
                                        onerror="
                                            this.src='/images/crypto/default.png'
                                        "
                                    >

                                    {{ $baseSymbol }}/USDT

                                </button>

                            @endforeach

                        </div>

                    </div>

                </div>


                <div class="text-right min-w-0">

                    <span
                        id="baseVolumeLabel"
                        class="text-[10px] text-muted uppercase font-medium"
                    >
                        BTC Volume:
                    </span>

                    <p
                        id="baseVolume"
                        class="text-sm font-bold text-white"
                    >
                        --
                    </p>

                </div>

            </div>


            <div
                class="grid grid-cols-3 gap-3 mt-4 pt-3 border-t border-white/5"
            >

                <div>

                    <div
                        class="text-[9px] text-muted uppercase"
                    >
                        Last Price
                    </div>

                    <div
                        id="marketLastPrice"
                        class="text-xs font-bold text-white mt-1"
                    >
                        --
                    </div>

                </div>


                <div>

                    <div
                        class="text-[9px] text-muted uppercase"
                    >
                        24H Change
                    </div>

                    <div
                        id="marketChange"
                        class="text-xs font-bold mt-1"
                    >
                        --
                    </div>

                </div>


                <div>

                    <div
                        class="text-[9px] text-muted uppercase"
                    >
                        24H High
                    </div>

                    <div
                        id="marketHigh"
                        class="text-xs font-bold text-white mt-1"
                    >
                        --
                    </div>

                </div>

            </div>

        </div>


        <!-- =========================================================
             MARKET CHART
        ========================================================== -->

        <div
            class="bg-[#1b1b1d] border border-white/5 rounded-2xl p-3 mb-4"
        >

            <div
                class="flex items-center justify-between px-2 py-2"
            >

                <h3
                    class="text-sm font-bold text-white"
                >
                    Market Chart
                </h3>

                <span
                    id="chartSymbolLabel"
                    class="text-[10px] text-muted"
                >
                    BTC/USDT
                </span>

            </div>


            <div
                class="flex items-center gap-2 px-2 pb-2"
            >

                <button
                    type="button"
                    data-interval="1"
                    class="chart-interval"
                    style="
                        padding:4px 12px;
                        font-size:12px;
                        background:var(--chip);
                        border:none;
                        border-radius:8px;
                        color:var(--muted);
                        font-weight:600;
                        cursor:pointer;
                    "
                >
                    1m
                </button>

                <button
                    type="button"
                    data-interval="30"
                    class="chart-interval"
                    style="
                        padding:4px 12px;
                        font-size:12px;
                        background:var(--chip);
                        border:none;
                        border-radius:8px;
                        color:var(--muted);
                        font-weight:600;
                        cursor:pointer;
                    "
                >
                    30m
                </button>

                <button
                    type="button"
                    data-interval="60"
                    class="chart-interval chart-interval-active"
                    style="
                        padding:4px 12px;
                        font-size:12px;
                        background:var(--accent);
                        border:none;
                        border-radius:8px;
                        color:#0b0c0d;
                        font-weight:700;
                        cursor:pointer;
                    "
                >
                    1h
                </button>

            </div>


            <div id="tradingview_chart">

                <div
                    class="h-full flex items-center justify-center text-[10px] text-muted"
                >
                    Loading market chart...
                </div>

            </div>

        </div>


        <!-- =========================================================
             BOT PARAMETERS
        ========================================================== -->

        <div
            class="bg-[#1b1b1d] border border-white/5 rounded-2xl p-5 mb-4"
        >

            <h3
                class="text-sm font-bold text-white mb-4"
            >
                Bot Parameters
            </h3>


            {{-- =========================================================
     BOT PARAMETERS
     ========================================================= --}}

@php
    /*
     * Determine whether this page was reached through
     * a validated Bot Code.
     */
    $isBotCodeMode = $botCode !== null;

    /*
     * Manual Bot configuration.
     */
    $manualBotType = strtolower(
        trim(
            (string) $bot->bot_type
        )
    );

    /*
     * Bot Code configuration.
     */
    $codeBotType = $isBotCodeMode
        ? strtolower(
            trim(
                (string) $botCode->bot->bot_type
            )
        )
        : '';

    $codeTradingPair = $isBotCodeMode
        ? strtoupper(
            trim(
                (string) $botCode->trading_pair
            )
        )
        : '';

    $codeDuration = $isBotCodeMode
        ? (string) $botCode->duration
        : '';

    /*
     * Amount previously validated through Continue To Bot.
     */
    $codeAmount = $isBotCodeMode
        ? $prefilledAmount
        : null;
@endphp


<form
    id="subscribeForm"
    method="POST"
    action="{{ route('bots.subscribe') }}"
    class="space-y-4"
>

    @csrf


    {{-- =====================================================
         BOT TYPE
         ===================================================== --}}

    <div>

        <label
            class="block text-[10px] text-muted mb-1.5 uppercase font-semibold"
            for="botType"
        >
            Trading Bot Type
        </label>

        <select
            id="botType"
            name="bot_type"
            required
            disabled
            class="w-full bg-[#262628] border border-white/5 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-accent/40 disabled:opacity-70 disabled:cursor-not-allowed"
        >

            <option
                value="{{ $isBotCodeMode ? $codeBotType : $manualBotType }}"
                selected
            >
                {{ ucfirst($isBotCodeMode ? $codeBotType : $manualBotType) }} Bot
            </option>

        </select>


        {{--

            This is the actual submitted Bot Type.

            The visible select is intentionally disabled
            in BOTH modes.

            Manual:
                value comes from selected Bot.

            Bot Code:
                value comes from validated Bot Code.

        --}}
        <input
            type="hidden"
            name="bot_type"
            id="submitted_bot_type"
            value="{{ $isBotCodeMode ? $codeBotType : $manualBotType }}"
        >

    </div>


    {{-- =====================================================
         TRADING PAIR
         ===================================================== --}}

    <div>

        <label
            class="block text-[10px] text-muted mb-1.5 uppercase font-semibold"
            for="tradingPairSelect"
        >
            Trading Pair
        </label>


        <select
            id="tradingPairSelect"
            required
            {{ $isBotCodeMode ? 'disabled' : '' }}
            class="w-full bg-[#262628] border border-white/5 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-accent/40 disabled:opacity-70 disabled:cursor-not-allowed"
        >

            @if($isBotCodeMode)

                <option
                    value="{{ $codeTradingPair }}"
                    selected
                >
                    {{ $codeTradingPair }}
                </option>

            @else

                <option
                    value=""
                    selected
                    disabled
                >
                    Select Trading Pair
                </option>

                @foreach(
                    ($supportedPairs ?? []) as $pair
                )

                    @php
                        $pairValue = strtoupper(
                            trim(
                                is_array($pair)
                                    ? (
                                        $pair['value']
                                        ?? $pair['pair']
                                        ?? $pair['symbol']
                                        ?? ''
                                    )
                                    : $pair
                            )
                        );
                    @endphp

                    @if($pairValue !== '')

                        <option
                            value="{{ $pairValue }}"
                        >
                            {{ $pairValue }}
                        </option>

                    @endif

                @endforeach

            @endif

        </select>


        {{--

            Hidden submission field.

            Manual mode:
                JavaScript copies the selected pair here.

            Bot Code mode:
                JavaScript fills this directly from
                the validated Bot Code.

        --}}
        <input
            type="hidden"
            name="trading_pair"
            id="tradingPair"
            value="{{ $isBotCodeMode ? $codeTradingPair : '' }}"
        >

    </div>


    {{-- =====================================================
         ACTIVE DURATION
         ===================================================== --}}

    <div>

        <label
            class="block text-[10px] text-muted mb-1.5 uppercase font-semibold"
            for="botDuration"
        >
            Active Duration
        </label>


        <select
            id="botDuration"
            required
            {{ $isBotCodeMode ? 'disabled' : '' }}
            class="w-full bg-[#262628] border border-white/5 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-accent/40 disabled:opacity-70 disabled:cursor-not-allowed"
        >

            <option
                value=""
                disabled
                {{ !$isBotCodeMode ? 'selected' : '' }}
            >
                {{ $isBotCodeMode ? $codeDuration : 'Select Duration' }}
            </option>


            @if($isBotCodeMode)

                <option
                    value="{{ $codeDuration }}"
                    selected
                >
                    {{ $codeDuration }}
                </option>

            @else

                @foreach(
                    ($durationOptions ?? []) as $rawDuration
                )

                    @php

                        if (
                            is_array($rawDuration)
                        ) {

                            $durationValue =
                                $rawDuration['value']
                                ??
                                $rawDuration['duration']
                                ??
                                $rawDuration['key']
                                ??
                                '';

                            $durationLabel =
                                $rawDuration['label']
                                ??
                                $rawDuration['name']
                                ??
                                $durationValue;

                        } else {

                            $durationValue =
                                $rawDuration;

                            $durationLabel =
                                $rawDuration;

                        }

                    @endphp

                    @if(
                        trim(
                            (string) $durationValue
                        ) !== ''
                    )

                        <option
                            value="{{ $durationValue }}"
                        >
                            {{ $durationLabel }}
                        </option>

                    @endif

                @endforeach

            @endif

        </select>


        {{--

            Hidden duration field.

            This is what gets submitted because the
            visible duration select is disabled in
            Bot Code mode.

            It is also kept synchronized in manual mode.

        --}}
        <input
            type="hidden"
            name="duration"
            id="submitted_duration"
            value="{{ $isBotCodeMode ? $codeDuration : '' }}"
        >

    </div>


    {{-- =====================================================
         INVESTMENT AMOUNT
         ===================================================== --}}

    <div>

        <label
            class="block text-[10px] text-muted mb-1.5 uppercase font-semibold"
            for="investmentAmount"
        >
            Investment Amount (USDT)
        </label>


        <div class="relative">

            <input
                type="number"
                id="investmentAmount"
                step="0.00000001"
                min="{{ $bot->min_amount }}"
                max="{{ $bot->max_amount }}"
                value="{{ $isBotCodeMode && $codeAmount !== null
                    ? number_format((float) $codeAmount, 8, '.', '')
                    : '' }}"
                required
                {{ $isBotCodeMode ? 'readonly' : '' }}
                class="w-full bg-[#262628] border border-white/5 rounded-xl pl-4 pr-16 py-2.5 text-sm text-white focus:outline-none focus:border-accent/40 {{ $isBotCodeMode ? 'opacity-70 cursor-not-allowed' : '' }}"
                placeholder="0.00"
            >


            {{--

                Submitted investment amount.

                Manual:
                    JavaScript synchronizes this with
                    the editable investment field.

                Bot Code:
                    It contains the previously validated
                    amount.

            --}}
            <input
                type="hidden"
                name="amount"
                id="submitted_amount"
                value="{{ $isBotCodeMode && $codeAmount !== null
                    ? number_format((float) $codeAmount, 8, '.', '')
                    : '' }}"
            >


            <span
                class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-semibold text-accent"
            >
                USDT
            </span>

        </div>


        <div
            class="flex justify-between mt-1.5 text-[9px] text-muted"
        >
            <span>
                Minimum:
                {{ number_format((float) $bot->min_amount, 2) }}
                USDT
            </span>

            <span>
                Maximum:
                {{ number_format((float) $bot->max_amount, 2) }}
                USDT
            </span>
        </div>

    </div>


                <!-- WALLET -->

                <div>

                    <div
                        class="flex justify-between text-xs text-muted mb-1.5 font-semibold"
                    >

                        <span>
                            Wallet Balance (USDT-TRC20)
                        </span>

                    </div>


                    <div
                        class="bg-[#262628] border border-white/5 rounded-xl px-4 py-2.5 flex justify-between items-center text-sm font-semibold"
                    >

                        <span
                            id="walletBalance"
                            class="text-white"
                        >
                            {{ number_format($userBalance, 4) }}
                        </span>

                        <span
                            class="text-muted text-xs"
                        >
                            USDT
                        </span>

                    </div>

                </div>


                <!-- LAUNCH -->

                <button
                    type="submit"
                    id="startBotBtn"
                    style="
                        width:100%;
                        background:var(--accent);
                        color:#0b0c0d;
                        font-weight:700;
                        border-radius:12px;
                        padding:14px;
                        border:none;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        gap:8px;
                        cursor:pointer;
                        transition:opacity .2s;
                        font-size:15px;
                    "
                >

                    <i
                        class='bx bx-play-circle'
                        style="font-size:20px"
                        id="playIcon"
                    ></i>

                    <i
                        class='bx bx-loader bx-spin'
                        style="
                            font-size:20px;
                            display:none;
                        "
                        id="loadingSpinner"
                    ></i>

                    <span id="btnText">
                        Launch Trading Bot
                    </span>

                </button>

            </form>

        </div>
        
        <!-- =========================================================
             SINGLE SIMULATION
        ========================================================== -->

        <div
            id="botSimulations"
            class="mb-4"
        >

            @if($simulationSubscription)

                @php

                    $simulation =
                        $simulationSubscription;

                    $segments =
                        $simulation
                            ->simulation_segments
                        ?? [];

                    $durationSeconds =
                        (int) (
                            $simulation
                                ->simulation_duration_seconds
                            ?? 0
                        );

                    $elapsedSeconds = 0;

                    if (
                        $simulation
                            ->simulation_started_at
                    ) {

                        $elapsedSeconds =
                            max(
                                0,
                                $simulation
                                    ->simulation_started_at
                                    ->diffInSeconds(
                                        now()
                                    )
                            );
                    }

                    if (
                        $durationSeconds > 0
                    ) {

                        $elapsedSeconds =
                            min(
                                $elapsedSeconds,
                                $durationSeconds
                            );
                    }

                    $completedMinutes =
                        min(
                            count($segments),
                            (int) floor(
                                $elapsedSeconds /
                                60
                            )
                        );

                    if (
                        $simulation
                            ->simulation_status
                        ===
                        'completed'
                    ) {

                        $completedMinutes =
                            count($segments);
                            
                    }

                    $visibleSegments =
                        array_slice(
                            $segments,
                            0,
                            $completedMinutes
                        );

                    $currentProfit =
                        (float)
                        $simulation
                            ->current_profit;

                    $progress =
                        $durationSeconds > 0
                            ? min(
                                100,
                                (
                                    $elapsedSeconds /
                                    $durationSeconds
                                ) * 100
                            )
                            : 100;

                @endphp


                <div
                    class="simulation-card bg-[#1b1b1d] border border-white/5 rounded-2xl p-5"
                    data-subscription-id="{{ $simulation->id }}"
                    data-completed-minute="{{ $completedMinutes }}"
                >

                    <div
                        class="flex items-center justify-between mb-4"
                    >

                        <div>

                            <h3
                                class="text-sm font-bold text-white"
                            >
                                Live Trading Panel
                            </h3>

                            <p
                                class="text-[10px] text-muted mt-1"
                            >
                                {{ $simulation->bot->name ?? $bot->name ?? 'Bot' }}
                            </p>

                        </div>


                        <span
                            class="text-[9px] uppercase font-bold px-2 py-1 rounded-lg bg-yellow-500/10 text-green-400"
                        >
                            LIVE
                        </span>

                    </div>


                    <!-- CURRENT P/L -->

                    <div
                        class="bg-[#262628] border border-white/5 rounded-xl p-4 mb-4"
                    >

                        <div
                            class="text-[9px] text-muted uppercase font-semibold mb-1"
                        >
                            Current P/L
                        </div>


                        <div
                            class="simulation-current-profit text-lg font-black {{ $currentProfit >= 0 ? 'text-accent' : 'text-red-400' }}"
                        >

                            {{ $currentProfit >= 0 ? '+' : '' }}{{ number_format($currentProfit, 2) }}

                            <span
                                class="text-[10px] text-muted"
                            >
                                USDT
                            </span>

                        </div>

                    </div>


                    <!-- STATUS -->

                    <div
                        class="grid grid-cols-3 gap-2 mb-4 items-stretch"
                    >

                        <div
                            class="bg-[#262628] rounded-xl p-3 min-w-0 overflow-hidden"
                        >

                            <div
                                class="text-[9px] text-muted uppercase"
                            >
                                Status
                            </div>

                            <div
                                class="simulation-status text-xs font-bold text-white mt-1"
                            >
                                {{ $simulationSubscription->simulation_status === 'completed' ? 'Done' : 'Active' }}
                            </div>

                        </div>


                        <div
                            class="bg-[#262628] rounded-xl p-3"
                        >

                            <div
                                class="text-[9px] text-muted uppercase"
                            >
                                Minute
                            </div>

                            <div
                                class="text-xs font-bold text-white mt-1"
                            >

                                <span
                                    class="simulation-minute"
                                >
                                    {{ $completedMinutes }}
                                </span>

                                /

                                <span
                                    class="simulation-total-minutes"
                                >
                                    {{ count($segments) }}
                                </span>

                            </div>

                        </div>


                        <div
                            class="bg-[#262628] rounded-xl p-3"
                        >

                            <div
                                class="text-[9px] text-muted uppercase"
                            >
                                Remaining
                            </div>

                            <div
                                class="simulation-countdown text-xs font-bold text-white mt-1"
                            >
                                --
                            </div>

                        </div>

                    </div>


                    <!-- PROGRESS -->

                    <div class="mb-4">

                        <div
                            class="flex justify-between text-[9px] text-muted uppercase mb-1"
                        >

                            <span>
                                Trade Progress
                            </span>

                            <span
                                class="simulation-progress-text"
                            >
                                {{ number_format($progress, 1) }}%
                            </span>

                        </div>


                        <div
                            class="w-full h-2 bg-[#111213] rounded-full overflow-hidden"
                        >

                            <div
                                class="simulation-progress-bar h-full bg-accent rounded-full"
                                style="
                                    width:{{ $progress }}%;
                                    transition:width .3s ease;
                                "
                            ></div>

                        </div>

                    </div>


                    <!-- COMPLETED MINUTES -->

                    <div>

                        <div
                            class="text-[9px] text-muted uppercase font-semibold mb-2"
                        >
                            Trade P/L History
                        </div>


                        <div
    class="simulation-segments space-y-1.5"
    style="
        max-height: 280px;
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 4px;
    "
>

                            @if(
                                empty(
                                    $visibleSegments
                                )
                            )

                                <div
                                    class="text-center py-4 text-[10px] text-muted"
                                >
                                    Simulation has started.
                                    First minute is pending.
                                </div>

                            @else

                                @foreach(
                                    $visibleSegments
                                    as $segment
                                )

                                    @php

                                        $profit =
                                            (float)
                                            (
                                                $segment['profit']
                                                ?? 0
                                            );

                                        $cumulative =
                                            (float)
                                            (
                                                $segment[
                                                    'cumulative_profit'
                                                ]
                                                ?? 0
                                            );

                                    @endphp


                                    <div
                                        class="flex items-center justify-between bg-[#262628] rounded-lg px-3 py-2"
                                    >

                                        <span
                                            class="text-[10px] text-muted"
                                        >
                                            Minute
                                            {{ $segment['minute'] ?? 0 }}
                                        </span>


                                        <span
                                            class="text-[10px] font-semibold {{ $profit >= 0 ? 'text-accent' : 'text-red-400' }}"
                                        >
                                            {{ $profit >= 0 ? '+' : '' }}{{ number_format($profit, 2) }}
                                            USDT
                                        </span>


                                        <span
                                            class="text-[10px] text-white font-semibold"
                                        >
                                            {{ $cumulative >= 0 ? '+' : '' }}{{ number_format($cumulative, 2) }}
                                        </span>

                                    </div>

                                @endforeach

                            @endif

                        </div>

                    </div>
                    


                </div>

            @else

                <div
                    class="bg-[#1b1b1d] border border-white/5 rounded-2xl p-5"
                >

                    <div
                        class="text-center py-5 text-[10px] text-muted"
                    >
                        No active bot trade.
                    </div>

                </div>

            @endif

        </div>


        <!-- =========================================================
             LIVE ORDER BOOK
        ========================================================== -->

        <div
            class="bg-[#1b1b1d] border border-white/5 rounded-2xl p-4 mb-4"
        >

            <div
                class="flex items-center justify-between mb-3"
            >

                <h3
                    class="text-sm font-bold text-white"
                >
                    Live Order Book
                </h3>


                <span
                    id="orderbookStatus"
                    class="text-[9px] text-muted uppercase"
                >
                    Connecting...
                </span>

            </div>


            <div
                class="grid grid-cols-3 text-[10px] text-muted uppercase font-semibold mb-2 border-b border-white/5 pb-1"
            >

                <div>
                    Price
                </div>

                <div class="text-center">
                    Amount
                </div>

                <div class="text-right">
                    Total
                </div>

            </div>


            <div
                id="orderbook"
                class="overflow-y-auto max-h-[220px]"
            >

                <div class="orderbook-empty">
                    Connecting to live market data...
                </div>

            </div>

        </div>

    </main>


    <!-- =========================================================
         BOT POPUP
    ========================================================== -->

    <div
        id="customPopup"
        style="
            display:none;
            position:fixed;
            inset:0;
            z-index:100000;
            align-items:center;
            justify-content:center;
            background:rgba(0,0,0,.65);
            backdrop-filter:blur(6px);
            padding:16px;
        "
    >

        <div
            style="
                width:min(420px,calc(100vw - 32px));
                background:#1b1b1d;
                border:1px solid rgba(255,255,255,.08);
                border-radius:18px;
                padding:24px;
                box-shadow:0 20px 70px rgba(0,0,0,.55);
            "
        >

            <div
                id="popupTitle"
                style="
                    color:#fff;
                    font-size:17px;
                    font-weight:800;
                    margin-bottom:8px;
                "
            >
                Bot
            </div>


            <div
                id="popupMessage"
                style="
                    color:rgba(255,255,255,.65);
                    font-size:13px;
                    line-height:1.6;
                    margin-bottom:20px;
                "
            ></div>


            <button
                id="popupOkBtn"
                type="button"
                style="
                    width:100%;
                    background:var(--accent);
                    color:#0b0c0d;
                    font-weight:700;
                    border-radius:10px;
                    padding:12px;
                    border:none;
                    cursor:pointer;
                    font-size:14px;
                "
            >
                OK
            </button>

        </div>

    </div>


    @push('scripts')


    <!-- =========================================================
         MARKET DATA
    ========================================================== -->

    <script>

    (function () {

        'use strict';

        let currentBaseSymbol = 'BTC';

        let currentPair = 'BTCUSDT';

        let currentInterval = 60;

        let orderbookSocket = null;

        let orderbookReconnectTimer = null;


        function normalizeSymbol(symbol) {

            return String(
                symbol || 'BTC'
            )
            .toUpperCase()
            .replace(
                /[^A-Z0-9]/g,
                ''
            );

        }


        function currentPairDisplay() {

            return (
                currentBaseSymbol +
                '/USDT'
            );

        }


        /*
         * ---------------------------------------------------------
         * TRADINGVIEW
         * ---------------------------------------------------------
         */
        function initTradingViewChart() {

            const chart =
                document.getElementById(
                    'tradingview_chart'
                );

            if (!chart) {
                return;
            }

            chart.innerHTML = '';

            const label =
                document.getElementById(
                    'chartSymbolLabel'
                );

            if (label) {
                label.textContent =
                    currentPairDisplay();
            }

            if (
                typeof TradingView ===
                'undefined'
            ) {

                chart.innerHTML = `
                    <div class="h-full flex items-center justify-center text-[10px] text-muted">
                        Market chart unavailable.
                    </div>
                `;

                return;
            }

            const containerId =
                'tradingview_chart';

            new TradingView.widget({

                autosize: true,

                symbol:
                    'BINANCE:' +
                    currentPair,

                interval:
                    String(
                        currentInterval
                    ),

                timezone:
                    'exchange',

                theme:
                    'dark',

                style:
                    '1',

                toolbar_bg:
                    '#1b1b1d',

                backgroundColor:
                    '#111213',

                enable_publishing:
                    false,

                hide_side_toolbar:
                    true,

                allow_symbol_change:
                    true,

                container_id:
                    containerId,

                studies: [],

            });

        }


        /*
         * ---------------------------------------------------------
         * MARKET STATS
         * ---------------------------------------------------------
         */
        async function loadMarketStats() {

            const symbol =
                currentPair;

            try {

                const response =
                    await fetch(
                        'https://api.binance.com/api/v3/ticker/24hr?symbol=' +
                        encodeURIComponent(
                            symbol
                        ),
                        {
                            cache:
                                'no-store'
                        }
                    );

                if (
                    !response.ok
                ) {
                    return;
                }

                const data =
                    await response.json();

                const lastPrice =
                    Number(
                        data.lastPrice ||
                        0
                    );

                const volume =
                    Number(
                        data.volume ||
                        0
                    );

                const change =
                    Number(
                        data.priceChangePercent ||
                        0
                    );

                const high =
                    Number(
                        data.highPrice ||
                        0
                    );


                const priceElement =
                    document.getElementById(
                        'marketLastPrice'
                    );

                if (priceElement) {

                    priceElement.textContent =
                        lastPrice.toLocaleString(
                            'en-US',
                            {
                                minimumFractionDigits:
                                    2,

                                maximumFractionDigits:
                                    8
                            }
                        );
                }


                const volumeElement =
                    document.getElementById(
                        'baseVolume'
                    );

                if (volumeElement) {

                    volumeElement.textContent =
                        volume.toLocaleString(
                            'en-US',
                            {
                                maximumFractionDigits:
                                    2
                            }
                        );
                }


                const volumeLabel =
                    document.getElementById(
                        'baseVolumeLabel'
                    );

                if (volumeLabel) {

                    volumeLabel.textContent =
                        currentBaseSymbol +
                        ' Volume:';

                }


                const changeElement =
                    document.getElementById(
                        'marketChange'
                    );

                if (changeElement) {

                    changeElement.textContent =
                        (
                            change >= 0
                                ? '+'
                                : ''
                        ) +
                        change.toFixed(
                            2
                        ) +
                        '%';

                    changeElement.classList.toggle(
                        'text-accent',
                        change >= 0
                    );

                    changeElement.classList.toggle(
                        'text-red-400',
                        change < 0
                    );
                }


                const highElement =
                    document.getElementById(
                        'marketHigh'
                    );

                if (highElement) {

                    highElement.textContent =
                        high.toLocaleString(
                            'en-US',
                            {
                                maximumFractionDigits:
                                    8
                            }
                        );
                }

            } catch (error) {

                console.error(
                    'Market stats error:',
                    error
                );

            }

        }


        /*
         * ---------------------------------------------------------
         * ORDER BOOK
         * ---------------------------------------------------------
         */
        function renderOrderBook(
            bids,
            asks
        ) {

            const container =
                document.getElementById(
                    'orderbook'
                );

            if (!container) {
                return;
            }

            const bidRows =
                Array.isArray(bids)
                    ? bids.slice(
                        0,
                        10
                    )
                    : [];

            const askRows =
                Array.isArray(asks)
                    ? asks.slice(
                        0,
                        10
                    )
                    : [];

            const rows = [];

            for (
                let i = 0;
                i < Math.max(
                    bidRows.length,
                    askRows.length
                );
                i++
            ) {

                const bid =
                    bidRows[i];

                const ask =
                    askRows[i];

                rows.push(`
                    <div class="grid grid-cols-2 gap-2 border-b border-white/5 py-1.5">

                        <div class="space-y-1">

                            ${
                                bid
                                    ? `
                                    <div class="order-row">
                                        <div class="order-bid">
                                            ${Number(bid[0]).toFixed(2)}
                                        </div>

                                        <div class="text-right text-gray-300">
                                            ${Number(bid[1]).toFixed(5)}
                                        </div>

                                        <div class="text-right text-muted">
                                            ${(
                                                Number(bid[0]) *
                                                Number(bid[1])
                                            ).toLocaleString(
                                                'en-US',
                                                {
                                                    minimumFractionDigits:
                                                        2
                                                }
                                            )}
                                        </div>
                                    </div>
                                    `
                                    : ''
                            }

                        </div>


                        <div class="space-y-1">

                            ${
                                ask
                                    ? `
                                    <div class="order-row">
                                        <div class="order-ask">
                                            ${Number(ask[0]).toFixed(2)}
                                        </div>

                                        <div class="text-right text-gray-300">
                                            ${Number(ask[1]).toFixed(5)}
                                        </div>

                                        <div class="text-right text-muted">
                                            ${(
                                                Number(ask[0]) *
                                                Number(ask[1])
                                            ).toLocaleString(
                                                'en-US',
                                                {
                                                    minimumFractionDigits:
                                                        2
                                                }
                                            )}
                                        </div>
                                    </div>
                                    `
                                    : ''
                            }

                        </div>

                    </div>
                `);

            }

            container.innerHTML =
                rows.length
                    ? rows.join('')
                    : `
                        <div class="orderbook-empty">
                            Waiting for live order book...
                        </div>
                    `;
        }


        function connectOrderbook() {

            if (
                orderbookSocket
            ) {

                try {
                    orderbookSocket.close();
                } catch (
                    error
                ) {}
            }

            if (
                orderbookReconnectTimer
            ) {

                clearTimeout(
                    orderbookReconnectTimer
                );

                orderbookReconnectTimer =
                    null;
            }

            const status =
                document.getElementById(
                    'orderbookStatus'
                );

            const stream =
                currentPair.toLowerCase() +
                '@depth10@100ms';

            const url =
                'wss://stream.binance.com:9443/ws/' +
                stream;

            try {

                orderbookSocket =
                    new WebSocket(
                        url
                    );


                orderbookSocket.onopen =
                    function () {

                        if (status) {

                            status.textContent =
                                'Live';

                            status.className =
                                'text-[9px] text-accent uppercase';

                        }

                    };


                orderbookSocket.onmessage =
                    function (
                        event
                    ) {

                        try {

                            const data =
                                JSON.parse(
                                    event.data
                                );

                            renderOrderBook(
                                data.bids ||
                                [],
                                data.asks ||
                                []
                            );

                        } catch (
                            error
                        ) {

                            console.error(
                                'Order book parsing error:',
                                error
                            );

                        }

                    };


                orderbookSocket.onerror =
                    function () {

                        if (status) {

                            status.textContent =
                                'Reconnecting...';

                            status.className =
                                'text-[9px] text-yellow-400 uppercase';

                        }

                    };


                orderbookSocket.onclose =
                    function () {

                        if (status) {

                            status.textContent =
                                'Reconnecting...';

                            status.className =
                                'text-[9px] text-yellow-400 uppercase';

                        }

                        orderbookReconnectTimer =
                            setTimeout(
                                connectOrderbook,
                                3000
                            );

                    };

            } catch (
                error
            ) {

                console.error(
                    'Order book connection error:',
                    error
                );

            }

        }


        /*
         * ---------------------------------------------------------
         * PAIR CHANGE
         * ---------------------------------------------------------
         */
        function changePair(
            symbol,
            icon
        ) {

            currentBaseSymbol =
                normalizeSymbol(
                    symbol
                );

            currentPair =
                currentBaseSymbol +
                'USDT';


            const text =
                document.getElementById(
                    'selectedPairText'
                );

            if (text) {

                text.textContent =
                    currentPairDisplay();

            }


            const input =
                document.getElementById(
                    'tradingPair'
                );

            if (input) {

                input.value =
                    currentPairDisplay();

            }


            const image =
                document.getElementById(
                    'pairIcon'
                );

            if (
                image &&
                icon
            ) {

                image.src =
                    icon;

            }


            const dropdown =
                document.getElementById(
                    'pairDropdown'
                );

            if (dropdown) {

                dropdown.classList.add(
                    'hidden'
                );

            }


            loadMarketStats();

            initTradingViewChart();

            connectOrderbook();

        }


        /*
         * Pair selector.
         */
        const pairButton =
            document.getElementById(
                'pairSelectorButton'
            );

        const pairDropdown =
            document.getElementById(
                'pairDropdown'
            );


        if (
            pairButton &&
            pairDropdown
        ) {

            pairButton.addEventListener(
                'click',
                function (event) {

                    event.stopPropagation();

                    pairDropdown.classList.toggle(
                        'hidden'
                    );

                }
            );

        }


        document.addEventListener(
            'click',
            function (event) {

                const option =
                    event.target.closest(
                        '.pair-option'
                    );

                if (option) {

                    event.preventDefault();

                    changePair(
                        option.dataset.symbol,
                        option.dataset.icon
                    );

                    return;

                }

                if (
                    pairDropdown &&
                    !event.target.closest(
                        '#pairDropdown'
                    ) &&
                    !event.target.closest(
                        '#pairSelectorButton'
                    )
                ) {

                    pairDropdown.classList.add(
                        'hidden'
                    );

                }

            }
        );


        /*
         * Chart intervals.
         */
        document
            .querySelectorAll(
                '.chart-interval'
            )
            .forEach(
                function (button) {

                    button.addEventListener(
                        'click',
                        function () {

                            document
                                .querySelectorAll(
                                    '.chart-interval'
                                )
                                .forEach(
                                    function (other) {

                                        other.style.background =
                                            'var(--chip)';

                                        other.style.color =
                                            'var(--muted)';

                                        other.style.fontWeight =
                                            '600';

                                    }
                                );

                            this.style.background =
                                'var(--accent)';

                            this.style.color =
                                '#0b0c0d';

                            this.style.fontWeight =
                                '700';

                            currentInterval =
                                parseInt(
                                    this.dataset.interval,
                                    10
                                );

                            initTradingViewChart();

                        }
                    );

                }
            );


        /*
         * Initial market startup.
         */
        initTradingViewChart();

        loadMarketStats();

        connectOrderbook();


        setInterval(
            loadMarketStats,
            5000
        );

    })();

    </script>


    <script>
(function () {

    'use strict';


    /*
     * =========================================================
     * BOT LAUNCH CONFIGURATION
     * =========================================================
     *
     * There are two modes:
     *
     * 1. MANUAL BOT MODE
     *
     *    Bot is opened directly from the Bots page.
     *
     *    - Bot Type     = selected Bot
     *    - Pair         = selectable
     *    - Duration     = selectable
     *    - Amount       = editable
     *
     *
     * 2. BOT CODE MODE
     *
     *    Bot Code was validated before reaching this page.
     *
     *    - Bot Type     = locked
     *    - Pair         = locked
     *    - Duration     = locked
     *    - Amount       = locked
     *
     * =========================================================
     */


    const isBotCodeMode =
        @json($isBotCodeMode);


    @php

    $botCodeConfig = null;

    if ($isBotCodeMode && $botCode) {

        $botCodeConfig = [
            'bot_type' => strtolower(
                trim(
                    (string) $botCode->bot->bot_type
                )
            ),

            'trading_pair' => strtoupper(
                trim(
                    (string) $botCode->trading_pair
                )
            ),

            'duration' => (string) $botCode->duration,

            'amount' => $prefilledAmount !== null
                ? (float) $prefilledAmount
                : null,
        ];
    }

@endphp

const botCodeConfig =
    @json($botCodeConfig);

const isBotCodeMode =
    @json($isBotCodeMode);


    /*
     * ---------------------------------------------------------
     * DOM ELEMENTS
     * ---------------------------------------------------------
     */

    const botType =
        document.getElementById(
            'botType'
        );


    const tradingPairSelect =
        document.getElementById(
            'tradingPairSelect'
        );


    const tradingPair =
        document.getElementById(
            'tradingPair'
        );


    const duration =
        document.getElementById(
            'botDuration'
        );


    const submittedBotType =
        document.getElementById(
            'submitted_bot_type'
        );


    const submittedDuration =
        document.getElementById(
            'submitted_duration'
        );


    const submittedAmount =
        document.getElementById(
            'submitted_amount'
        );


    const amountInput =
        document.getElementById(
            'investmentAmount'
        );


    /*
     * ---------------------------------------------------------
     * SYNCHRONIZE HIDDEN FIELDS
     * ---------------------------------------------------------
     *
     * The hidden fields are the actual values submitted
     * for locked fields.
     * ---------------------------------------------------------
     */

    function syncSubmissionFields() {

        /*
         * BOT TYPE
         */
        if (
            submittedBotType &&
            botType
        ) {

            submittedBotType.value =
                botType.value || '';

        }


        /*
         * TRADING PAIR
         */
        if (
            tradingPair &&
            tradingPairSelect
        ) {

            tradingPair.value =
                tradingPairSelect.value || '';

        }


        /*
         * DURATION
         */
        if (
            submittedDuration &&
            duration
        ) {

            submittedDuration.value =
                duration.value || '';

        }


        /*
         * INVESTMENT
         */
        if (
            submittedAmount &&
            amountInput
        ) {

            submittedAmount.value =
                amountInput.value || '';

        }

    }
    
    /*
 * Make the synchronization function available
 * to the Bot Launch script.
 */
window.syncBotLaunchSubmissionFields =
    syncSubmissionFields;


    /*
     * ---------------------------------------------------------
     * APPLY MANUAL BOT CONFIGURATION
     * ---------------------------------------------------------
     */

    function initializeManualMode() {

        /*
         * The selected Bot is already known.
         *
         * Do not allow the user to change the Bot Type.
         */
        if (botType) {

            botType.value =
                @json(
                    strtolower(
                        trim(
                            (string)
                            $bot->bot_type
                        )
                    )
                );

            botType.disabled =
                true;

        }


        /*
         * Trading Pair is selectable.
         */
        if (tradingPairSelect) {

            tradingPairSelect.disabled =
                false;

        }


        /*
         * Duration is selectable.
         */
        if (duration) {

            duration.disabled =
                false;

        }


        /*
         * Investment is editable.
         */
        if (amountInput) {

            amountInput.readOnly =
                false;

        }


        syncSubmissionFields();

    }


    /*
     * ---------------------------------------------------------
     * APPLY BOT CODE CONFIGURATION
     * ---------------------------------------------------------
     */

    function initializeBotCodeMode() {

        if (
            !botCodeConfig
        ) {

            return;

        }


        /*
         * BOT TYPE
         */
        if (botType) {

            botType.value =
                botCodeConfig.bot_type
                || '';

            botType.disabled =
                true;

        }


        /*
         * TRADING PAIR
         */
        if (tradingPairSelect) {

            tradingPairSelect.value =
                botCodeConfig.trading_pair
                || '';

            tradingPairSelect.disabled =
                true;

        }


        /*
         * DURATION
         */
        if (duration) {

            duration.value =
                botCodeConfig.duration
                || '';

            duration.disabled =
                true;

        }


        /*
         * INVESTMENT
         */
        if (
            amountInput &&
            botCodeConfig.amount !== null &&
            botCodeConfig.amount !== undefined
        ) {

            amountInput.value =
                botCodeConfig.amount;

            amountInput.readOnly =
                true;

        }


        /*
         * Copy all validated values into
         * the hidden submission fields.
         */
        if (submittedBotType) {

            submittedBotType.value =
                botCodeConfig.bot_type
                || '';

        }


        if (tradingPair) {

            tradingPair.value =
                botCodeConfig.trading_pair
                || '';

        }


        if (submittedDuration) {

            submittedDuration.value =
                botCodeConfig.duration
                || '';

        }


        if (
            submittedAmount &&
            botCodeConfig.amount !== null &&
            botCodeConfig.amount !== undefined
        ) {

            submittedAmount.value =
                botCodeConfig.amount;

        }

    }


    /*
     * ---------------------------------------------------------
     * MANUAL PAIR CHANGE
     * ---------------------------------------------------------
     */

    if (tradingPairSelect) {

        tradingPairSelect.addEventListener(
            'change',
            function () {

                if (
                    isBotCodeMode
                ) {

                    return;

                }

                if (tradingPair) {

                    tradingPair.value =
                        this.value || '';

                }

            }
        );

    }


    /*
     * ---------------------------------------------------------
     * MANUAL DURATION CHANGE
     * ---------------------------------------------------------
     */

    if (duration) {

        duration.addEventListener(
            'change',
            function () {

                if (
                    isBotCodeMode
                ) {

                    return;

                }

                if (submittedDuration) {

                    submittedDuration.value =
                        this.value || '';

                }

            }
        );

    }


    /*
     * ---------------------------------------------------------
     * MANUAL AMOUNT CHANGE
     * ---------------------------------------------------------
     */

    if (amountInput) {

        amountInput.addEventListener(
            'input',
            function () {

                if (
                    isBotCodeMode
                ) {

                    return;

                }

                if (submittedAmount) {

                    submittedAmount.value =
                        this.value || '';

                }

            }
        );

    }


    /*
     * ---------------------------------------------------------
     * INITIALIZE
     * ---------------------------------------------------------
     */

    if (
        isBotCodeMode
    ) {

        initializeBotCodeMode();

    } else {

        initializeManualMode();

    }


    /*
     * Final synchronization.
     */
    syncSubmissionFields();


})();
</script>


    <!-- =========================================================
         BOT LAUNCH
    ========================================================== -->
    <script>

(function () {

    'use strict';

    const codeInput =
        document.getElementById('botCode');

    const codeStatus =
        document.getElementById('botCodeStatus');

    const codeMessage =
        document.getElementById('botCodeMessage');

    const codeIndicator =
        document.getElementById('botCodeIndicator');

    const botType =
        document.getElementById('botType');

    const duration =
        document.getElementById('botDuration');

    const tradingPair =
        document.getElementById('tradingPair');

    if (!codeInput) {
        return;
    }

    function setCodeState(
        state,
        message
    ) {

        if (codeStatus) {

            codeStatus.textContent =
                state === 'ready'
                    ? 'Ready'
                    : 'Required';

            codeStatus.classList.toggle(
                'text-accent',
                state === 'ready'
            );

            codeStatus.classList.toggle(
                'text-muted',
                state !== 'ready'
            );

        }

        if (codeMessage) {

            codeMessage.textContent =
                message;

        }

        if (codeIndicator) {

            codeIndicator.innerHTML =
                state === 'ready'
                    ? `<i class="bx bx-check-circle text-accent"></i>`
                    : `<i class="bx bx-key"></i>`;

        }

    }

    codeInput.addEventListener(
        'input',
        function () {

            this.value =
                this.value
                    .toUpperCase()
                    .trimStart();

            setCodeState(
                this.value
                    ? 'ready'
                    : 'required',
                this.value
                    ? 'Bot code entered. It will be validated securely when you launch the bot.'
                    : 'Enter the bot code provided by the administrator.'
            );

        }
    );

})();

</script>
    <script>

    (function () {

        'use strict';


        const form =
            document.getElementById(
                'subscribeForm'
            );

        const submitButton =
            document.getElementById(
                'startBotBtn'
            );

        const playIcon =
            document.getElementById(
                'playIcon'
            );

        const loadingSpinner =
            document.getElementById(
                'loadingSpinner'
            );

        const buttonText =
            document.getElementById(
                'btnText'
            );

        const popup =
            document.getElementById(
                'customPopup'
            );

        const popupTitle =
            document.getElementById(
                'popupTitle'
            );

        const popupMessage =
            document.getElementById(
                'popupMessage'
            );

        const popupOk =
            document.getElementById(
                'popupOkBtn'
            );


        function resetButton() {

            if (submitButton) {

                submitButton.disabled =
                    false;

                submitButton.style.opacity =
                    '1';

            }

            if (playIcon) {

                playIcon.style.display =
                    'inline-flex';

            }

            if (loadingSpinner) {

                loadingSpinner.style.display =
                    'none';

            }

            if (buttonText) {

                buttonText.textContent =
                    'Launch Trading Bot';

            }

        }


        function showPopup(
            title,
            message
        ) {

            if (!popup) {
                return;
            }

            if (popupTitle) {

                popupTitle.textContent =
                    title;

            }

            if (popupMessage) {

                popupMessage.textContent =
                    message;

            }

            popup.style.display =
                'flex';


            if (popupOk) {

                popupOk.onclick =
                    function () {

                        popup.style.display =
                            'none';

                    };

            }

        }


        window.showBotPopup =
            showPopup;


        if (!form) {
            return;
        }


        form.addEventListener(
            'submit',
            async function (
                event
            ) {

                event.preventDefault();


                if (
                    submitButton &&
                    submitButton.disabled
                ) {

                    return;

                }


                if (submitButton) {

                    submitButton.disabled =
                        true;

                    submitButton.style.opacity =
                        '.6';

                }

                if (playIcon) {

                    playIcon.style.display =
                        'none';

                }

                if (loadingSpinner) {

                    loadingSpinner.style.display =
                        'inline-flex';

                }

                if (buttonText) {

                    buttonText.textContent =
                        'Starting Bot...';

                }


                const formData =
                    new FormData(
                        form
                    );
                
                
                /*
                 * ---------------------------------------------------------
                 * INCLUDE DISABLED BOT PARAMETERS
                 * ---------------------------------------------------------
                 *
                 * HTML excludes disabled controls from FormData.
                 * The Bot Type and Duration fields are intentionally
                 * disabled when a Bot Code has been loaded, so we must
                 * explicitly add their values to the AJAX request.
                 */
                
                /*
 * Synchronize disabled/hidden fields.
 */
if (
    typeof window.syncBotLaunchSubmissionFields ===
    'function'
) {

    window.syncBotLaunchSubmissionFields();

}

formData.set(
    'bot_type',
    document.getElementById(
        'submitted_bot_type'
    )?.value || ''
);

formData.set(
    'trading_pair',
    document.getElementById(
        'tradingPair'
    )?.value || ''
);

formData.set(
    'duration',
    document.getElementById(
        'submitted_duration'
    )?.value || ''
);

formData.set(
    'amount',
    document.getElementById(
        'submitted_amount'
    )?.value || ''
);
                
                
                /*
                 * Trading Pair is already a hidden field in the form,
                 * so it is automatically included in FormData.
                 */
                
                
                try {
                
                    const response =
                        await fetch(
                            form.action,
                            {
                                method:
                                    'POST',

                                headers: {

                                    'X-CSRF-TOKEN':
                                        '{{ csrf_token() }}',

                                    'Accept':
                                        'application/json',

                                    'X-Requested-With':
                                        'XMLHttpRequest'

                                },

                                body:
                                    formData
                            }
                        );


                    let result = {};

                    try {

                        result =
                            await response.json();

                    } catch (
                        error
                    ) {

                        throw new Error(
                            'The server returned an invalid response.'
                        );

                    }


                    if (
                        !response.ok ||
                        !result.success
                    ) {

                        throw new Error(
                            result.message ||
                            'Unable to launch the trading bot.'
                        );

                    }


                    /*
                     * Update wallet locally.
                     */
                    const wallet =
                        document.getElementById(
                            'walletBalance'
                        );

                    const amount =
                        Number(
                            formData.get(
                                'amount'
                            ) || 0
                        );

                    if (wallet) {

                        const currentBalance =
                            Number(
                                wallet.textContent
                                    .replace(
                                        /,/g,
                                        ''
                                    )
                            );

                        if (
                            Number.isFinite(
                                currentBalance
                            )
                        ) {

                            wallet.textContent =
                                Math.max(
                                    0,
                                    currentBalance -
                                    amount
                                ).toLocaleString(
                                    'en-US',
                                    {
                                        minimumFractionDigits:
                                            4,

                                        maximumFractionDigits:
                                            4
                                    }
                                );

                        }

                    }


                    /*
                 * Reset investment amount after
                 * successful launch.
                 */
                const launchAmountInput =
                    document.getElementById(
                        'investmentAmount'
                    );
                
                const launchSubmittedAmount =
                    document.getElementById(
                        'submitted_amount'
                    );
                
                
                if (launchAmountInput) {
                
                    launchAmountInput.value =
                        '';
                
                }
                
                
                if (launchSubmittedAmount) {
                
                    launchSubmittedAmount.value =
                        '';
                
                }


                    /*
                     * Reset duration.
                     */
                    const duration =
                        document.getElementById(
                            'botDuration'
                        );

                    if (duration) {

                        duration.selectedIndex =
                            0;

                    }


                    resetButton();


                    showPopup(
                        'Bot Activated',
                        result.message ||
                        'The bot has been activated successfully.'
                    );


                    if (
    result.subscription_id
) {

    /*
     * Create the simulation exactly as
     * the existing system already does.
     */
    startNewSimulation(
        result.subscription_id
    );


    function scrollToSimulation(
    smooth = true
) {

    let attempts = 0;

    const maxAttempts = 20;


    function findSimulation() {

        const container =
            document.getElementById(
                'botSimulations'
            );

        const card =
            container
                ? container.querySelector(
                    '.simulation-card'
                )
                : null;


        if (container && card) {

            container.scrollIntoView({

                behavior:
                    smooth
                        ? 'smooth'
                        : 'auto',

                block:
                    'center'

            });


            card.classList.add(
                'simulation-focus'
            );


            setTimeout(
                function () {

                    card.classList.remove(
                        'simulation-focus'
                    );

                },
                1800
            );


            return;

        }


        attempts++;


        if (
            attempts <
            maxAttempts
        ) {

            setTimeout(
                findSimulation,
                100
            );

        }

    }


    findSimulation();

}

}

                } catch (
                    error
                ) {

                    console.error(
                        'Bot launch error:',
                        error
                    );

                    resetButton();

                    showPopup(
                        'Launch Failed',
                        error.message ||
                        'Something went wrong. Please try again.'
                    );

                }

            }
        );

    })();
    

    </script>


    <!-- =========================================================
         SINGLE LIVE SIMULATION ENGINE
    ========================================================== -->

    <script>

    (function () {

        'use strict';


        const simulationUrlTemplate =
        "{{ route('bots.simulation', '__SUBSCRIPTION__') }}";


        let activeCard = null;


        function formatMoney(
            value
        ) {

            const number =
                Number(
                    value || 0
                );

            if (
                !Number.isFinite(
                    number
                )
            ) {

                return '+0.00';

            }

            return (
                number >= 0
                    ? '+'
                    : ''
            ) +
            number.toLocaleString(
                'en-US',
                {
                    minimumFractionDigits:
                        2,

                    maximumFractionDigits:
                        2
                }
            );

        }


        function formatCountdown(
            seconds
        ) {

            seconds =
                Math.max(
                    0,
                    Math.floor(
                        Number(
                            seconds || 0
                        )
                    )
                );

            const hours =
                Math.floor(
                    seconds / 3600
                );

            const minutes =
                Math.floor(
                    (
                        seconds %
                        3600
                    ) / 60
                );

            const secs =
                seconds %
                60;

            return [

                String(hours)
                    .padStart(
                        2,
                        '0'
                    ),

                String(minutes)
                    .padStart(
                        2,
                        '0'
                    ),

                String(secs)
                    .padStart(
                        2,
                        '0'
                    )

            ].join(':');

        }


        function getContainer() {

            return document.getElementById(
                'botSimulations'
            );

        }
        
        function scrollToSimulation(
    smooth = true
) {

    const container =
        document.getElementById(
            'botSimulations'
        );

    if (!container) {
        return;
    }


    /*
     * Give the newly-created simulation
     * card a moment to enter the DOM.
     */
    setTimeout(
        function () {

            container.scrollIntoView({
                behavior:
                    smooth
                        ? 'smooth'
                        : 'auto',

                block:
                    'center'
            });


            /*
             * Add a temporary visual
             * highlight after scrolling.
             */
            const card =
                container.querySelector(
                    '.simulation-card'
                );

            if (!card) {
                return;
            }


            card.classList.add(
                'simulation-focus'
            );


            setTimeout(
                function () {

                    card.classList.remove(
                        'simulation-focus'
                    );

                },
                1800
            );

        },
        250
    );
}


        function stopCardTimers(
            card
        ) {

            if (!card) {
                return;
            }

            if (
                card._pollTimer
            ) {

                clearInterval(
                    card._pollTimer
                );

                card._pollTimer =
                    null;

            }

            if (
                card._uiTimer
            ) {

                clearInterval(
                    card._uiTimer
                );

                card._uiTimer =
                    null;

            }

        }
        


        /*
         * ---------------------------------------------------------
         * CREATE SINGLE CARD AFTER LAUNCH
         * ---------------------------------------------------------
         */
        function createSimulationCard(
            subscriptionId
        ) {

            const container =
                getContainer();

            if (!container) {
                return null;
            }


            /*
             * Remove old simulation card.
             */
            container
                .querySelectorAll(
                    '.simulation-card'
                )
                .forEach(
                    function (
                        card
                    ) {

                        stopCardTimers(
                            card
                        );

                        card.remove();

                    }
                );


            const card =
                document.createElement(
                    'div'
                );

            card.className =
                'simulation-card bg-[#1b1b1d] border border-white/5 rounded-2xl p-5';

            card.dataset.subscriptionId =
                subscriptionId;

            card.dataset.completedMinute =
                '0';


            card.innerHTML = `

                <div
                    class="flex items-center justify-between mb-4"
                >

                    <div>

                        <h3
                            class="text-sm font-bold text-white"
                        >
                            Live Trading
                        </h3>

                        <p
                            class="text-[10px] text-muted mt-1"
                        >
                            Live Bot Trading
                        </p>

                    </div>

                    <span
                        class="text-[9px] uppercase font-bold px-2 py-1 rounded-lg bg-yellow-500/10 text-green-400"
                    >
                        LIVE
                    </span>

                </div>


                <div
                    class="bg-[#262628] border border-white/5 rounded-xl p-4 mb-4"
                >

                    <div
                        class="text-[9px] text-muted uppercase font-semibold mb-1"
                    >
                        Current P/L
                    </div>

                    <div
                        class="simulation-current-profit text-lg font-black text-accent"
                    >
                        +0.00
                        <span
                            class="text-[10px] text-muted"
                        >
                            USDT
                        </span>
                    </div>

                </div>


                <div
                    class="grid grid-cols-3 gap-2 mb-4"
                >

                    <div
                        class="bg-[#262628] rounded-xl p-3"
                    >

                        <div
                            class="text-[9px] text-muted uppercase"
                        >
                            Status
                        </div>

                        <div
                            class="simulation-status text-xs font-bold text-white mt-1 min-w-0 leading-tight break-words"
                        >
                            running
                        </div>

                    </div>


                    <div
                        class="bg-[#262628] rounded-xl p-3 min-w-0 overflow-hidden"
                    >

                        <div
                            class="text-[9px] text-muted uppercase"
                        >
                            Minute
                        </div>

                        <div
                            class="text-xs font-bold text-white mt-1 whitespace-nowrap"
                        >

                            <span
                                class="simulation-minute"
                            >
                                0
                            </span>

                            /

                            <span
                                class="simulation-total-minutes"
                            >
                                0
                            </span>

                        </div>

                    </div>


                    <div
                        class="bg-[#262628] rounded-xl p-3 min-w-0 overflow-hidden"
                    >

                        <div
                            class="text-[9px] text-muted uppercase"
                        >
                            Remaining
                        </div>

                        <div
                            class="simulation-countdown text-xs font-bold text-white mt-1 whitespace-nowrap"
                        >
                            --
                        </div>

                    </div>

                </div>


                <div class="mb-4">

                    <div
                        class="flex justify-between text-[9px] text-muted uppercase mb-1"
                    >

                        <span>
                            Trade Progress
                        </span>

                        <span
                            class="simulation-progress-text"
                        >
                            0.0%
                        </span>

                    </div>


                    <div
                        class="w-full h-2 bg-[#111213] rounded-full overflow-hidden"
                    >

                        <div
                            class="simulation-progress-bar h-full bg-accent rounded-full"
                            style="width:0%"
                        ></div>

                    </div>

                </div>


                <div>

                    <div
                        class="text-[9px] text-muted uppercase font-semibold mb-2"
                    >
                        Trade P/L History
                    </div>

                    <div
                        class="simulation-segments space-y-1.5"
                    >

                        <div
                            class="text-center py-4 text-[10px] text-muted"
                        >
                            Trade has started.
                            First minute is pending.
                        </div>

                    </div>

                </div>

            `;


            container.appendChild(
                card
            );

            activeCard =
                card;

            return card;

        }


        /*
         * ---------------------------------------------------------
         * POPUP
         * ---------------------------------------------------------
         */
        function showMinuteProfitPopup(
            subscriptionId,
            segment
        ) {

            if (!segment) {
                return;
            }

            const minute =
                Number(
                    segment.minute ||
                    0
                );

            if (
                minute <= 0
            ) {
                return;
            }


            /*
             * Prevent duplicate notifications.
             */
            const key =
                'bot_pl_popup_' +
                subscriptionId +
                '_' +
                minute;


            if (
                sessionStorage.getItem(
                    key
                ) ===
                'shown'
            ) {

                return;

            }


            sessionStorage.setItem(
                key,
                'shown'
            );


            const profit =
                Number(
                    segment.profit ||
                    0
                );

            const isProfit =
                profit >= 0;


            const popup =
                document.createElement(
                    'div'
                );

            popup.className =
                'minute-pl-popup ' +
                (
                    isProfit
                        ? 'minute-pl-profit'
                        : 'minute-pl-loss'
                );


            popup.innerHTML = `

                <div
                    class="minute-pl-icon"
                >

                    <i
                        class="bx ${
                            isProfit
                                ? 'bx-trending-up'
                                : 'bx-trending-down'
                        }"
                    ></i>

                </div>


                <div
                    class="minute-pl-content"
                >

                    <div
                        class="minute-pl-title"
                    >
                        ${
                            isProfit
                                ? 'Profit Recorded'
                                : 'Loss Recorded'
                        }
                    </div>

                    <div
                        class="minute-pl-minute"
                    >
                        Minute ${minute}
                    </div>

                    <div
                        class="minute-pl-amount"
                    >
                        ${formatMoney(profit)}
                        USDT
                    </div>

                </div>


                <button
                    type="button"
                    class="minute-pl-close"
                >
                    ×
                </button>

            `;


            document.body.appendChild(
                popup
            );


            requestAnimationFrame(
                function () {

                    popup.classList.add(
                        'minute-pl-visible'
                    );

                }
            );


            function closePopup() {

                popup.classList.remove(
                    'minute-pl-visible'
                );

                setTimeout(
                    function () {

                        if (
                            popup.parentNode
                        ) {

                            popup.remove();

                        }

                    },
                    300
                );

            }


            const closeButton =
                popup.querySelector(
                    '.minute-pl-close'
                );

            if (closeButton) {

                closeButton.addEventListener(
                    'click',
                    closePopup
                );

            }


            setTimeout(
                closePopup,
                7000
            );

        }


        /*
         * ---------------------------------------------------------
         * RENDER SERVER SEGMENTS
         * ---------------------------------------------------------
         */
        function renderSegments(
            card,
            segments
        ) {

            const container =
                card.querySelector(
                    '.simulation-segments'
                );

            if (!container) {
                return;
            }


            if (
                !Array.isArray(
                    segments
                )
                ||
                !segments.length
            ) {

                container.innerHTML = `
                    <div
                        class="text-center py-4 text-[10px] text-muted"
                    >
                        Trade has started.
                        First minute is pending.
                    </div>
                `;

                return;

            }


            container.innerHTML =
                segments
                    .map(
                        function (
                            segment
                        ) {

                            const profit =
                                Number(
                                    segment.profit ||
                                    0
                                );

                            const cumulative =
                                Number(
                                    segment.cumulative_profit ||
                                    0
                                );

                            const profitClass =
                                profit >= 0
                                    ? 'text-accent'
                                    : 'text-red-400';


                            return `

                                <div
                                    class="flex items-center justify-between bg-[#262628] rounded-lg px-3 py-2"
                                >

                                    <span
                                        class="text-[10px] text-muted"
                                    >
                                        Minute
                                        ${segment.minute ?? 0}
                                    </span>


                                    <span
                                        class="text-[10px] ${profitClass} font-semibold"
                                    >
                                        ${formatMoney(profit)}
                                        USDT
                                    </span>


                                    <span
                                        class="text-[10px] text-white font-semibold"
                                    >
                                        ${formatMoney(cumulative)}
                                    </span>

                                </div>

                            `;

                        }
                    )
                    .join('');

        }


        /*
         * ---------------------------------------------------------
         * SERVER UPDATE
         * ---------------------------------------------------------
         */
        async function fetchSimulation(
            card
        ) {

            if (!card) {
                return;
            }


            const subscriptionId =
                card.dataset
                    .subscriptionId;

            if (!subscriptionId) {
                return;
            }


            const url =
                simulationUrlTemplate
                    .replace(
                        '__SUBSCRIPTION__',
                        subscriptionId
                    );


            try {

                const response =
                    await fetch(
                        url,
                        {
                            method:
                                'GET',

                            headers: {

                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest'

                            },

                            cache:
                                'no-store'
                        }
                    );


                if (
                    !response.ok
                ) {

                    return;

                }


                const result =
                    await response.json();


                if (
                    !result.success ||
                    !result.simulation
                ) {

                    return;

                }


                const state =
                    result.simulation;


                const previousMinute =
                    Number(
                        card.dataset
                            .completedMinute ||
                        0
                    );


                const currentMinute =
                    Number(
                        state.minute ||
                        0
                    );


                const segments =
                    Array.isArray(
                        state.segments
                    )
                        ? state.segments
                        : [];


                /*
                 * Only NEW server-completed minutes
                 * create popups.
                 */
                if (
                    currentMinute >
                    previousMinute
                ) {

                    for (
                        let minute =
                            previousMinute;
                        minute <
                        currentMinute;
                        minute++
                    ) {

                        const segment =
                            segments[
                                minute
                            ];

                        if (segment) {

                            showMinuteProfitPopup(
                                subscriptionId,
                                segment
                            );

                        }

                    }

                }


                card.dataset.completedMinute =
                    currentMinute;


                card._simulationState =
                    state;


                updateCard(
                    card,
                    state
                );


                /*
                 * Completion.
                 */
                if (
    state.status ===
        'completed' ||
    state.status ===
        'terminated'
) {

    stopCardTimers(
        card
    );

    return;

}

            } catch (
                error
            ) {

                console.error(
                    'Simulation update error:',
                    error
                );

            }

        }


        /*
         * ---------------------------------------------------------
         * UPDATE CARD
         * ---------------------------------------------------------
         *
         * The browser only animates:
         *
         * - elapsed time
         * - progress
         * - countdown
         *
         * It does NOT create future P/L.
         */
        function updateCard(
            card,
            state
        ) {

            if (
                !card ||
                !state
            ) {

                return;

            }


            const duration =
                Number(
                    state.duration_seconds ||
                    0
                );


            let elapsed =
                Number(
                    state.elapsed_seconds ||
                    0
                );


            /*
             * Use the server timestamp to keep the
             * progress clock smooth.
             */
            if (
                state.status ===
                    'running'
                &&
                state.started_at
            ) {

                const startedAt =
                    Date.parse(
                        state.started_at
                    );

                if (
                    Number.isFinite(
                        startedAt
                    )
                ) {

                    elapsed =
                        Math.floor(
                            (
                                Date.now() -
                                startedAt
                            ) / 1000
                        );

                }

            }


            elapsed =
                Math.max(
                    0,
                    elapsed
                );


            if (
                duration > 0
            ) {

                elapsed =
                    Math.min(
                        elapsed,
                        duration
                    );

            }


            const progress =
                duration > 0
                    ? Math.min(
                        100,
                        (
                            elapsed /
                            duration
                        ) * 100
                    )
                    : 100;


            /*
             * IMPORTANT:
             *
             * No frontend P/L interpolation.
             *
             * current_profit is the latest
             * server-confirmed P/L.
             */
            const currentProfit =
                Number(
                    state.current_profit ||
                    0
                );


            const profitElement =
                card.querySelector(
                    '.simulation-current-profit'
                );


            if (profitElement) {

                profitElement.innerHTML = `
                    ${formatMoney(currentProfit)}

                    <span
                        class="text-[10px] text-muted"
                    >
                        USDT
                    </span>
                `;


                profitElement.classList.toggle(
                    'text-accent',
                    currentProfit >= 0
                );

                profitElement.classList.toggle(
                    'text-red-400',
                    currentProfit < 0
                );

            }


       /* Status */

const statusElement =
    card.querySelector(
        '.simulation-status'
    );


if (statusElement) {

    const isDone =
        state.status ===
            'completed' ||
        state.status ===
            'terminated';


    statusElement.textContent =
        isDone
            ? 'Done'
            : 'Active';


    statusElement.classList.toggle(
        'simulation-status-running',
        !isDone
    );


    statusElement.classList.toggle(
        'simulation-status-completed',
        isDone
    );

}
            
            const terminateButton =
    card.querySelector(
        '.terminate-bot-btn'
    );

if (terminateButton) {

    const simulationStatus =
        String(
            state.status ||
            'running'
        ).toLowerCase();

    if (
        simulationStatus ===
            'completed'
        ||
        simulationStatus ===
            'terminated'
    ) {

        terminateButton.disabled =
            true;

        terminateButton.style.display =
            'none';

    } else {

        terminateButton.disabled =
            false;

        terminateButton.style.display =
            'flex';

    }

}


            const progressBar =
                card.querySelector(
                    '.simulation-progress-bar'
                );

            if (progressBar) {

                progressBar.style.width =
                    progress.toFixed(
                        2
                    ) +
                    '%';

            }


            const progressText =
                card.querySelector(
                    '.simulation-progress-text'
                );

            if (progressText) {

                progressText.textContent =
                    progress.toFixed(
                        1
                    ) +
                    '%';

            }


            const completedMinutes =
                Number(
                    state.minute ||
                    0
                );


            const minuteElement =
                card.querySelector(
                    '.simulation-minute'
                );

            if (minuteElement) {

                minuteElement.textContent =
                    completedMinutes;

            }


            const totalMinutesElement =
                card.querySelector(
                    '.simulation-total-minutes'
                );

            if (
                totalMinutesElement
            ) {

                totalMinutesElement.textContent =
                    Number(
                        state.total_minutes ||
                        0
                    );

            }


            const countdownElement =
                card.querySelector(
                    '.simulation-countdown'
                );

            if (
                countdownElement
            ) {

                countdownElement.textContent =
                    formatCountdown(
                        Math.max(
                            0,
                            duration -
                            elapsed
                        )
                    );

            }


            /*
             * Only server-provided completed
             * segments are rendered.
             */
            renderSegments(
                card,
                Array.isArray(
                    state.segments
                )
                    ? state.segments
                    : []
            );

        }


        /*
         * ---------------------------------------------------------
         * LOCAL CLOCK
         * ---------------------------------------------------------
         */
        function startLocalClock(
            card
        ) {

            if (!card) {
                return;
            }


            stopCardTimers(
                card
            );


            card._uiTimer =
                setInterval(
                    function () {

                        if (
                            !card._simulationState
                        ) {

                            return;

                        }

                        if (
                            card._simulationState
                                .status
                            ===
                            'completed'
                        ) {

                            return;

                        }

                        updateCard(
                            card,
                            card._simulationState
                        );

                    },
                    250
                );


            /*
             * Poll the server every 2 seconds.
             *
             * This is what causes the P/L and popup
             * to update as each real minute completes.
             */
            card._pollTimer =
                setInterval(
                    function () {

                        fetchSimulation(
                            card
                        );

                    },
                    2000
                );


            /*
             * First request immediately.
             */
            fetchSimulation(
                card
            );

        }


        /*
         * ---------------------------------------------------------
         * START NEW SIMULATION
         * ---------------------------------------------------------
         */
        window.startNewSimulation =
            function (
                subscriptionId
            ) {

                const card =
                    createSimulationCard(
                        subscriptionId
                    );

                if (!card) {
                    return;
                }

                startLocalClock(
                    card
                );

            };


        /*
         * ---------------------------------------------------------
         * INITIAL PAGE SIMULATION
         * ---------------------------------------------------------
         */
        const initialCard =
            document.querySelector(
                '#botSimulations .simulation-card'
            );


        if (initialCard) {

            activeCard =
                initialCard;

            startLocalClock(
                initialCard
            );

        }

    })();
    
    (function () {

    'use strict';

    const terminateUrlTemplate =
        @json(
            url(
                '/bots/subscriptions/__SUBSCRIPTION__/terminate'
            )
        );


    function formatMoney(value) {

        const number =
            Number(value || 0);

        if (!Number.isFinite(number)) {
            return '+0.00';
        }

        return (
            number >= 0
                ? '+'
                : ''
        ) +
        number.toLocaleString(
            'en-US',
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        );
    }


    async function terminateBot(button) {

        if (!button) {
            return;
        }


        const subscriptionId =
            button.dataset.subscriptionId;


        if (!subscriptionId) {

            if (typeof window.showBotPopup === 'function') {

                window.showBotPopup(
                    'Termination Failed',
                    'The bot subscription could not be identified.'
                );

            }

            return;
        }


        /*
         * Confirmation.
         */
        const confirmed =
            window.confirm(
                'Are you sure you want to stop this bot trade?\n\n' +
                'The accumulated P/L and your original trading capital ' +
                'will be returned to your USDT-TRC20 wallet.'
            );


        if (!confirmed) {
            return;
        }


        /*
         * Prevent double clicks.
         */
        button.disabled = true;

        const originalHtml =
            button.innerHTML;


        button.innerHTML = `
            <i class="bx bx-loader-alt bx-spin"></i>
            <span>Terminating Trade...</span>
        `;


        const url =
            terminateUrlTemplate.replace(
                '__SUBSCRIPTION__',
                encodeURIComponent(
                    subscriptionId
                )
            );


        try {

            const response =
                await fetch(
                    url,
                    {
                        method: 'POST',

                        headers: {

                            'X-CSRF-TOKEN':
                                '{{ csrf_token() }}',

                            'Accept':
                                'application/json',

                            'X-Requested-With':
                                'XMLHttpRequest'

                        }
                    }
                );


            let result = {};

            try {

                result =
                    await response.json();

            } catch (jsonError) {

                throw new Error(
                    'The server returned an invalid response.'
                );

            }


            if (
                !response.ok ||
                !result.success
            ) {

                throw new Error(
                    result.message ||
                    'Unable to terminate the bot trade.'
                );

            }


            /*
             * -----------------------------------------------------
             * UPDATE WALLET BALANCE ON SCREEN
             * -----------------------------------------------------
             */
            const wallet =
                document.getElementById(
                    'walletBalance'
                );


            if (
                wallet &&
                result.wallet_credit !== undefined
            ) {

                const currentBalance =
                    Number(
                        wallet.textContent
                            .replace(
                                /,/g,
                                ''
                            )
                    );


                const walletCredit =
                    Number(
                        result.wallet_credit
                    );


                if (
                    Number.isFinite(
                        currentBalance
                    ) &&
                    Number.isFinite(
                        walletCredit
                    )
                ) {

                    wallet.textContent =
                        (
                            currentBalance +
                            walletCredit
                        ).toLocaleString(
                            'en-US',
                            {
                                minimumFractionDigits:
                                    4,

                                maximumFractionDigits:
                                    4
                            }
                        );

                }

            }


            /*
             * -----------------------------------------------------
             * STOP SIMULATION TIMERS
             * -----------------------------------------------------
             */
            const card =
                button.closest(
                    '.simulation-card'
                );


            if (card) {

                if (card._pollTimer) {

                    clearInterval(
                        card._pollTimer
                    );

                    card._pollTimer =
                        null;

                }


                if (card._uiTimer) {

                    clearInterval(
                        card._uiTimer
                    );

                    card._uiTimer =
                        null;

                }


                /*
                 * Update status immediately.
                 */
                const status =
                    card.querySelector(
                        '.simulation-status'
                    );


                if (status) {

                    status.textContent =
                        'terminated';

                }


                /*
                 * Update P/L.
                 */
                const profit =
                    card.querySelector(
                        '.simulation-current-profit'
                    );


                if (
                    profit &&
                    result.current_profit !== undefined
                ) {

                    const currentProfit =
                        Number(
                            result.current_profit
                        );


                    profit.innerHTML = `
                        ${formatMoney(currentProfit)}

                        <span
                            class="text-[10px] text-muted"
                        >
                            USDT
                        </span>
                    `;


                    profit.classList.toggle(
                        'text-accent',
                        currentProfit >= 0
                    );


                    profit.classList.toggle(
                        'text-red-400',
                        currentProfit < 0
                    );

                }


                /*
                 * Replace termination button.
                 */
                button.innerHTML = `
                    <i class="bx bx-check-circle"></i>
                    <span>Trade Terminated</span>
                `;


                button.disabled = true;


                button.style.color =
                    '#00ff85';

                button.style.borderColor =
                    'rgba(0,255,133,.3)';

                button.style.background =
                    'rgba(0,255,133,.08)';

            }


            /*
             * Show result popup.
             */
            if (
                typeof window.showBotPopup ===
                'function'
            ) {

                window.showBotPopup(
                    'Bot Trade Terminated',
                    result.message ||
                    'The bot trade has been terminated successfully. Your capital and accumulated P/L have been returned to your USDT-TRC20 wallet.'
                );

            } else {

                alert(
                    result.message ||
                    'Bot trade terminated successfully.'
                );

            }


        } catch (error) {

            console.error(
                'Bot termination error:',
                error
            );


            /*
             * Restore button.
             */
            button.disabled = false;

            button.innerHTML =
                originalHtml;


            if (
                typeof window.showBotPopup ===
                'function'
            ) {

                window.showBotPopup(
                    'Termination Failed',
                    error.message ||
                    'Unable to terminate the bot trade. Please try again.'
                );

            } else {

                alert(
                    error.message ||
                    'Unable to terminate the bot trade.'
                );

            }

        }

    }


    /*
     * Event delegation.
     *
     * This also works for simulation cards
     * dynamically created after launching a bot.
     */
    document.addEventListener(
        'click',
        function (event) {

            const button =
                event.target.closest(
                    '.terminate-bot-btn'
                );


            if (!button) {
                return;
            }


            event.preventDefault();


            terminateBot(
                button
            );

        }
    );

})();
</script>


    @endpush

</x-user-layout>

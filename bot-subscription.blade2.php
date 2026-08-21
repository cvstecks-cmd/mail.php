@php

    $isLaunched =
        ($eventType ?? 'launched') === 'launched';

    $isCompleted =
        ($eventType ?? '') === 'completed';

    $isTerminated =
        ($eventType ?? '') === 'terminated';

    $finalProfitValue =
        (float) ($final_profit ?? 0);

    $profitIsPositive =
        $finalProfitValue > 0;

    $profitIsNegative =
        $finalProfitValue < 0;

    $profitLabel =
        $profitIsPositive
            ? 'Profit'
            : (
                $profitIsNegative
                    ? 'Loss'
                    : 'No Profit / Loss'
            );

    /*
     * Use the application's public asset URL.
     *
     * Change this path ONLY if your actual logo is stored
     * somewhere else.
     */
    $logoUrl =
        asset('images/logo.png');

@endphp

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        {{ $companyName }} - Trading Bot Notification
    </title>

</head>

<body
    style="
        margin:0;
        padding:0;
        background:#080b0d;
        font-family:Arial,Helvetica,sans-serif;
        color:#e8eef2;
    "
>

<table
    width="100%"
    cellpadding="0"
    cellspacing="0"
    border="0"
    style="
        width:100%;
        background:#080b0d;
        padding:30px 12px;
    "
>

<tr>

<td align="center">

<table
    width="620"
    cellpadding="0"
    cellspacing="0"
    border="0"
    style="
        width:100%;
        max-width:620px;
        background:#111619;
        border:1px solid #253238;
        border-radius:18px;
        overflow:hidden;
    "
>

<!-- =========================================================
     HEADER
========================================================= -->

<tr>

<td
    align="center"
    style="
        padding:30px 28px 24px;
        background:#0c1113;
        border-bottom:1px solid #253238;
    "
>

@if(
    file_exists(
        public_path('images/logo.png')
    )
)

<img
    src="{{ $logoUrl }}"
    alt="{{ $companyName }}"
    width="170"
    style="
        display:block;
        max-width:170px;
        height:auto;
        margin:0 auto 18px;
    "
>

@else

<div
    style="
        font-size:26px;
        line-height:32px;
        font-weight:800;
        letter-spacing:.5px;
        color:#00ff85;
        margin-bottom:18px;
    "
>
    {{ $companyName }}
</div>

@endif

<div
    style="
        font-size:12px;
        line-height:18px;
        letter-spacing:2px;
        text-transform:uppercase;
        color:#8c9aa2;
    "
>
    Automated Trading Notification
</div>

</td>

</tr>

<!-- =========================================================
     STATUS
========================================================= -->

<tr>

<td
    style="
        padding:32px 30px 10px;
    "
>

<div
    style="
        font-size:13px;
        line-height:20px;
        color:#8c9aa2;
        margin-bottom:8px;
    "
>
    {{ now()->format('M d, Y') }}
</div>

@if($isLaunched)

<h1
    style="
        margin:0;
        font-size:28px;
        line-height:36px;
        color:#ffffff;
        font-weight:800;
    "
>
    Trading Bot Activated
</h1>

<p
    style="
        margin:12px 0 0;
        font-size:15px;
        line-height:24px;
        color:#b7c3c9;
    "
>
    Your trading bot has been successfully activated
    and is now running its configured simulation.
</p>

@elseif($isCompleted)

<h1
    style="
        margin:0;
        font-size:28px;
        line-height:36px;
        color:#ffffff;
        font-weight:800;
    "
>
    Trading Bot Trade Completed
</h1>

<p
    style="
        margin:12px 0 0;
        font-size:15px;
        line-height:24px;
        color:#b7c3c9;
    "
>
    Your trading bot has completed its configured trading
    session.
</p>

@else

<h1
    style="
        margin:0;
        font-size:28px;
        line-height:36px;
        color:#ffffff;
        font-weight:800;
    "
>
    Trading Bot Trade Terminated
</h1>

<p
    style="
        margin:12px 0 0;
        font-size:15px;
        line-height:24px;
        color:#b7c3c9;
    "
>
    Your trading bot session has been terminated and the
    applicable balance has been settled.
</p>

@endif

</td>

</tr>

<!-- =========================================================
     GREETING
========================================================= -->

<tr>

<td
    style="
        padding:20px 30px 10px;
    "
>

<p
    style="
        margin:0;
        font-size:15px;
        line-height:25px;
        color:#d7e0e4;
    "
>
    Dear
    <strong style="color:#ffffff;">
        {{ $subscription->user->name }}
    </strong>,
</p>

</td>

</tr>

<!-- =========================================================
     SUMMARY CARD
========================================================= -->

<tr>

<td
    style="
        padding:20px 30px;
    "
>

<table
    width="100%"
    cellpadding="0"
    cellspacing="0"
    border="0"
    style="
        width:100%;
        background:#171e21;
        border:1px solid #2a373c;
        border-radius:14px;
    "
>

<tr>

<td
    style="
        padding:22px;
    "
>

<div
    style="
        font-size:11px;
        text-transform:uppercase;
        letter-spacing:1.5px;
        color:#00ff85;
        font-weight:700;
        margin-bottom:8px;
    "
>
    Trade Summary
</div>

<div
    style="
        font-size:22px;
        line-height:30px;
        font-weight:800;
        color:#ffffff;
    "
>
    {{ $bot->name }}
</div>

<table
    width="100%"
    cellpadding="0"
    cellspacing="0"
    border="0"
    style="
        margin-top:18px;
    "
>

<tr>

<td
    width="50%"
    style="
        padding:7px 0;
        color:#89979e;
        font-size:13px;
    "
>
    Trading Pair
</td>

<td
    width="50%"
    align="right"
    style="
        padding:7px 0;
        color:#ffffff;
        font-size:13px;
        font-weight:700;
    "
>
    {{ $trading_pair }}
</td>

</tr>

<tr>

<td
    style="
        padding:7px 0;
        color:#89979e;
        font-size:13px;
    "
>
    Investment
</td>

<td
    align="right"
    style="
        padding:7px 0;
        color:#ffffff;
        font-size:13px;
        font-weight:700;
    "
>
    {{ number_format($amount, 8) }} USDT
</td>

</tr>

<tr>

<td
    style="
        padding:7px 0;
        color:#89979e;
        font-size:13px;
    "
>
    Configured P/L Range
</td>

<td
    align="right"
    style="
        padding:7px 0;
        color:#00ff85;
        font-size:13px;
        font-weight:700;
    "
>
    {{ number_format($minFinalProfit, 2) }}
    to
    {{ number_format($maxFinalProfit, 2) }}
    USDT
</td>

</tr>

@if(
    !$isLaunched
)

<tr>

<td
    style="
        padding:7px 0;
        color:#89979e;
        font-size:13px;
    "
>
    Final P/L
</td>

<td
    align="right"
    style="
        padding:7px 0;
        font-size:14px;
        font-weight:800;
        color:
            {{ $profitIsPositive
                ? '#00ff85'
                : (
                    $profitIsNegative
                        ? '#ff6b6b'
                        : '#ffffff'
                )
            }};
    "
>
    {{ $profitIsPositive ? '+' : '' }}
    {{ number_format($finalProfitValue, 8) }}
    USDT
</td>

</tr>

@endif

</table>

</td>

</tr>

</table>

</td>

</tr>

<!-- =========================================================
     TRADING FEATURES
========================================================= -->

<tr>

<td
    style="
        padding:10px 30px 20px;
    "
>

<div
    style="
        font-size:18px;
        line-height:25px;
        font-weight:800;
        color:#ffffff;
        margin-bottom:12px;
    "
>
    Trading Features
</div>

<table
    width="100%"
    cellpadding="0"
    cellspacing="0"
    border="0"
    style="
        width:100%;
        border-collapse:collapse;
        background:#141a1d;
        border:1px solid #29363b;
    "
>

<tr>

<td
    style="
        padding:13px 14px;
        background:#1b2428;
        color:#00ff85;
        font-size:11px;
        font-weight:800;
        text-transform:uppercase;
        letter-spacing:.7px;
        border-bottom:1px solid #29363b;
    "
>
    Trading Features
</td>

<td
    style="
        padding:13px 14px;
        background:#1b2428;
        color:#00ff85;
        font-size:11px;
        font-weight:800;
        text-transform:uppercase;
        letter-spacing:.7px;
        border-bottom:1px solid #29363b;
    "
>
    Details
</td>

</tr>

<tr>

<td
    style="
        padding:13px 14px;
        color:#8e9ca3;
        font-size:13px;
        border-bottom:1px solid #29363b;
    "
>
    Trading Type
</td>

<td
    style="
        padding:13px 14px;
        color:#ffffff;
        font-size:13px;
        font-weight:700;
        border-bottom:1px solid #29363b;
    "
>
    {{ ucfirst($bot->bot_type ?? 'Trading Bot') }}
</td>

</tr>

<tr>

<td
    style="
        padding:13px 14px;
        color:#8e9ca3;
        font-size:13px;
        border-bottom:1px solid #29363b;
    "
>
    Strategy
</td>

<td
    style="
        padding:13px 14px;
        color:#ffffff;
        font-size:13px;
        font-weight:700;
        border-bottom:1px solid #29363b;
    "
>
    {{ $strategy }}
</td>

</tr>

<tr>

<td
    style="
        padding:13px 14px;
        color:#8e9ca3;
        font-size:13px;
    "
>
    Risk Level
</td>

<td
    style="
        padding:13px 14px;
        font-size:13px;
        font-weight:800;
        color:
            {{ $riskLevel === 'Low'
                ? '#00ff85'
                : (
                    $riskLevel === 'Moderate'
                        ? '#f5c542'
                        : (
                            $riskLevel === 'High'
                                ? '#ff9f43'
                                : '#ff6b6b'
                        )
                )
            }};
    "
>
    {{ $riskLevel }}
</td>

</tr>

</table>

</td>

</tr>

<!-- =========================================================
     EXPECTED PERFORMANCE
========================================================= -->

<tr>

<td
    style="
        padding:10px 30px 20px;
    "
>

<div
    style="
        font-size:18px;
        line-height:25px;
        font-weight:800;
        color:#ffffff;
        margin-bottom:12px;
    "
>
    Expected Performance
</div>

<table
    width="100%"
    cellpadding="0"
    cellspacing="0"
    border="0"
>

<tr>

<td
    width="50%"
    style="
        padding-right:7px;
    "
>

<table
    width="100%"
    cellpadding="0"
    cellspacing="0"
    border="0"
    style="
        background:#141c1f;
        border:1px solid #29363b;
    "
>

<tr>

<td
    style="
        padding:20px;
    "
>

<div
    style="
        color:#8e9ca3;
        font-size:11px;
        text-transform:uppercase;
        letter-spacing:.8px;
    "
>
    Win Rate
</div>

<div
    style="
        margin-top:7px;
        color:#00ff85;
        font-size:25px;
        line-height:32px;
        font-weight:800;
    "
>
    {{ number_format($winRate, 2) }}%
</div>

<div
    style="
        margin-top:5px;
        color:#718087;
        font-size:11px;
    "
>
    {{ $completedTradeCount }}
    completed trades
</div>

</td>

</tr>

</table>

</td>

<td
    width="50%"
    style="
        padding-left:7px;
    "
>

<table
    width="100%"
    cellpadding="0"
    cellspacing="0"
    border="0"
    style="
        background:#141c1f;
        border:1px solid #29363b;
    "
>

<tr>

<td
    style="
        padding:20px;
    "
>

<div
    style="
        color:#8e9ca3;
        font-size:11px;
        text-transform:uppercase;
        letter-spacing:.8px;
    "
>
    Average Daily Profit
</div>

<div
    style="
        margin-top:7px;
        color:#ffffff;
        font-size:25px;
        line-height:32px;
        font-weight:800;
    "
>
    {{ $averageDailyProfit >= 0 ? '+' : '' }}
    {{ number_format($averageDailyProfit, 2) }}%
</div>

<div
    style="
        margin-top:5px;
        color:#718087;
        font-size:11px;
    "
>
    Based on
    {{ $performanceDays }}
    trading days
</div>

</td>

</tr>

</table>

</td>

</tr>

</table>

</td>

</tr>

<!-- =========================================================
     COMPLETION / TERMINATION RESULT
========================================================= -->

@if(!$isLaunched)

<tr>

<td
    style="
        padding:10px 30px 25px;
    "
>

<table
    width="100%"
    cellpadding="0"
    cellspacing="0"
    border="0"
    style="
        background:
            {{ $isCompleted
                ? '#0e2018'
                : '#241416'
            }};
        border:1px solid
            {{ $isCompleted
                ? '#164f36'
                : '#573034'
            }};
    "
>

<tr>

<td
    align="center"
    style="
        padding:24px;
    "
>

<div
    style="
        font-size:11px;
        text-transform:uppercase;
        letter-spacing:1.2px;
        color:
            {{ $isCompleted
                ? '#00ff85'
                : '#ff7777'
            }};
        font-weight:800;
    "
>
    {{ $isCompleted ? 'Final Result' : 'Termination Result' }}
</div>

<div
    style="
        margin-top:8px;
        font-size:30px;
        line-height:38px;
        font-weight:900;
        color:
            {{ $profitIsPositive
                ? '#00ff85'
                : (
                    $profitIsNegative
                        ? '#ff7777'
                        : '#ffffff'
                )
            }};
    "
>
    {{ $profitIsPositive ? '+' : '' }}
    {{ number_format($finalProfitValue, 8) }}
    USDT
</div>

<div
    style="
        margin-top:5px;
        color:#aebbc0;
        font-size:13px;
    "
>
    {{ $profitLabel }}
</div>

</td>

</tr>

</table>

</td>

</tr>

@endif

<!-- =========================================================
     VIEW DASHBOARD BUTTON
========================================================= -->

<tr>

<td
    align="center"
    style="
        padding:5px 30px 30px;
    "
>

<a
    href="{{ route('bots.show', $bot->id) }}"
    style="
        display:inline-block;
        padding:14px 28px;
        background:#00ff85;
        color:#07100c;
        text-decoration:none;
        font-size:14px;
        font-weight:800;
        border-radius:9px;
    "
>
    View Bot Performance
</a>

</td>

</tr>

<!-- =========================================================
     IMPORTANT NOTES
========================================================= -->

<tr>

<td
    style="
        padding:0 30px 30px;
    "
>

<div
    style="
        padding:20px;
        background:#0d1315;
        border:1px solid #263238;
    "
>

<div
    style="
        color:#ffffff;
        font-size:14px;
        font-weight:800;
        margin-bottom:10px;
    "
>
    Important Information
</div>

<div
    style="
        color:#89979e;
        font-size:12px;
        line-height:21px;
    "
>
    • Your bot operates according to its configured
      trading parameters.<br>

    • Profit and loss figures shown in this message are
      based on the trading records maintained by the system.<br>

    • Historical performance does not guarantee future
      results.<br>

    • Trading involves financial risk.
</div>

</div>

</td>

</tr>

<!-- =========================================================
     FOOTER
========================================================= -->

<tr>

<td
    align="center"
    style="
        padding:24px 30px;
        background:#0c1113;
        border-top:1px solid #253238;
    "
>

<div
    style="
        color:#00ff85;
        font-size:15px;
        font-weight:800;
    "
>
    {{ $companyName }}
</div>

<div
    style="
        margin-top:8px;
        color:#718087;
        font-size:11px;
        line-height:18px;
    "
>
    For assistance:
    <a
        href="mailto:{{ $supportEmail }}"
        style="
            color:#aebbc0;
            text-decoration:none;
        "
    >
        {{ $supportEmail }}
    </a>
</div>

<div
    style="
        margin-top:12px;
        color:#526067;
        font-size:10px;
    "
>
    This is an automated notification.
    Please do not reply directly to this message.
</div>

</td>

</tr>

</table>

</td>

</tr>

</table>

</body>

</html>

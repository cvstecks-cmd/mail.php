@component('mail::message')
# Trading Bot Subscription Activated

Dear {{ $subscription->user->name }},

Your subscription to the {{ $bot->name }} trading bot has been successfully activated!

@component('mail::panel')
**Subscription Details**
- Bot: {{ $bot->name }}
- Investment Amount: {{ number_format($amount, 2) }} USDT
- Trading Pair: {{ $trading_pair }}
- Subscription Date: {{ $subscription->subscribed_at->format('M d, Y H:i:s') }}
- Expires: {{ $expires_at->format('M d, Y H:i:s') }}

**Bot Strategy**
{{ $bot->description }}

**Expected Performance**
- Win Rate: {{ $bot->win_rate }}%
- Average Daily Profit: {{ $bot->daily_profit }}%
@endcomponent

@component('mail::table')
| Trading Features | Details |
|-----------------|----------|
| Trading Type | {{ ucfirst($bot->bot_type) }} |
| Strategy | {{ ucfirst($bot->strategy) }} |
| Risk Level | {{ ucfirst($bot->risk_level) }} |
@endcomponent

You can monitor your bot's performance and profits in real-time from your dashboard. The bot will automatically execute trades based on market conditions and its trading strategy.

@component('mail::button', ['url' => route('bots.show', $bot->id)])
View Bot Performance
@endcomponent

**Important Notes:**
- The bot will trade automatically 24/7
- All profits are automatically added to your account
- You can view detailed trade history in your dashboard
- Trading involves risk and past performance does not guarantee future results

If you have any questions about your bot subscription or need assistance, please contact our support team at {{ $supportEmail }}.

Best regards,  
{{ $companyName }} Team
@endcomponent
<x-user-layout :title="'Bot History'">
    <main class="w-full mx-auto p-4 pb-24 md:pb-4 text-white">
        <!-- Header -->
        <div class="top">
            <div class="left-icons">
                <a href="{{ route('bots') }}" class="icon-btn"><i class='bx bx-arrow-back'></i></a>
            </div>
            <div class="title">Trading History</div>
            <div class="icon-btn"></div>
        </div>

        <div class="space-y-3">
            @forelse($trades as $trade)
                <div class="bg-[#1b1b1d] border border-white/5 p-4 rounded-xl">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h4 class="text-sm font-semibold text-white">{{ $trade->bot->name }}</h4>
                            <span class="text-[11px] text-muted">{{ $trade->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-accent/10 text-accent border border-accent/20">Completed</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mt-2 pt-2 border-t border-white/5">
                        <div>
                            <span class="text-[10px] text-muted uppercase">Invested</span>
                            <p class="text-xs font-medium text-white">${{ number_format($trade->amount, 2) }}</p>
                        </div>
                        <div>
                            <span class="text-[10px] text-muted uppercase">Profit/Loss</span>
                            <p class="text-xs font-semibold {{ $trade->result === 'win' ? 'text-accent' : 'text-danger' }}">
                                {{ $trade->result === 'win' ? '+$'.number_format($trade->profit, 2) : '-$'.number_format($trade->amount, 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-[#1b1b1d] border border-white/5 p-8 rounded-xl text-center text-muted text-xs">
                    No bot trading history found.
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-5 select-none text-xs">
            {{ $trades->links() }}
        </div>
    </main>
</x-user-layout>

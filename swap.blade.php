<x-user-layout :title="'Swap'">
    <!-- Header -->
    <div class="top">
        <div class="left-icons">
            <a href="{{ route('dashboard') }}" class="icon-btn"><i class='bx bx-arrow-back'></i></a>
        </div>
        <div class="title">Swap</div>
        <button class="icon-btn" onclick="window.showToast('Swap options coming soon!')"><i class='bx bx-dots-vertical-rounded'></i></button>
    </div>

    <main class="w-full mx-auto px-4 pb-24 pt-2 md:pb-4 text-white">

        <div class="swap-container">
            <!-- Coin Selector visual preview at the top -->
            <div class="coin-selectors">
                <div class="coin-selector">
                    <div class="coin-logo" id="fromCoinLogoContainer">
                        <i class="fas fa-coins text-2xl text-blue-500"></i>
                    </div>
                    <h3 class="coin-name" id="fromCoinName">Select</h3>
                    <div class="coin-balance" id="fromCoinBalance">Balance: 0.00</div>
                    <div class="coin-price" id="fromCoinPrice">$0.00</div>
                    <button type="button" class="coin-select-btn" onclick="openCryptoSelector('from')">Change</button>
                </div>
                
                <div class="swap-arrow" onclick="swapDirections()">
                    <i class='bx bx-transfer-alt'></i>
                </div>
                
                <div class="coin-selector">
                    <div class="coin-logo" id="toCoinLogoContainer">
                        <i class="fas fa-coins text-2xl text-blue-500"></i>
                    </div>
                    <h3 class="coin-name" id="toCoinName">Select</h3>
                    <div class="coin-balance" id="toCoinBalance">Balance: 0.00</div>
                    <div class="coin-price" id="toCoinPrice">$0.00</div>
                    <button type="button" class="coin-select-btn" onclick="openCryptoSelector('to')">Change</button>
                </div>
            </div>

            <!-- Swap Form -->
            <form id="swapForm" class="form-container">
                @csrf
                <div class="form-group">
                    <label class="form-label">You Pay</label>
                    <div class="amount-container">
                        <div class="coin-input">
                            <div class="coin-input-logo" id="fromInputLogoContainer">
                                <i class="fas fa-coins text-gray-400"></i>
                            </div>
                            <div class="coin-input-symbol" id="fromInputSymbol">Select</div>
                            <input type="number" 
                                   id="fromAmount" 
                                   name="amount" 
                                   placeholder="0.00"
                                   min="0"
                                   step="any"
                                   required
                                   class="form-input"
                                   oninput="calculateSwap()">
                        </div>
                        <button type="button" class="max-btn" id="maxBtn" onclick="setAmount(100)">MAX</button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">You Receive</label>
                    <div class="amount-container">
                        <div class="coin-input">
                            <div class="coin-input-logo" id="toInputLogoContainer">
                                <i class="fas fa-coins text-gray-400"></i>
                            </div>
                            <div class="coin-input-symbol" id="toInputSymbol">Select</div>
                            <input type="number" 
                                   id="toAmount" 
                                   readonly
                                   placeholder="0.00"
                                   class="form-input">
                        </div>
                    </div>
                </div>

                <div class="swap-details">
                    <div class="swap-item">
                        <span>Exchange Rate:</span>
                        <span id="rate">-</span>
                    </div>
                    <div class="swap-item">
                        <span>Swap Fee (0.3%):</span>
                        <span id="feeAmount">0.00</span>
                    </div>
                    <div class="swap-item">
                        <span>Network Fee:</span>
                        <span>0.0001 BTC</span>
                    </div>
                </div>

                <button type="submit" 
                        id="swapButton"
                        disabled
                        class="submit-btn">
                    Select currencies
                </button>
            </form>
        </div>

        <!-- Swap History Section -->
        <div class="mt-8 pb-24" style="padding: 0 8px;">
            <h2 class="text-lg font-semibold mb-4">Recent Swaps</h2>
            <div class="space-y-3">
                @forelse($swapTransactions as $transaction)
                    @php
                        // Determine status styling
                        $isPending = $transaction->status === 'pending';
                        $isFailed = $transaction->status === 'failed';
                        $isCompleted = $transaction->status === 'completed';
                        
                        // Status-based styling
                        if ($isPending) {
                            $iconBg = 'bg-yellow-50 dark:bg-yellow-900/20';
                            $iconColor = 'text-yellow-500';
                            $icon = 'fa-clock';
                            $statusText = 'Pending Approval';
                        } elseif ($isFailed) {
                            $iconBg = 'bg-red-50 dark:bg-red-900/20';
                            $iconColor = 'text-red-500';
                            $icon = 'fa-times';
                            $statusText = 'Failed';
                        } else {
                            $iconBg = 'bg-blue-50 dark:bg-blue-900/20';
                            $iconColor = 'text-blue-500';
                            $icon = 'fa-exchange-alt';
                            $statusText = 'Completed';
                        }
                        
                        // Get cryptocurrency display names
                        $fromCryptoName = $transaction->fromCryptocurrency ? $transaction->fromCryptocurrency->getDisplayName() : strtoupper($transaction->from_crypto);
                        $toCryptoName = $transaction->toCryptocurrency ? $transaction->toCryptocurrency->getDisplayName() : strtoupper($transaction->to_crypto);
                        
                        // Get USD value from metadata
                        $usdVal = $transaction->metadata['usd_value'] ?? 0;
                        
                        // Format amounts
                        $amountIn = number_format($transaction->amount_in, 6);
                        $amountOut = number_format($transaction->amount_out, 6);
                        
                        // Format date
                        $formattedDate = $transaction->created_at->format('M d, Y • h:i A');
                    @endphp
                    
                    <div class="flex items-center justify-between p-4 bg-[#1b1b1d] rounded-xl border border-white/5">
                        <!-- Left side: Icon + Details -->
                        <div class="flex items-center flex-1 min-w-0">
                            <div class="w-10 h-10 {{ $iconBg }} rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas {{ $icon }} {{ $iconColor }}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-sm text-white truncate">
                                    {{ $fromCryptoName }} <i class="fas fa-arrow-right text-xs text-muted mx-1"></i> {{ $toCryptoName }}
                                </h3>
                                <p class="text-xs text-muted mt-0.5">{{ $formattedDate }}</p>
                                @if($isPending)
                                    <span class="inline-block mt-1 text-[10px] px-2 py-0.5 rounded-full bg-yellow-900/30 text-yellow-500 font-semibold">{{ $statusText }}</span>
                                @elseif($isFailed)
                                    <span class="inline-block mt-1 text-[10px] px-2 py-0.5 rounded-full bg-red-900/30 text-red-500 font-semibold">{{ $statusText }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Right side: Amount + USD value -->
                        <div class="text-right ml-4 flex-shrink-0">
                            <p class="font-semibold text-sm text-white">
                                {{ $amountIn }} → {{ $amountOut }}
                            </p>
                            <p class="text-xs text-muted mt-0.5">
                                ≈ ${{ number_format($usdVal, 2) }}
                            </p>
                        </div>
                    </div>
                @empty
                    <!-- Empty state -->
                    <div class="flex flex-col items-center justify-center py-12 bg-[#1b1b1d] rounded-xl border border-white/5">
                        <div class="w-16 h-16 bg-[#262628] rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-exchange-alt text-2xl text-muted"></i>
                        </div>
                        <h3 class="text-base font-semibold text-white mb-1">No swap history yet</h3>
                        <p class="text-xs text-muted">Start swapping cryptocurrencies above</p>
                    </div>
                @endforelse
            </div>
        </div>
    </main>

    <!-- Crypto Selector Modal -->
    <div id="cryptoSelectorModal" class="modal-overlay" style="display: none; justify-content: center; align-items: center;">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Select Asset</h3>
                <button type="button" onclick="closeCryptoSelector()" class="modal-close">
                    <i class="bx bx-x"></i>
                </button>
            </div>
            <div class="search-container">
                <input type="text" class="search-input" id="coinSearch" placeholder="Search coins..." onkeyup="filterCoins()">
            </div>
            <div class="coins-list" id="cryptoList">
                <!-- Crypto list will be populated dynamically -->
            </div>
        </div>
    </div>

    <!-- Result Modal -->
    <div id="resultModal" class="modal-overlay" style="display: none; justify-content: center; align-items: center;">
        <div class="modal" style="padding: 24px;">
            <div class="text-center">
                <!-- Success State -->
                <div id="successState" class="hidden">
                    <div class="w-16 h-16 bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check text-2xl text-green-400"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-1">Swap Successful!</h3>
                    <p class="text-muted mb-6 text-xs">Your transaction has been completed successfully.</p>
                </div>

                <!-- Pending Approval State -->
                <div id="pendingState" class="hidden">
                    <div class="w-16 h-16 bg-yellow-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-2xl text-yellow-400"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-1">Swap Pending Approval</h3>
                    <p class="text-muted mb-6 text-xs">Your swap request has been submitted successfully and is awaiting admin approval.</p>
                </div>

                <!-- Error State -->
                <div id="errorState" class="hidden">
                    <div class="w-16 h-16 bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-times text-2xl text-red-500"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-1">Swap Failed</h3>
                    <p id="errorMessage" class="text-muted mb-6 text-xs"></p>
                </div>

                <!-- Transaction Details -->
                <div id="transactionDetails" class="space-y-4 hidden text-left">
                    <div class="bg-[#262628] rounded-xl p-4">
                        <div class="flex justify-between text-xs mb-3">
                            <span class="text-muted">From</span>
                            <span id="resultFromAmount" class="font-semibold text-white"></span>
                        </div>
                        <div class="flex justify-between text-xs mb-3">
                            <span class="text-muted">To</span>
                            <span id="resultToAmount" class="font-semibold text-white"></span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-muted">Rate</span>
                            <span id="resultRate" class="font-semibold text-white"></span>
                        </div>
                    </div>
                </div>

                <button type="button" onclick="closeModal()" 
                        class="submit-btn" style="margin-top: 24px;">
                    Done
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let cryptoData = {
            from: { crypto: '', balance: 0, price: 0 },
            to: { crypto: '', balance: 0, price: 0 }
        };

        // Build prices, balances, and crypto config from database assets
        let prices = {};
        let balances = {};
        let CRYPTO_CONFIG = {};
        @foreach($assets as $asset)
            @php
                $lowerSymbol = strtolower($asset['symbol']);
            @endphp
            prices['{{ $lowerSymbol }}'] = {{ $asset['price'] ?? 0 }};
            balances['{{ $lowerSymbol }}'] = {{ $asset['balance'] ?? 0 }};
            CRYPTO_CONFIG['{{ $lowerSymbol }}'] = {
                icon: '{{ $asset['icon_url'] }}',
                name: '{{ $asset['name'] }}',
                symbol: '{{ $asset['symbol'] }}'
            };
        @endforeach
        
        let currentSelector = '';

        function getDisplayName(crypto) {
            if (CRYPTO_CONFIG[crypto] && CRYPTO_CONFIG[crypto].name) {
                return CRYPTO_CONFIG[crypto].name;
            }
            return crypto.split('_').map(part => part.toUpperCase()).join(' ');
        }

        function getPriceForCrypto(crypto) {
            return prices[crypto] || 0;
        }

        function updateUI() {
            const swapButton = document.getElementById('swapButton');
            if (cryptoData.from.crypto && cryptoData.to.crypto) {
                swapButton.removeAttribute('disabled');
                swapButton.textContent = `Swap ${getDisplayName(cryptoData.from.crypto)} to ${getDisplayName(cryptoData.to.crypto)}`;
            } else {
                swapButton.setAttribute('disabled', 'disabled');
                swapButton.textContent = 'Select currencies';
            }
            calculateSwap();
        }

        function calculateSwap() {
            const fromAmount = parseFloat(document.getElementById('fromAmount').value) || 0;
            if (fromAmount && cryptoData.from.price && cryptoData.to.price) {
                const usdValue = fromAmount * cryptoData.from.price;
                const toAmount = usdValue / cryptoData.to.price;
                
                document.getElementById('toAmount').value = toAmount.toFixed(6);
                
                // Swap fee calculation (0.3%)
                const fee = fromAmount * 0.003;
                document.getElementById('feeAmount').textContent = `${fee.toFixed(6)} ${getDisplayName(cryptoData.from.crypto)}`;
                
                if (cryptoData.from.crypto && cryptoData.to.crypto) {
                    const rate = toAmount / fromAmount;
                    document.getElementById('rate').textContent = 
                        `1 ${getDisplayName(cryptoData.from.crypto)} = ${rate.toFixed(6)} ${getDisplayName(cryptoData.to.crypto)}`;
                }
            } else {
                document.getElementById('toAmount').value = '';
                document.getElementById('feeAmount').textContent = '0.00';
                document.getElementById('rate').textContent = '-';
            }
        }

        function openCryptoSelector(type) {
            currentSelector = type;
            const modal = document.getElementById('cryptoSelectorModal');
            const cryptoList = document.getElementById('cryptoList');
            cryptoList.innerHTML = '';

            // Reset search input
            document.getElementById('coinSearch').value = '';

            const selectedFrom = cryptoData.from.crypto;
            const selectedTo = cryptoData.to.crypto;

            Object.keys(CRYPTO_CONFIG).forEach(crypto => {
                if (balances[crypto] !== undefined && 
                    crypto !== (type === 'from' ? selectedTo : selectedFrom)) {
                    
                    const balance = balances[crypto];
                    const config = CRYPTO_CONFIG[crypto];
                    const displayName = getDisplayName(crypto);
                    
                    const item = document.createElement('div');
                    item.className = 'coin-item';
                    item.onclick = () => selectCrypto(crypto);
                    
                    item.innerHTML = `
                        <div class="coin-item-logo">
                            <img src="${config.icon}" alt="crypto icon">
                        </div>
                        <div class="coin-item-info">
                            <div class="coin-item-name text-white">${displayName}</div>
                            <div class="coin-item-symbol">${config.symbol.toUpperCase()}</div>
                        </div>
                        <div class="coin-item-balance text-right text-white">
                            ${balance}
                        </div>
                    `;
                    
                    cryptoList.appendChild(item);
                }
            });

            modal.style.display = 'flex';
            modal.classList.add('active');
        }

        function closeCryptoSelector() {
            const modal = document.getElementById('cryptoSelectorModal');
            modal.classList.remove('active');
            modal.style.display = 'none';
            currentSelector = '';
        }

        function filterCoins() {
            const input = document.getElementById('coinSearch');
            const filter = input.value.toUpperCase();
            const list = document.getElementById('cryptoList');
            const items = list.getElementsByClassName('coin-item');
            
            for (let i = 0; i < items.length; i++) {
                const name = items[i].querySelector('.coin-item-name').textContent;
                const symbol = items[i].querySelector('.coin-item-symbol').textContent;
                if (name.toUpperCase().indexOf(filter) > -1 || symbol.toUpperCase().indexOf(filter) > -1) {
                    items[i].style.display = "flex";
                } else {
                    items[i].style.display = "none";
                }
            }
        }

        function updateCoinSelectorUI(selectorType) {
            const data = cryptoData[selectorType];
            const logoContainer = document.getElementById(`${selectorType}CoinLogoContainer`);
            const nameEl = document.getElementById(`${selectorType}CoinName`);
            const balanceEl = document.getElementById(`${selectorType}CoinBalance`);
            const priceEl = document.getElementById(`${selectorType}CoinPrice`);
            
            const inputLogoContainer = document.getElementById(`${selectorType}InputLogoContainer`);
            const inputSymbolEl = document.getElementById(`${selectorType}InputSymbol`);

            if (data.crypto) {
                const config = CRYPTO_CONFIG[data.crypto];
                const displayName = getDisplayName(data.crypto);
                
                logoContainer.innerHTML = `<img src="${config.icon}" alt="${displayName}" class="w-full h-full object-cover">`;
                nameEl.textContent = displayName;
                balanceEl.textContent = `Balance: ${data.balance}`;
                priceEl.textContent = `$${data.price.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                
                inputLogoContainer.innerHTML = `<img src="${config.icon}" alt="${displayName}" class="w-full h-full object-cover">`;
                inputSymbolEl.textContent = config.symbol.toUpperCase();
            } else {
                logoContainer.innerHTML = `<i class="fas fa-coins text-2xl text-blue-500"></i>`;
                nameEl.textContent = 'Select';
                balanceEl.textContent = 'Balance: 0.00';
                priceEl.textContent = '$0.00';
                
                inputLogoContainer.innerHTML = `<i class="fas fa-coins text-gray-400"></i>`;
                inputSymbolEl.textContent = 'Select';
            }
        }

        function selectCrypto(crypto) {
            const config = CRYPTO_CONFIG[crypto];
            const displayName = getDisplayName(crypto);
            const balance = balances[crypto];
            const price = getPriceForCrypto(crypto);

            cryptoData[currentSelector] = {
                crypto: crypto,
                balance: balance,
                price: price
            };

            updateCoinSelectorUI(currentSelector);
            closeCryptoSelector();
            updateUI();
        }

        function swapDirections() {
            // Swap state
            const temp = {...cryptoData.from};
            cryptoData.from = {...cryptoData.to};
            cryptoData.to = temp;

            // Update UI for both
            updateCoinSelectorUI('from');
            updateCoinSelectorUI('to');

            // Swap amounts or recalculate
            calculateSwap();
            updateUI();
        }

        function setAmount(percentage) {
            if (!cryptoData.from.crypto) return;
            
            const maxAmount = cryptoData.from.balance;
            const amount = (maxAmount * percentage) / 100;
            document.getElementById('fromAmount').value = amount.toFixed(8);
            calculateSwap();
        }

        function showSuccess(data, status = 'completed') {
            const modal = document.getElementById('resultModal');
            
            document.getElementById('successState').classList.add('hidden');
            document.getElementById('pendingState').classList.add('hidden');
            document.getElementById('errorState').classList.add('hidden');
            
            if (status === 'pending') {
                document.getElementById('pendingState').classList.remove('hidden');
            } else {
                document.getElementById('successState').classList.remove('hidden');
            }
            
            document.getElementById('transactionDetails').classList.remove('hidden');
            
            document.getElementById('resultFromAmount').textContent = 
                `${data.from_amount} ${getDisplayName(data.from_crypto)}`;
            document.getElementById('resultToAmount').textContent = 
                `${data.to_amount} ${getDisplayName(data.to_crypto)}`;
            document.getElementById('resultRate').textContent = 
                `1 ${getDisplayName(data.from_crypto)} = ${data.rate.toFixed(6)} ${getDisplayName(data.to_crypto)}`;
            
            modal.style.display = 'flex';
            modal.classList.add('active');
        }

        function showError(message) {
            const modal = document.getElementById('resultModal');
            document.getElementById('successState').classList.add('hidden');
            document.getElementById('pendingState').classList.add('hidden');
            document.getElementById('errorState').classList.remove('hidden');
            document.getElementById('transactionDetails').classList.add('hidden');
            document.getElementById('errorMessage').textContent = message;
            modal.style.display = 'flex';
            modal.classList.add('active');
        }

        function closeModal() {
            const modal = document.getElementById('resultModal');
            modal.classList.remove('active');
            modal.style.display = 'none';
            window.location.reload(); 
        }

        // Form submission
        document.getElementById('swapForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!cryptoData.from.crypto || !cryptoData.to.crypto) {
                showError('Please select currencies');
                return;
            }

            const amount = parseFloat(document.getElementById('fromAmount').value);
            if (!amount || amount <= 0) {
                showError('Please enter a valid amount');
                return;
            }

            if (amount > cryptoData.from.balance) {
                showError('Insufficient balance');
                return;
            }

            const swapButton = document.getElementById('swapButton');
            const originalText = swapButton.textContent;
            swapButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';
            swapButton.disabled = true;

            try {
                const response = await fetch('{{ route("swap.process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        from_crypto: cryptoData.from.crypto,
                        to_crypto: cryptoData.to.crypto,
                        amount: amount
                    })
                });

                const result = await response.json();
                if (result.success) {
                    showSuccess(result.data, result.status);
                } else {
                    showError(result.message);
                }
            } catch (error) {
                console.error('Swap error:', error);
                showError('An error occurred while processing your swap');
            } finally {
                swapButton.innerHTML = originalText;
                swapButton.disabled = false;
            }
        });
    </script>
    @endpush
</x-user-layout>

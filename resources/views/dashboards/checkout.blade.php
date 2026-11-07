<!DOCTYPE html>
<html lang="id" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout — MarketHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' };
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="auth-token" content="{{ session('auth_token') }}">
</head>
<body class="bg-slate-50 dark:bg-slate-900 transition-colors duration-300">

    <nav class="bg-white dark:bg-slate-800 shadow-sm border-b border-slate-200 dark:border-slate-700">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <h1 class="font-black text-xl text-indigo-600">Market<span class="text-slate-800 dark:text-white">Hub</span></h1>
                <a href="{{ route('user.dashboard') }}" class="text-indigo-600 dark:text-indigo-400 font-medium hover:underline text-sm">
                    &larr; Kembali Belanja
                </a>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto mt-10 px-4 sm:px-6 lg:px-8 pb-20">

        <h2 class="text-3xl font-black text-slate-800 dark:text-white mb-2">Checkout</h2>
        <p class="text-slate-500 dark:text-slate-400 mb-8">Periksa pesanan Anda sebelum melanjutkan pembayaran.</p>

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Loading state --}}
        <div id="checkout-loading" class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-12 text-center">
            <svg class="w-8 h-8 text-blue-500 animate-spin mx-auto mb-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            <p class="text-slate-400">Memuat pesanan...</p>
        </div>

        {{-- Ringkasan Pesanan --}}
        <div id="checkout-card" class="hidden bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">Ringkasan Pesanan</h3>
            </div>
            <div id="checkout-items" class="divide-y divide-slate-100 dark:divide-slate-700"></div>

            {{-- Empty state --}}
            <div id="empty-cart-msg" class="hidden p-12 text-center">
                <p class="text-slate-400 dark:text-slate-500 text-lg mb-4">Keranjang Anda kosong.</p>
                <a href="{{ route('user.dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-xl transition text-sm">
                    Mulai Belanja
                </a>
            </div>

            {{-- Totals --}}
            <div id="checkout-totals" class="hidden p-6 bg-slate-50 dark:bg-slate-800/50 rounded-b-2xl">
                <div class="flex justify-between text-sm text-slate-500 dark:text-slate-400 mb-2">
                    <span>Total Item</span>
                    <span id="total-items-count">0</span>
                </div>
                <div class="flex justify-between text-xl font-black text-slate-800 dark:text-white border-t border-slate-200 dark:border-slate-600 pt-3 mt-3">
                    <span>Total Pembayaran</span>
                    <span id="grand-total-display">Rp 0</span>
                </div>
            </div>
        </div>

        {{-- Checkout form --}}
        <form id="checkout-form" action="{{ route('user.checkout.process') }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="items" id="checkout-items-json">
            <input type="hidden" name="mode" id="checkout-mode-field" value="{{ $mode }}">
            <button type="submit" id="checkout-btn" class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-bold py-4 rounded-2xl transition text-lg shadow-md" disabled>
                Bayar Sekarang
            </button>
        </form>

    </main>

    <script>
        const CSRF    = document.querySelector('meta[name="csrf-token"]').content;
        const MODE    = "{{ $mode }}";
        const BUYNOW  = @json($buynow);

        function formatRupiah(n) {
            return 'Rp ' + parseInt(n).toLocaleString('id-ID');
        }

        async function loadCheckout() {
            const loading = document.getElementById('checkout-loading');
            const card    = document.getElementById('checkout-card');
            const form    = document.getElementById('checkout-form');

            try {
                let items = [];

                if (MODE === 'buynow' && BUYNOW) {
                    items = [{
                        product_id:  BUYNOW.product_id,
                        productName: BUYNOW.name,
                        price:       BUYNOW.price,
                        quantity:    BUYNOW.quantity,
                        subtotal:    BUYNOW.price * BUYNOW.quantity,
                    }];
                } else {
                    // Fetch from server-side cart API with Sanctum Bearer token
                    const token = document.querySelector('meta[name="auth-token"]')?.content || localStorage.getItem('auth_token') || '';
                    const headers = {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    };
                    if (token) {
                        headers['Authorization'] = 'Bearer ' + token;
                    }

                    const res = await fetch('/api/cart', {
                        headers: headers,
                        credentials: 'same-origin',
                    });
                    const data = await res.json();
                    if (data.success) {
                        items = (data.data.items || []).map(i => ({
                            product_id:  i.productId || i.product_id,
                            productName: i.productName || i.name,
                            price:       i.price,
                            quantity:    i.quantity,
                            subtotal:    i.subtotal,
                        }));
                    }
                }

                loading.classList.add('hidden');
                card.classList.remove('hidden');
                form.classList.remove('hidden');

                render(items);
            } catch {
                loading.innerHTML = '<p class="text-red-500">Gagal memuat data checkout. <a href="javascript:location.reload()" class="underline">Muat ulang</a></p>';
            }
        }

        function render(items) {
            const container = document.getElementById('checkout-items');
            const emptyMsg  = document.getElementById('empty-cart-msg');
            const totals    = document.getElementById('checkout-totals');
            const btn       = document.getElementById('checkout-btn');
            const jsonInput = document.getElementById('checkout-items-json');

            container.innerHTML = '';

            if (!items || items.length === 0) {
                emptyMsg.classList.remove('hidden');
                totals.classList.add('hidden');
                btn.disabled = true;
                jsonInput.value = '[]';
                return;
            }

            emptyMsg.classList.add('hidden');
            totals.classList.remove('hidden');
            btn.disabled = false;

            let totalItems = 0, grandTotal = 0;

            items.forEach(item => {
                const subtotal = item.subtotal || (item.price * item.quantity);
                totalItems += item.quantity;
                grandTotal += subtotal;

                const row = document.createElement('div');
                row.className = 'p-6 flex items-start gap-4';
                row.innerHTML = `
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800 dark:text-white text-base">${item.productName}</p>
                        <p class="text-slate-500 dark:text-slate-400 text-sm mt-0.5">${formatRupiah(item.price)} / item</p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="text-slate-500 dark:text-slate-400 text-sm">× ${item.quantity}</span>
                        <span class="font-bold text-slate-800 dark:text-white text-sm">${formatRupiah(subtotal)}</span>
                    </div>
                `;
                container.appendChild(row);
            });

            document.getElementById('total-items-count').textContent = totalItems + ' item';
            document.getElementById('grand-total-display').textContent = formatRupiah(grandTotal);
            // Submit only the product_id and quantity (prices are re-read server-side)
            jsonInput.value = JSON.stringify(items.map(i => ({ product_id: i.product_id, quantity: i.quantity })));
        }

        loadCheckout();
    </script>
</body>
</html>

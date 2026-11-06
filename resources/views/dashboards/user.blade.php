<!DOCTYPE html>
<html lang="id" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengguna</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' };
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    {{-- Sanctum CSRF cookie (needed for session-authenticated API requests) --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-slate-50 dark:bg-slate-900 transition-colors duration-300 relative">

    {{-- ─── NAVBAR ─────────────────────────────────────────────────────────────── --}}
    <nav class="bg-white dark:bg-slate-800 shadow-sm border-b border-slate-200 dark:border-slate-700">
        <div class="w-full px-4 sm:px-6 flex items-center h-20 relative">

            {{-- Toggle Light/Dark Mode --}}
            <div class="absolute left-4 sm:left-6 z-10">
                <button onclick="toggleTheme()" class="relative flex items-center w-[120px] h-[46px] rounded-full transition-colors duration-300 bg-[#b3b3b3] dark:bg-[#1a1a1a] border-2 border-transparent focus:outline-none overflow-hidden shadow-sm hover:opacity-90">
                    <span class="absolute left-3 text-white font-extrabold text-[11px] leading-tight tracking-wider dark:opacity-0 transition-opacity duration-300">LIGHT<br>MODE</span>
                    <span class="absolute right-3 text-white font-extrabold text-[11px] leading-tight tracking-wider opacity-0 dark:opacity-100 transition-opacity duration-300 text-right">DARK<br>MODE</span>
                    <span class="absolute right-[4px] dark:-translate-x-[74px] w-9 h-9 bg-white rounded-full transition-transform duration-300 flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-[#a0a0a0] dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <svg class="w-5 h-5 text-black hidden dark:block" viewBox="0 0 24 24" fill="currentColor"><path d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"></path></svg>
                    </span>
                </button>
            </div>

            {{-- Logo + Nav Items --}}
            <div class="w-full flex items-center pl-[140px]">
                <h1 class="font-black text-2xl text-indigo-600 tracking-tight">Market<span class="text-slate-800 dark:text-white">Hub</span></h1>

                {{-- Nav items: Order History, Cart, Toko Saya, Settings --}}
                <div class="flex items-center gap-5 ml-auto">

                    {{-- 🧾 Order History Button --}}
                    <a href="{{ route('user.orders') }}" class="p-2 text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition" title="Riwayat Pesanan">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </a>

                    {{-- 🛒 Cart Button --}}
                    <button id="btn-open-cart" onclick="openCart()" class="relative p-2 text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition" title="Keranjang Belanja">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span id="cart-badge" class="hidden absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-blue-600 text-white text-[10px] font-black rounded-full flex items-center justify-center px-1 leading-none">0</span>
                    </button>

                    {{-- Toko Saya / Menjadi Seller --}}
                    @if(Auth::user()->role === 'seller')
                        <a href="{{ route('seller.dashboard') }}" class="bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2 rounded-full text-sm font-bold shadow-sm transition">Toko Saya</a>
                    @else
                        <a href="{{ route('become.seller') }}" class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2 rounded-full text-sm font-bold shadow-sm transition">Menjadi Seller?</a>
                    @endif

                    {{-- Settings --}}
                    <a href="{{ route('settings', ['context' => 'user']) }}" class="text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400 transition" title="Pengaturan">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </a>
                </div>
            </div>

        </div>
    </nav>

    {{-- ─── MAIN CONTENT ───────────────────────────────────────────────────────── --}}
    <main class="max-w-screen-2xl mx-auto mt-10 px-4 sm:px-6 lg:px-8 pb-12">

        {{-- Notifikasi --}}
        @if(session('success'))
            <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 p-4 mb-6 rounded-r shadow-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Header & Pencarian --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h2 class="text-3xl font-black text-slate-800 dark:text-white">Jelajahi Produk</h2>
                <p class="text-slate-500 dark:text-slate-400 mt-1">Temukan barang impianmu di sini.</p>
            </div>
            <form action="{{ route('user.dashboard') }}" method="GET" class="flex w-full md:w-1/3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk (contoh: mouse)..." class="w-full px-4 py-3 rounded-l-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-r-xl font-bold transition">Cari</button>
            </form>
        </div>

        {{-- Ambil data produk --}}
        @php
            $searchQuery = request('search');
            $productsList = \App\Models\Product::with('user')
                ->when($searchQuery, function ($q, $searchQuery) {
                    return $q->where('name', 'like', '%' . $searchQuery . '%');
                })
                ->latest()
                ->get();
        @endphp

        {{-- Grid Produk --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-5">
            @forelse($productsList as $product)
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col hover:shadow-lg transition">

                    {{-- Info Produk --}}
                    <div class="p-4 flex-1">
                        <div class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-1.5">{{ $product->user->store_name ?? 'Toko' }}</div>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-1.5 leading-tight">{{ $product->name }}</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-xs line-clamp-2 mb-3">{{ $product->description }}</p>
                        <div class="text-lg font-black text-slate-800 dark:text-white">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Sisa Stok: <span class="font-bold" id="max-stock-{{ $product->id }}">{{ $product->stock }}</span></div>
                    </div>

                    {{-- Aksi --}}
                    <div class="p-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                        @if($product->stock > 0)
                            {{-- Baris 1: Kuantitas + Keranjang --}}
                            <div class="flex items-center gap-2 mb-2">
                                {{-- Qty stepper — CSS grid for perfect centering --}}
                                <div class="grid border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 overflow-hidden" style="grid-template-columns: 2rem 2rem 2rem;">
                                    <button type="button" onclick="ubahQty({{ $product->id }}, -1)" class="h-9 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-600 font-bold transition text-sm">-</button>
                                    <div class="h-9 flex items-center justify-center">
                                        <input type="number" id="qty-{{ $product->id }}" min="1" max="{{ $product->stock }}" value="1" class="w-full h-full text-center bg-transparent text-slate-800 dark:text-white text-sm font-bold focus:outline-none p-0 border-0" readonly>
                                    </div>
                                    <button type="button" onclick="ubahQty({{ $product->id }}, 1, {{ $product->stock }})" class="h-9 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-600 font-bold transition text-sm">+</button>
                                </div>

                                {{-- Tambah ke Keranjang (calls API) --}}
                                <button type="button"
                                    onclick="tambahKeKeranjang({{ $product->id }}, {{ $product->stock }})"
                                    title="Tambah ke Keranjang"
                                    class="h-9 px-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl transition flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </button>
                            </div>

                            {{-- Baris 2: Beli Sekarang (form POST → buyProduct → checkout) --}}
                            <form action="{{ route('user.buy', $product->id) }}" method="POST" id="form-buy-{{ $product->id }}">
                                @csrf
                                <input type="hidden" name="quantity" id="buy-qty-{{ $product->id }}" value="1">
                                <button type="button" onclick="beliSekarang({{ $product->id }})" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-xl transition text-sm text-center shadow-sm whitespace-nowrap">Beli Sekarang</button>
                            </form>
                        @else
                            <button disabled class="w-full bg-slate-300 dark:bg-slate-700 text-slate-500 font-bold py-2 rounded-xl cursor-not-allowed text-sm">Stok Habis</button>
                        @endif
                    </div>

                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-slate-800 rounded-2xl p-10 text-center shadow-sm border border-slate-200 dark:border-slate-700">
                    <p class="text-slate-500 dark:text-slate-400 text-lg">
                        @if($searchQuery)
                            Yaaah, produk "{{ $searchQuery }}" tidak ditemukan.
                        @else
                            Belum ada produk yang dijual saat ini.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

    </main>

    {{-- ─── TOAST CONTAINER ────────────────────────────────────────────────────── --}}
    <div id="toast-container" class="fixed top-5 right-5 z-[9999] flex flex-col gap-3 pointer-events-none"></div>

    {{-- ─── MODAL: CART ─────────────────────────────────────────────────────────── --}}
    <div id="modal-cart" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeCart()"></div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-lg mx-4 shadow-2xl z-10 border border-slate-200 dark:border-slate-700 flex flex-col max-h-[85vh]">

            {{-- Header --}}
            <div class="flex items-center justify-between p-6 border-b border-slate-200 dark:border-slate-700 flex-shrink-0">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">Keranjang Belanja</h3>
                <button onclick="closeCart()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Loading --}}
            <div id="cart-loading" class="flex-1 flex items-center justify-center p-12 hidden">
                <div class="flex flex-col items-center gap-3">
                    <svg class="w-8 h-8 text-blue-500 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <span class="text-slate-400 text-sm">Memuat keranjang...</span>
                </div>
            </div>

            {{-- Error --}}
            <div id="cart-error" class="hidden flex-1 flex items-center justify-center p-12 text-center">
                <div>
                    <p class="text-red-500 font-medium mb-2">Gagal memuat keranjang.</p>
                    <button onclick="loadCart()" class="text-sm text-blue-600 hover:underline">Coba lagi</button>
                </div>
            </div>

            {{-- Items --}}
            <div id="cart-items-container" class="flex-1 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-700 hidden"></div>

            {{-- Empty --}}
            <div id="cart-empty-state" class="hidden flex-1 flex flex-col items-center justify-center p-12 text-center">
                <svg class="w-16 h-16 text-slate-300 dark:text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <p class="text-slate-500 dark:text-slate-400 font-medium mb-1">Keranjang Anda kosong</p>
                <p class="text-slate-400 dark:text-slate-500 text-sm">Tambah produk untuk mulai berbelanja</p>
            </div>

            {{-- Footer / Totals --}}
            <div id="cart-footer" class="hidden border-t border-slate-200 dark:border-slate-700 p-6 bg-slate-50 dark:bg-slate-800/50 rounded-b-2xl flex-shrink-0">
                <div class="flex justify-between text-sm text-slate-500 dark:text-slate-400 mb-1">
                    <span>Total Item</span>
                    <span id="cart-total-items">0</span>
                </div>
                <div class="flex justify-between text-lg font-black text-slate-800 dark:text-white border-t border-slate-200 dark:border-slate-600 pt-3 mt-2 mb-4">
                    <span>Total</span>
                    <span id="cart-total-price">Rp 0</span>
                </div>
                <a href="{{ route('user.checkout') }}" onclick="closeCart()" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition text-center shadow-sm">
                    Checkout
                </a>
            </div>

        </div>
    </div>

    {{-- ─── SCRIPT ──────────────────────────────────────────────────────────────── --}}
    <script>
        // ─── CSRF token for API requests (Sanctum session-based) ─────────────────
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        // ─── API helpers ──────────────────────────────────────────────────────────
        async function apiGet(url) {
            const r = await fetch(url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            return r.json();
        }

        async function apiPost(url, body) {
            const r = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
                body: JSON.stringify(body),
            });
            return r.json();
        }

        async function apiPatch(url, body) {
            const r = await fetch(url, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
                body: JSON.stringify(body),
            });
            return r.json();
        }

        async function apiDelete(url) {
            const r = await fetch(url, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            return r.json();
        }

        // ─── Theme ────────────────────────────────────────────────────────────────
        function toggleTheme() {
            const html = document.documentElement;
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                html.classList.add('dark');
                localStorage.theme = 'dark';
            }
        }

        // ─── Format Rupiah ────────────────────────────────────────────────────────
        function formatRupiah(n) {
            return 'Rp ' + parseInt(n).toLocaleString('id-ID');
        }

        // ─── Cart badge ───────────────────────────────────────────────────────────
        function updateBadge(totalItems) {
            const badge = document.getElementById('cart-badge');
            if (!badge) return;
            if (totalItems > 0) {
                badge.textContent = totalItems > 99 ? '99+' : totalItems;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }

        // ─── Cart state (in memory, refreshed from API) ───────────────────────────
        let cartState = { items: [], totalItems: 0, totalPrice: 0 };

        // ─── Qty stepper on product cards ─────────────────────────────────────────
        function ubahQty(productId, delta, maxStok = 999) {
            const input = document.getElementById('qty-' + productId);
            const newVal = parseInt(input.value) + delta;
            if (newVal >= 1 && newVal <= maxStok) input.value = newVal;
        }

        // ─── Add to cart (via API) ────────────────────────────────────────────────
        async function tambahKeKeranjang(productId, maxStock) {
            const qty = parseInt(document.getElementById('qty-' + productId).value);
            try {
                const res = await apiPost('/api/cart', { productId, quantity: qty });
                if (res.success) {
                    cartState = res.data;
                    updateBadge(cartState.totalItems);
                    tampilkanToast(qty + ' produk berhasil ditambahkan ke keranjang');
                } else {
                    tampilkanToast(res.message || 'Gagal menambahkan ke keranjang', 'error');
                }
            } catch {
                tampilkanToast('Terjadi kesalahan. Coba lagi.', 'error');
            }
        }

        // ─── Buy now ──────────────────────────────────────────────────────────────
        function beliSekarang(productId) {
            const qty = parseInt(document.getElementById('qty-' + productId).value);
            document.getElementById('buy-qty-' + productId).value = qty;
            document.getElementById('form-buy-' + productId).submit();
        }

        // ─── Open/close cart modal ────────────────────────────────────────────────
        function openCart() {
            const m = document.getElementById('modal-cart');
            m.classList.remove('hidden');
            m.classList.add('flex');
            loadCart();
        }

        function closeCart() {
            const m = document.getElementById('modal-cart');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }

        // ─── Load cart from API ───────────────────────────────────────────────────
        async function loadCart() {
            const loading   = document.getElementById('cart-loading');
            const error     = document.getElementById('cart-error');
            const container = document.getElementById('cart-items-container');
            const empty     = document.getElementById('cart-empty-state');
            const footer    = document.getElementById('cart-footer');

            // Show loading
            loading.classList.remove('hidden');
            error.classList.add('hidden');
            container.classList.add('hidden');
            empty.classList.add('hidden');
            footer.classList.add('hidden');

            try {
                const res = await apiGet('/api/cart');
                loading.classList.add('hidden');

                if (!res.success) throw new Error(res.message);

                cartState = res.data;
                updateBadge(cartState.totalItems);
                renderCartItems(cartState);
            } catch {
                loading.classList.add('hidden');
                error.classList.remove('hidden');
            }
        }

        // ─── Render cart items ────────────────────────────────────────────────────
        function renderCartItems(data) {
            const container = document.getElementById('cart-items-container');
            const empty     = document.getElementById('cart-empty-state');
            const footer    = document.getElementById('cart-footer');

            container.innerHTML = '';

            if (!data.items || data.items.length === 0) {
                container.classList.add('hidden');
                empty.classList.remove('hidden');
                footer.classList.add('hidden');
                return;
            }

            container.classList.remove('hidden');
            empty.classList.add('hidden');
            footer.classList.remove('hidden');

            data.items.forEach(item => {
                const row = document.createElement('div');
                row.className = 'flex items-center gap-3 p-4';
                row.innerHTML = `
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800 dark:text-white text-sm truncate">${item.productName}</p>
                        <p class="text-slate-500 dark:text-slate-400 text-xs">${formatRupiah(item.price)}</p>
                    </div>
                    <div class="grid flex-shrink-0 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 overflow-hidden" style="grid-template-columns:1.75rem 1.75rem 1.75rem;">
                        <button onclick="cartUpdateQty(${item.id}, ${item.quantity - 1})" class="h-8 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-600 font-bold text-sm transition">-</button>
                        <div class="h-8 flex items-center justify-center">
                            <span class="text-sm font-bold text-slate-800 dark:text-white">${item.quantity}</span>
                        </div>
                        <button onclick="cartUpdateQty(${item.id}, ${item.quantity + 1})" class="h-8 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-600 font-bold text-sm transition">+</button>
                    </div>
                    <div class="flex-shrink-0 w-24 text-right">
                        <p class="font-bold text-slate-800 dark:text-white text-sm">${formatRupiah(item.subtotal)}</p>
                    </div>
                    <button onclick="cartRemoveItem(${item.id})" class="flex-shrink-0 p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition" title="Hapus">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                `;
                container.appendChild(row);
            });

            document.getElementById('cart-total-items').textContent = data.totalItems + ' item';
            document.getElementById('cart-total-price').textContent = formatRupiah(data.totalPrice);
        }

        // ─── Cart: update quantity ────────────────────────────────────────────────
        async function cartUpdateQty(itemId, newQty) {
            if (newQty < 1) {
                return cartRemoveItem(itemId);
            }
            try {
                const res = await apiPatch('/api/cart/' + itemId, { quantity: newQty });
                if (res.success) {
                    cartState = res.data;
                    updateBadge(cartState.totalItems);
                    renderCartItems(cartState);
                } else {
                    tampilkanToast(res.message || 'Gagal memperbarui kuantitas', 'error');
                }
            } catch {
                tampilkanToast('Terjadi kesalahan.', 'error');
            }
        }

        // ─── Cart: remove item ────────────────────────────────────────────────────
        async function cartRemoveItem(itemId) {
            try {
                const res = await apiDelete('/api/cart/' + itemId);
                if (res.success) {
                    cartState = res.data;
                    updateBadge(cartState.totalItems);
                    renderCartItems(cartState);
                } else {
                    tampilkanToast(res.message || 'Gagal menghapus item', 'error');
                }
            } catch {
                tampilkanToast('Terjadi kesalahan.', 'error');
            }
        }

        // ─── Toast ────────────────────────────────────────────────────────────────
        function tampilkanToast(pesan, type = 'success') {
            const container = document.getElementById('toast-container');
            const isSuccess = type === 'success';
            const toast = document.createElement('div');
            toast.className = [
                'flex items-center gap-3 bg-white dark:bg-slate-800',
                isSuccess ? 'border border-emerald-200 dark:border-emerald-700' : 'border border-red-200 dark:border-red-700',
                'px-4 py-3 rounded-2xl shadow-lg pointer-events-auto',
                'transition-all duration-300 ease-out opacity-0 translate-x-8'
            ].join(' ');

            toast.innerHTML = `
                <div class="flex-shrink-0 w-8 h-8 ${isSuccess ? 'bg-emerald-100 dark:bg-emerald-900/40' : 'bg-red-100 dark:bg-red-900/40'} rounded-full flex items-center justify-center">
                    ${isSuccess
                        ? '<svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>'
                        : '<svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>'
                    }
                </div>
                <p class="text-sm font-medium ${isSuccess ? 'text-emerald-700 dark:text-emerald-400' : 'text-red-700 dark:text-red-400'}">${pesan}</p>
            `;

            container.appendChild(toast);
            requestAnimationFrame(() => requestAnimationFrame(() => toast.classList.remove('opacity-0', 'translate-x-8')));
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-x-8');
                setTimeout(() => toast.remove(), 300);
            }, 2500);
        }

        // ─── Init: load badge on page load ────────────────────────────────────────
        (async () => {
            try {
                const res = await apiGet('/api/cart');
                if (res.success) {
                    cartState = res.data;
                    updateBadge(cartState.totalItems);
                }
            } catch { /* silently ignore on page load */ }
        })();
    </script>
</body>
</html>
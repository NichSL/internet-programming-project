<!DOCTYPE html>
<html lang="id" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan — MarketHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' };
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
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

        <h2 class="text-3xl font-black text-slate-800 dark:text-white mb-2">Riwayat Pesanan</h2>
        <p class="text-slate-500 dark:text-slate-400 mb-8">Semua transaksi pembelian Anda.</p>

        @if($orders->isEmpty())
            {{-- Empty state --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-16 text-center shadow-sm border border-slate-200 dark:border-slate-700">
                <svg class="w-20 h-20 text-slate-300 dark:text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-xl font-bold text-slate-700 dark:text-slate-300 mb-2">Belum ada pesanan</p>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">Pembelian Anda yang berhasil akan muncul di sini.</p>
                <a href="{{ route('user.dashboard') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-8 rounded-xl transition shadow-sm">
                    Mulai Belanja
                </a>
            </div>
        @else
            <div class="space-y-5">
                @foreach($orders as $order)
                    @php
                        $orderNumber = 'ORD-' . str_pad($order->id, 5, '0', STR_PAD_LEFT);
                        $unitPrice   = $order->quantity > 0 ? intdiv($order->total_price, $order->quantity) : $order->total_price;
                    @endphp
                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">

                        {{-- Order header --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700 gap-2">
                            <div>
                                <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">{{ $orderNumber }}</span>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $order->created_at->translatedFormat('d F Y, H:i') }}</p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-bold px-3 py-1 rounded-full self-start sm:self-center">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>

                        {{-- Product line --}}
                        <div class="px-6 py-5">
                            <div class="flex items-start gap-4">
                                {{-- Icon placeholder for product --}}
                                <div class="flex-shrink-0 w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-slate-800 dark:text-white">{{ $order->product->name ?? '(Produk dihapus)' }}</p>
                                    <p class="text-xs text-blue-600 dark:text-blue-400 font-semibold mt-0.5">{{ $order->product->user->store_name ?? 'Toko' }}</p>
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2 text-sm text-slate-500 dark:text-slate-400">
                                        <span>Rp {{ number_format($unitPrice, 0, ',', '.') }} / item</span>
                                        <span class="text-slate-300 dark:text-slate-600">×</span>
                                        <span>{{ $order->quantity }} item</span>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 text-right">
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Subtotal</p>
                                    <p class="font-black text-slate-800 dark:text-white text-base">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Footer total --}}
                        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-700 flex justify-between items-center">
                            <span class="text-sm text-slate-500 dark:text-slate-400">Total Pesanan</span>
                            <span class="text-lg font-black text-slate-800 dark:text-white">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif

    </main>

</body>
</html>

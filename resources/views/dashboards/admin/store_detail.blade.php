<!DOCTYPE html>
<html lang="id" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Toko - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' };
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 transition-colors duration-300">
    
    <!-- NAVBAR -->
    <nav class="bg-slate-900 dark:bg-slate-950 shadow-md h-20 flex items-center px-4 sm:px-6">
        <div class="max-w-7xl mx-auto w-full flex justify-between items-center">
            <h1 class="font-black text-2xl text-white tracking-tight">Admin<span class="text-purple-400">Panel</span></h1>
            <a href="{{ route('admin.dashboard') }}" class="text-white bg-slate-700 hover:bg-slate-600 px-4 py-2 rounded-lg text-sm font-bold transition">
                &larr; Kembali
            </a>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto mt-10 px-4 sm:px-6 lg:px-8 pb-12">
        
        <!-- HEADER INFO TOKO -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border-l-4 border-emerald-500 mb-8">
            <h2 class="text-2xl font-black text-slate-800 dark:text-white">Detail Toko: {{ $seller->store_name }}</h2>
            <p class="text-slate-500 dark:text-slate-400 mt-1"><span class="font-bold">Pemilik:</span> {{ $seller->name }}</p>
            <p class="text-slate-500 dark:text-slate-400"><span class="font-bold">Alamat:</span> {{ $seller->store_address ?? 'Belum diatur' }}</p>
        </div>

        <!-- GRID DUA KOLOM: DAFTAR PRODUK & HISTORY PENJUALAN -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- KOLOM KIRI: DAFTAR PRODUK -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden self-start">
                <div class="p-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Daftar Produk Toko</h3>
                </div>
                <div class="p-4">
                    <ul class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        @forelse($seller->products as $product)
                            <li class="py-3 flex justify-between items-center">
                                <div>
                                    <p class="font-bold text-slate-800 dark:text-white">{{ $product->name }}</p>
                                    <p class="text-sm text-slate-500">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                </div>
                                <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">
                                    Stok: {{ $product->stock }}
                                </span>
                            </li>
                        @empty
                            <li class="py-4 text-center text-slate-500">Toko ini belum memiliki produk.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- KOLOM KANAN: HISTORY PENJUALAN -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden self-start">
                <div class="p-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">History Penjualan</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="bg-slate-100 dark:bg-slate-900/50 text-slate-500 uppercase">
                                <th class="px-4 py-3">Pembeli</th>
                                <th class="px-4 py-3">Barang (Qty)</th>
                                <th class="px-4 py-3">Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @forelse($seller->sales as $sale)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                                <td class="px-4 py-3 font-bold text-slate-800 dark:text-white">{{ $sale->buyer->name }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                    {{ $sale->product->name ?? 'Dihapus' }} <br>
                                    <span class="text-xs text-slate-400">x{{ $sale->quantity }}</span>
                                </td>
                                <td class="px-4 py-3 font-bold text-emerald-600 dark:text-emerald-400">+ Rp {{ number_format($sale->total_price, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-slate-500">
                                    Toko ini belum memiliki riwayat penjualan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </main>
</body>
</html>
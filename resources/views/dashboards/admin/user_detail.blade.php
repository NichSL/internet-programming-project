<!DOCTYPE html>
<html lang="id" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengguna - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' };
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 transition-colors duration-300">
    
    <!-- NAVBAR (Sama seperti dashboard admin) -->
    <nav class="bg-slate-900 dark:bg-slate-950 shadow-md h-20 flex items-center px-4 sm:px-6">
        <div class="max-w-7xl mx-auto w-full flex justify-between items-center">
            <h1 class="font-black text-2xl text-white tracking-tight">Admin<span class="text-purple-400">Panel</span></h1>
            <a href="{{ route('admin.dashboard') }}" class="text-white bg-slate-700 hover:bg-slate-600 px-4 py-2 rounded-lg text-sm font-bold transition">
                &larr; Kembali
            </a>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto mt-10 px-4 sm:px-6 lg:px-8 pb-12">
        
        <!-- HEADER INFO PENGGUNA -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border-l-4 border-blue-500 mb-8">
            <h2 class="text-2xl font-black text-slate-800 dark:text-white">Profil Pengguna</h2>
            <p class="text-slate-500 dark:text-slate-400 mt-1"><span class="font-bold">Nama:</span> {{ $user->name }}</p>
            <p class="text-slate-500 dark:text-slate-400"><span class="font-bold">Email:</span> {{ $user->email }}</p>
        </div>

        <!-- TABEL HISTORY BELANJA -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">History Belanja</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100 dark:bg-slate-900/50 text-slate-500 text-sm uppercase">
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Barang</th>
                            <th class="px-6 py-4">Qty</th>
                            <th class="px-6 py-4">Total Harga</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($user->purchases as $order)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $order->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 font-bold text-slate-800 dark:text-white">{{ $order->product->name ?? 'Produk Telah Dihapus' }}</td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $order->quantity }}</td>
                            <td class="px-6 py-4 font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                                Pengguna ini belum pernah melakukan transaksi.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</body>
</html>
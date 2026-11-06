<!DOCTYPE html>
<html lang="id" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Seller</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' };
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 transition-colors duration-300 relative">
    <nav class="bg-emerald-700 dark:bg-emerald-900 shadow-md">
        <div class="w-full px-4 sm:px-6 flex items-center h-20 relative">
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
            <div class="w-full flex items-center pl-[140px]">
                <h1 class="font-black text-2xl text-white tracking-tight">Seller<span class="text-emerald-300">Center</span></h1>
                <div class="flex items-center gap-5 ml-auto">
                    <button onclick="openModal('modal-keluar-toko')" class="text-emerald-50 hover:text-white font-medium bg-emerald-600 dark:bg-emerald-700 px-4 py-2 rounded-lg transition">Keluar dari Toko</button>
                    <form id="form-logout" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                    <a href="{{ route('settings', ['context' => 'seller']) }}" class="text-emerald-100 hover:text-white transition">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto mt-10 px-4 sm:px-6 lg:px-8 pb-20">
        @if(session('success'))
            <div class="bg-emerald-100 text-emerald-700 px-4 py-3 rounded-lg mb-6 shadow-sm">{{ session('success') }}</div>
        @endif

        <!-- Informasi Toko -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-sm border border-slate-200 dark:border-slate-700 flex items-center justify-between mb-8">
            <div>
                <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-1">Informasi Toko</p>
                <h2 class="text-3xl font-extrabold text-slate-800 dark:text-white mb-2">{{ Auth::user()->store_name }}</h2>
                <p class="text-slate-500 dark:text-slate-400 flex items-center gap-2">📍 {{ Auth::user()->store_address }}</p>
            </div>
            <div class="text-right">
                <p class="text-slate-500 dark:text-slate-400">Pemilik:</p>
                <p class="font-bold text-slate-800 dark:text-white">{{ Auth::user()->name }}</p>
            </div>
        </div>

        <!-- Tabel Manajemen Produk -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">Daftar Produk</h3>
                <a href="{{ route('products.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-5 rounded-lg transition shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Produk
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400 text-sm uppercase tracking-wider">
                            <th class="px-6 py-4 font-semibold">Nama Produk</th>
                            <th class="px-6 py-4 font-semibold">Harga</th>
                            <th class="px-6 py-4 font-semibold">Stok</th>
                            <th class="px-6 py-4 font-semibold text-right">Manajemen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($products as $product)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                                <td class="px-6 py-4 text-slate-800 dark:text-slate-200 font-medium">{{ $product->name }}</td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400">{{ $product->stock }} item</td>
                                <td class="px-6 py-4 text-right flex justify-end gap-3">
                                    <a href="{{ route('products.edit', $product->id) }}" class="text-blue-500 hover:text-blue-700 bg-blue-50 dark:bg-blue-900/30 px-3 py-1 rounded-md transition font-medium">Edit</a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 dark:bg-red-900/30 px-3 py-1 rounded-md transition font-medium">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">Belum ada produk. Silakan tambah produk pertama Anda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Keluar Toko -->
    <div id="modal-keluar-toko" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal('modal-keluar-toko')"></div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 max-w-sm w-full mx-4 shadow-2xl z-10 text-center border border-slate-200 dark:border-slate-700">
            <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Keluar dari Toko?</h3>
            <p class="text-slate-500 dark:text-slate-400 mb-6 text-sm">Anda akan kembali ke mode pengguna (User biasa).</p>
            <div class="flex gap-3">
                <button onclick="closeModal('modal-keluar-toko')" class="flex-1 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold py-3 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 transition">Batal</button>
                <a href="{{ route('user.dashboard') }}" class="flex-1 bg-amber-500 text-white font-bold py-3 rounded-xl hover:bg-amber-600 transition block">Ya, Keluar</a>
            </div>
        </div>
    </div>

    <!-- Modal Logout Akun -->
    <div id="modal-logout" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal('modal-logout')"></div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 max-w-sm w-full mx-4 shadow-2xl z-10 text-center border border-slate-200 dark:border-slate-700">
            <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Logout Akun?</h3>
            <p class="text-slate-500 dark:text-slate-400 mb-6 text-sm">Anda akan keluar dari aplikasi sepenuhnya.</p>
            <div class="flex gap-3">
                <button onclick="closeModal('modal-logout')" class="flex-1 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold py-3 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 transition">Batal</button>
                <button onclick="document.getElementById('form-logout').submit()" class="flex-1 bg-red-600 text-white font-bold py-3 rounded-xl hover:bg-red-700 transition">Ya, Logout</button>
            </div>
        </div>
    </div>

    <script>
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
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="id" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' };
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 transition-colors duration-300 relative">
    <nav class="bg-slate-900 dark:bg-slate-950 shadow-md">
        <div class="w-full px-4 sm:px-6 flex items-center h-20 relative">
            
            <!-- POJOK KIRI: Toggle Light/Dark Mode -->
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

            <!-- CONTAINER UTAMA: Logo dan Menu Kanan -->
            <div class="max-w-7xl mx-auto w-full flex justify-between items-center pl-[140px] xl:pl-0">
                
                <!-- KIRI CONTAINER: Logo -->
                <h1 class="font-black text-2xl text-white tracking-tight">Admin<span class="text-purple-400">Panel</span></h1>
                
                <!-- KANAN CONTAINER: Logout -> Setting(Paling Kanan) -->
                <div class="flex items-center gap-5">
                    
                    <!-- Tombol Pemicu Modal Logout -->
                    <button onclick="openModal('modal-logout')" class="text-slate-300 dark:text-slate-400 hover:text-white font-medium transition">Logout</button>
                    
                    <!-- Form Logout Asli (Disembunyikan) -->
                    <form id="form-logout" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>

                    <!-- Setting (Gear - Paling Kanan) -->
                    <a href="{{ route('settings', ['context' => 'admin']) }}" class="text-slate-400 hover:text-purple-400 transition ml-2">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </a>
                </div>

            </div>
        </div>
    </nav>
    <main class="max-w-7xl mx-auto mt-10 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border-l-4 border-purple-500 dark:border-purple-600">
                <p class="text-slate-500 dark:text-slate-400 text-sm font-bold">Total Pengguna</p>
                <p class="text-3xl font-black text-slate-800 dark:text-white mt-2">1,204</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border-l-4 border-emerald-500 dark:border-emerald-600">
                <p class="text-slate-500 dark:text-slate-400 text-sm font-bold">Total Toko</p>
                <p class="text-3xl font-black text-slate-800 dark:text-white mt-2">342</p>
            </div>
        </div>
    </main>

    <!-- Modal: Logout Akun -->
    <div id="modal-logout" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal('modal-logout')"></div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 max-w-sm w-full mx-4 shadow-2xl z-10 text-center border border-slate-200 dark:border-slate-700 transform transition-all">
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
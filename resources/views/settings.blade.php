<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun</title>
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
                <h1 class="font-bold text-xl text-slate-800 dark:text-white">Pengaturan Akun</h1>
                
                @php
                    // Logika tombol kembali menggunakan Context
                    $dashboardRoute = route('user.dashboard');
                    if($context === 'seller') $dashboardRoute = route('seller.dashboard');
                    if($context === 'admin') $dashboardRoute = route('admin.dashboard');
                @endphp
                <a href="{{ $dashboardRoute }}" class="text-indigo-600 dark:text-indigo-400 font-medium hover:underline">
                    &larr; Kembali ke Dashboard
                </a>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto mt-10 px-4 sm:px-6 lg:px-8 pb-20 space-y-8">

        <!-- 1. Form Profil -->
        <div class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-6">Informasi Dasar</h2>
            @if(session('success_profile'))
                <div class="bg-green-100 text-green-700 p-4 rounded mb-6">{{ session('success_profile') }}</div>
            @endif
            @if($errors->has('username'))
                <div class="bg-red-100 text-red-700 p-4 rounded mb-6">{{ $errors->first('username') }}</div>
            @endif
            @if($errors->has('email'))
                <div class="bg-red-100 text-red-700 p-4 rounded mb-6">{{ $errors->first('email') }}</div>
            @endif
            <form action="{{ route('settings.profile') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-slate-600 dark:text-slate-300 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ Auth::user()->name }}" class="w-full bg-transparent border border-slate-300 dark:border-slate-600 dark:text-white px-4 py-3 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-slate-600 dark:text-slate-300 mb-2">Username</label>
                        <input type="text" name="username" value="{{ Auth::user()->username }}" class="w-full bg-transparent border border-slate-300 dark:border-slate-600 dark:text-white px-4 py-3 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                </div>
                <div class="mb-6">
                    <label class="block text-slate-600 dark:text-slate-300 mb-2">Email</label>
                    <input type="email" name="email" value="{{ Auth::user()->email }}" class="w-full bg-transparent border border-slate-300 dark:border-slate-600 dark:text-white px-4 py-3 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                </div>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition">Simpan Profil</button>
            </form>
        </div>

        <!-- 2. Form Ganti Password -->
        <div class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-6">Keamanan (Ganti Password)</h2>
            @if(session('success_password'))
                <div class="bg-green-100 text-green-700 p-4 rounded mb-6">{{ session('success_password') }}</div>
            @endif
            @if($errors->has('current_password'))
                <div class="bg-red-100 text-red-700 p-4 rounded mb-6">{{ $errors->first('current_password') }}</div>
            @endif
            <form action="{{ route('settings.password') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-slate-600 dark:text-slate-300 mb-2">Password Saat Ini</label>
                        <input type="password" name="current_password" class="w-full bg-transparent border border-slate-300 dark:border-slate-600 dark:text-white px-4 py-3 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-slate-600 dark:text-slate-300 mb-2">Password Baru</label>
                        <input type="password" name="new_password" class="w-full bg-transparent border border-slate-300 dark:border-slate-600 dark:text-white px-4 py-3 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-slate-600 dark:text-slate-300 mb-2">Konfirmasi Password Baru</label>
                        <input type="password" name="new_password_confirmation" class="w-full bg-transparent border border-slate-300 dark:border-slate-600 dark:text-white px-4 py-3 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                </div>
                <button type="submit" class="mt-6 bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded-lg transition">Ganti Password</button>
            </form>
        </div>

        <!-- 3. Form Toko (Hanya Muncul Jika Role = Seller DAN Context = Seller) -->
        @if(Auth::user()->role === 'seller' && $context === 'seller')
        <div class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-6">Pengaturan Toko</h2>
            @if(session('success_store'))
                <div class="bg-green-100 text-green-700 p-4 rounded mb-6">{{ session('success_store') }}</div>
            @endif
            <form action="{{ route('settings.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-slate-600 dark:text-slate-300 mb-2">Nama Toko</label>
                        <input type="text" name="store_name" value="{{ Auth::user()->store_name }}" class="w-full bg-transparent border border-slate-300 dark:border-slate-600 dark:text-white px-4 py-3 rounded-lg focus:ring-2 focus:ring-amber-500" required>
                    </div>
                    <div>
                        <label class="block text-slate-600 dark:text-slate-300 mb-2">Alamat Toko</label>
                        <textarea name="store_address" rows="3" class="w-full bg-transparent border border-slate-300 dark:border-slate-600 dark:text-white px-4 py-3 rounded-lg focus:ring-2 focus:ring-amber-500" required>{{ Auth::user()->store_address }}</textarea>
                    </div>
                </div>
                <button type="submit" class="mt-6 bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 px-6 rounded-lg transition">Simpan Data Toko</button>
            </form>
        </div>
        @endif

        <!-- Zona Bahaya: Logout -->
        <div class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-sm border border-red-200 dark:border-red-900/50">
            <h2 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Keluar dari Akun</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">Anda akan keluar dari sesi ini sepenuhnya. Pastikan Anda sudah menyimpan semua perubahan.</p>
            <button onclick="openLogoutModal()" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-xl transition shadow-sm flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Logout Akun
            </button>
        </div>

    </main>

    <!-- Form Logout -->
    <form id="form-logout-settings" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <!-- Modal Konfirmasi Logout -->
    <div id="modal-logout-settings" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeLogoutModal()"></div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 max-w-sm w-full mx-4 shadow-2xl z-10 text-center border border-slate-200 dark:border-slate-700">
            <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Logout Akun?</h3>
            <p class="text-slate-500 dark:text-slate-400 mb-6 text-sm">Anda akan keluar dari aplikasi sepenuhnya.</p>
            <div class="flex gap-3">
                <button onclick="closeLogoutModal()" class="flex-1 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold py-3 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 transition">Batal</button>
                <button onclick="document.getElementById('form-logout-settings').submit()" class="flex-1 bg-red-600 text-white font-bold py-3 rounded-xl hover:bg-red-700 transition">Ya, Logout</button>
            </div>
        </div>
    </div>

    <script>
        function openLogoutModal() {
            const m = document.getElementById('modal-logout-settings');
            m.classList.remove('hidden');
            m.classList.add('flex');
        }
        function closeLogoutModal() {
            const m = document.getElementById('modal-logout-settings');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }
    </script></body>
</html>
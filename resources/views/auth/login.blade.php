<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login User</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen relative overflow-hidden">
    
    <!-- EFEK AURA WARNA (BACKGROUND) -->
    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-purple-400 rounded-full mix-blend-multiply filter blur-[100px] opacity-70"></div>
    <div class="absolute top-[20%] right-[-5%] w-96 h-96 bg-indigo-400 rounded-full mix-blend-multiply filter blur-[120px] opacity-60"></div>
    <div class="absolute bottom-[-10%] left-[20%] w-96 h-96 bg-pink-400 rounded-full mix-blend-multiply filter blur-[100px] opacity-60"></div>

    <!-- FORM LOGIN (Efek Kaca / Glassmorphism) -->
    <div class="z-10 bg-white/80 backdrop-blur-xl p-8 rounded-3xl shadow-2xl w-full max-w-md border border-white/50">
        <h2 class="text-3xl font-extrabold text-center text-slate-800 mb-8">Selamat Datang</h2>
        
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg text-sm shadow-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="/login" method="POST">
            @csrf
            <div class="mb-5">
                <label class="block text-slate-700 font-bold mb-2 text-sm">Username</label>
                <input type="text" name="username" value="{{ old('username') }}" class="w-full bg-white/90 border border-slate-200 px-4 py-3 rounded-xl focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition shadow-sm" required>
            </div>
            
            <div class="mb-8 relative">
                <label class="block text-slate-700 font-bold mb-2 text-sm">Password</label>
                <input type="password" name="password" id="password" class="w-full bg-white/90 border border-slate-200 px-4 py-3 pr-10 rounded-xl focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition shadow-sm" required>
                <button type="button" onclick="togglePassword('password', 'icon-login')" class="absolute right-3 top-10 text-slate-400 hover:text-indigo-600 focus:outline-none transition">
                    <svg id="icon-login-closed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 block">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                    <svg id="icon-login-open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 hidden">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white font-black py-4 px-4 rounded-xl hover:bg-indigo-700 transition duration-300 shadow-lg shadow-indigo-200">Masuk</button>
        </form>
        <p class="mt-6 text-center text-slate-600">Belum punya akun? <a href="{{ route('register') }}" class="text-indigo-600 font-bold hover:underline">Daftar sekarang</a></p>
    </div>

    <script>
        function togglePassword(fieldId, iconPrefix) {
            const field = document.getElementById(fieldId);
            const iconClosed = document.getElementById(iconPrefix + '-closed');
            const iconOpen = document.getElementById(iconPrefix + '-open');

            if (field.type === "password") {
                field.type = "text";
                iconClosed.classList.replace('block', 'hidden');
                iconOpen.classList.replace('hidden', 'block');
            } else {
                field.type = "password";
                iconOpen.classList.replace('block', 'hidden');
                iconClosed.classList.replace('hidden', 'block');
            }
        }
    </script>
</body>
</html>
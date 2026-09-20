<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 flex items-center justify-center min-h-screen">
    <div class="bg-slate-800 p-8 rounded-2xl shadow-2xl w-full max-w-md border border-slate-700">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-white">Admin<span class="text-purple-500">Portal</span></h2>
            <p class="text-slate-400 text-sm mt-1">Area terlarang selain administrator.</p>
        </div>
        
        @if ($errors->any())
            <div class="bg-red-900 border-l-4 border-red-500 text-red-200 p-4 mb-6 rounded text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="/admin/login" method="POST">
            @csrf
            <div class="mb-5">
                <label class="block text-slate-300 font-medium mb-2">Admin Username</label>
                <input type="text" name="username" placeholder="Contoh: admin_joko" value="{{ old('username') }}" class="w-full bg-slate-700 border border-slate-600 text-white px-4 py-3 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none" required>
            </div>
            
            <div class="mb-8 relative">
                <label class="block text-slate-300 font-medium mb-2">Password</label>
                <input type="password" name="password" id="password" class="w-full bg-slate-700 border border-slate-600 text-white px-4 py-3 pr-10 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none" required>
                <button type="button" onclick="togglePassword('password', 'icon-admin')" class="absolute right-3 top-10 text-slate-400 hover:text-slate-200 focus:outline-none">
                    <svg id="icon-admin-closed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 block">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                    <svg id="icon-admin-open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 hidden">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
            </div>

            <button type="submit" class="w-full bg-purple-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-purple-700 transition shadow-lg">Login Admin</button>
        </form>
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
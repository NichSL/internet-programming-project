<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buka Toko</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen">
    <div class="bg-white p-10 rounded-3xl shadow-2xl w-full max-w-lg border border-slate-100">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-slate-800">Mulai Jualan 🚀</h2>
            <p class="text-slate-500 mt-2">Lengkapi data di bawah untuk membuka toko Anda.</p>
        </div>
        <form action="/become-seller" method="POST">
            @csrf
            <div class="mb-5">
                <label class="block text-slate-700 font-bold mb-2">Nama Toko</label>
                <input type="text" name="store_name" placeholder="Misal: Toko Berkah" class="w-full border-2 border-slate-200 px-4 py-3 rounded-xl focus:border-amber-500 focus:outline-none transition" value="{{ old('store_name') }}">
                @error('store_name')
                    eh ini error store name
                @enderror
            </div>
            <div class="mb-8">
                <label class="block text-slate-700 font-bold mb-2">Alamat Toko</label>
                <textarea name="store_address" rows="3" placeholder="Masukkan alamat lengkap toko" class="w-full border-2 border-slate-200 px-4 py-3 rounded-xl focus:border-amber-500 focus:outline-none transition"></textarea>
                @error('store_address')
                    eh ini error store address
                @enderror
            </div>
            <button type="submit" class="w-full bg-amber-500 text-white font-black py-4 px-4 rounded-xl hover:bg-amber-600 transition duration-300 shadow-lg shadow-amber-200 text-lg">Buka Toko Sekarang</button>
            <a href="{{ route('user.dashboard') }}" class="block text-center mt-4 text-slate-500 font-medium hover:text-slate-800">Batal, kembali ke dashboard</a>
        </form>
    </div>
</body>
</html>
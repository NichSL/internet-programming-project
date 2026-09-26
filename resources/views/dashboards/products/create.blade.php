<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk Baru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' };
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 transition-colors duration-300 py-10">
    <div class="max-w-2xl mx-auto px-4">
        <a href="{{ route('seller.dashboard') }}" class="text-emerald-600 dark:text-emerald-400 hover:underline mb-6 inline-block font-medium">&larr; Kembali ke Dashboard</a>
        <div class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-6">Tambah Produk Baru</h2>
            
            <form action="{{ route('products.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-slate-700 dark:text-slate-300 font-bold mb-2">Nama Produk</label>
                    <input type="text" name="name" class="w-full bg-transparent border border-slate-300 dark:border-slate-600 dark:text-white px-4 py-3 rounded-lg focus:ring-2 focus:ring-emerald-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-slate-700 dark:text-slate-300 font-bold mb-2">Deskripsi Produk (Opsional)</label>
                    <textarea name="description" rows="4" class="w-full bg-transparent border border-slate-300 dark:border-slate-600 dark:text-white px-4 py-3 rounded-lg focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 font-bold mb-2">Harga (Rp)</label>
                        <input type="number" name="price" min="0" class="w-full bg-transparent border border-slate-300 dark:border-slate-600 dark:text-white px-4 py-3 rounded-lg focus:ring-2 focus:ring-emerald-500" required>
                    </div>
                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 font-bold mb-2">Stok (Item)</label>
                        <input type="number" name="stock" min="0" class="w-full bg-transparent border border-slate-300 dark:border-slate-600 dark:text-white px-4 py-3 rounded-lg focus:ring-2 focus:ring-emerald-500" required>
                    </div>
                </div>
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-lg transition shadow-md">Simpan Produk</button>
            </form>
        </div>
    </div>
</body>
</html>
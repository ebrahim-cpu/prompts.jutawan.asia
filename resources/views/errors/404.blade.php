<!DOCTYPE html>
<html lang="ms" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>404 — Halaman Tidak Ditemui | PromptLib</title>
        <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎨</text></svg>">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            body { font-family: 'Inter', sans-serif; background-color: #0f172a; color: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
            .text-gradient { background: linear-gradient(to right, #a855f7, #ec4899); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        </style>
    </head>
    <body class="antialiased">
        <!-- Background orbs -->
        <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-purple-600 rounded-full mix-blend-screen filter blur-3xl opacity-20"></div>
            <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-pink-600 rounded-full mix-blend-screen filter blur-3xl opacity-20"></div>
        </div>

        <div class="relative z-10 text-center px-6">
            <div class="text-8xl md:text-9xl font-extrabold text-gradient mb-4">404</div>
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-3">Oops! Halaman Tidak Ditemui</h1>
            <p class="text-gray-400 text-sm md:text-base max-w-md mx-auto mb-8">
                Halaman yang anda cari mungkin telah dipadamkan, ditukar namanya, atau tidak wujud.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="/" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold text-sm rounded-xl hover:opacity-90 transition shadow-lg shadow-purple-500/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Kembali ke Laman Utama
                </a>
                <a href="/pricing" class="inline-flex items-center gap-2 px-6 py-3 bg-white/10 text-white font-bold text-sm rounded-xl hover:bg-white/20 transition border border-white/10">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    Lihat Pelan Premium
                </a>
            </div>
            <p class="text-xs text-gray-600 mt-12">&copy; {{ date('Y') }} PromptLib. made by rafiridzuan (intern khtp)</p>
        </div>
    </body>
</html>

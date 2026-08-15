<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Koleksi Prompt AI - Jutawan.asia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; color: #000 !important; }
            .print-table th { background-color: #f3f4f6 !important; color: #111827 !important; }
            .print-card { border: 1px solid #e5e7eb !important; background: #fff !important; color: #111827 !important; }
        }
    </style>
</head>
<body class="bg-gray-950 text-gray-100 font-sans p-6 min-h-screen">

    <!-- Action Toolbar (Hidden in Print) -->
    <div class="no-print max-w-7xl mx-auto mb-6 flex items-center justify-between bg-gray-900 border border-white/10 p-4 rounded-2xl shadow-xl">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-400 font-bold">
                📄
            </div>
            <div>
                <h1 class="text-sm font-bold text-white">Laporan Export Prompt AI (PDF/Print)</h1>
                <p class="text-xs text-gray-400">Skop: <span class="font-bold text-pink-400 uppercase">{{ $scope === 'filtered' ? 'Rekod Ditapis (Filtered)' : 'Semua Rekod (All Prompts)' }}</span> | {{ count($prompts) }} Rekod</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 hover:opacity-90 text-white font-bold text-xs rounded-xl shadow-lg shadow-purple-500/20 transition cursor-pointer flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak / Simpan PDF</span>
            </button>
            <button onclick="window.close()" class="px-3.5 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 text-xs font-bold rounded-xl transition cursor-pointer">
                Tutup Window
            </button>
        </div>
    </div>

    <!-- Printable Report Container -->
    <div class="max-w-7xl mx-auto bg-gray-900 border border-white/10 print-card rounded-2xl p-8 shadow-2xl space-y-6">
        
        <!-- Document Header -->
        <div class="flex items-start justify-between border-b border-gray-800 pb-6 flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight">Koleksi Prompt AI</h1>
                <p class="text-xs text-gray-400 mt-1">Laporan Rasmi Eksport Sistem Prompts.Jutawan.Asia</p>
            </div>
            <div class="text-right text-xs text-gray-400 space-y-0.5">
                <p><span class="font-bold text-gray-300">Tarikh Dijana:</span> {{ date('d/m/Y H:i:s') }}</p>
                <p><span class="font-bold text-gray-300">Jumlah Prompt:</span> {{ count($prompts) }} Rekod</p>
                <p><span class="font-bold text-gray-300">Skop Export:</span> {{ strtoupper($scope) }}</p>
            </div>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left border-collapse print-table">
                <thead>
                    <tr class="bg-gray-800 text-gray-300 uppercase font-bold border-b border-gray-700">
                        <th class="p-3 w-10 text-center">#</th>
                        <th class="p-3">Tajuk Prompt</th>
                        <th class="p-3 w-28">Kategori</th>
                        <th class="p-3 w-20 text-center">Akses</th>
                        <th class="p-3 w-20 text-center">Rating</th>
                        <th class="p-3 w-36">Tag</th>
                        <th class="p-3">Teks Prompt Sebenar</th>
                        <th class="p-3 w-28 text-right">Dikemaskini</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 text-gray-300">
                    @forelse($prompts as $index => $p)
                        @php $catInfo = $p->getCategoryInfo(); @endphp
                        <tr class="hover:bg-gray-800/40 transition">
                            <td class="p-3 text-center font-mono text-gray-400">{{ $index + 1 }}</td>
                            <td class="p-3 font-bold text-white">{{ $p->title }}</td>
                            <td class="p-3">{{ $catInfo['icon'] ?? '' }} {{ $catInfo['label'] ?? $p->category }}</td>
                            <td class="p-3 text-center font-bold">
                                @if($p->is_premium)
                                    <span class="text-yellow-400">⭐ Premium</span>
                                @else
                                    <span class="text-green-400">Free</span>
                                @endif
                            </td>
                            <td class="p-3 text-center text-yellow-400">@for($i = 0; $i < ($p->rating ?? 3); $i++)★@endfor</td>
                            <td class="p-3 text-pink-300 font-mono">{{ implode(', ', $p->getTagsArray()) }}</td>
                            <td class="p-3 font-mono text-[11px] leading-snug whitespace-pre-wrap max-w-xs">{{ $p->prompt_text }}</td>
                            <td class="p-3 text-right font-mono text-gray-400">{{ $p->updated_at ? $p->updated_at->format('d/m/Y') : '' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-gray-500">Tiada prompt ditemui dalam skop ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="border-t border-gray-800 pt-4 text-center text-[10px] text-gray-500">
            Hak Cipta Terpelihara &copy; {{ date('Y') }} Jutawan.asia - Laporan Eksport Auto-Generated.
        </div>
    </div>

    <script>
        // Auto trigger browser print / PDF download modal when loaded
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 600);
        });
    </script>
</body>
</html>

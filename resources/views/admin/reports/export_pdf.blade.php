<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $filename }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; background-color: #ffffff !important; color: #000000 !important; }
            .no-print { display: none !important; }
            .print-border { border: 1px solid #e2e8f0 !important; }
        }
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen p-6 sm:p-10">

    <!-- Top Action Bar for PDF View -->
    <div class="max-w-4xl mx-auto mb-6 flex justify-between items-center no-print">
        <button onclick="window.history.back()" class="px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white text-xs font-bold rounded-lg transition">
            ← Kembali
        </button>
        <button onclick="window.print()" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-bold rounded-xl shadow-lg transition flex items-center gap-2">
            🖨️ Cetak / Simpan sebagai PDF
        </button>
    </div>

    <!-- PDF Document Card Container -->
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl border border-gray-200 p-8 sm:p-12 print-border">
        <!-- Document Header -->
        <div class="flex justify-between items-start border-b border-gray-200 pb-6 mb-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-2xl">🎨</span>
                    <span class="text-xl font-bold tracking-tight text-gray-900">Prompt<span class="text-purple-600">Lib</span></span>
                </div>
                <h1 class="text-2xl font-extrabold text-gray-900">{{ $reportTitle }}</h1>
                <p class="text-xs text-gray-500 mt-1">Laporan Interactive Analitik Pentadbir</p>
            </div>
            <div class="text-right">
                <div class="inline-block px-3 py-1 bg-purple-100 text-purple-700 text-xs font-extrabold rounded-lg uppercase tracking-wider">
                    Tahun {{ $year }}
                </div>
                <p class="text-xs text-gray-500 mt-2">Kekerapan: <strong class="text-gray-800 capitalize">{{ $period }}</strong></p>
                <p class="text-[11px] text-gray-400 mt-0.5">Tarikh Dijana: {{ date('d/m/Y h:i A') }}</p>
            </div>
        </div>

        <!-- Summary KPIs -->
        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="bg-purple-50 border border-purple-100 p-4 rounded-xl text-center">
                <span class="text-xs text-purple-600 font-bold uppercase tracking-wider">Jumlah Rekod</span>
                <div class="text-2xl font-black text-purple-900 mt-1">{{ number_format($reportData['total']) }}</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 p-4 rounded-xl text-center">
                <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">Tempoh Kemuncak</span>
                <div class="text-lg font-bold text-gray-900 mt-1">
                    {{ $reportData['peakLabel'] }}
                    <span class="text-xs font-semibold text-purple-600">({{ $reportData['peakValue'] }})</span>
                </div>
            </div>
            <div class="bg-gray-50 border border-gray-200 p-4 rounded-xl text-center">
                <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">Purata Rekod</span>
                <div class="text-2xl font-black text-gray-900 mt-1">{{ $reportData['avgValue'] }}</div>
            </div>
        </div>

        <!-- Breakdown Table -->
        <div class="mb-8">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-3">Ringkasan Data Mengikut Tempoh</h3>
            <div class="overflow-x-auto border border-gray-200 rounded-xl">
                <table class="w-full text-left text-xs divide-y divide-gray-200">
                    <thead class="bg-gray-50 text-gray-700 font-bold uppercase">
                        <tr>
                            <th class="px-4 py-3">Tempoh / Nisbah</th>
                            <th class="px-4 py-3 text-right">Jumlah Rekod</th>
                            <th class="px-4 py-3 text-right">Peratusan (%)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($reportData['labels'] as $idx => $label)
                            @php
                                $val = $reportData['values'][$idx] ?? 0;
                                $pct = $reportData['total'] > 0 ? round(($val / $reportData['total']) * 100, 1) : 0;
                            @endphp
                            <tr class="{{ $idx % 2 === 0 ? 'bg-white' : 'bg-gray-50/50' }}">
                                <td class="px-4 py-2.5 font-medium text-gray-900">{{ $label }}</td>
                                <td class="px-4 py-2.5 text-right font-bold text-purple-700">{{ number_format($val) }}</td>
                                <td class="px-4 py-2.5 text-right text-gray-600">{{ $pct }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100 font-bold border-t border-gray-300">
                        <tr>
                            <td class="px-4 py-3 text-gray-900">JUMLAH KESELURUHAN</td>
                            <td class="px-4 py-3 text-right text-purple-900">{{ number_format($reportData['total']) }}</td>
                            <td class="px-4 py-3 text-right text-gray-900">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Document Footer -->
        <div class="border-t border-gray-200 pt-4 flex justify-between items-center text-[11px] text-gray-400">
            <p>PromptLib System &copy; {{ date('Y') }} — Hak Cipta Terpelihara</p>
            <p>Dokumen ini dijana secara automatik oleh Panel Admin.</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            // Auto open print dialog if directly opening export PDF link
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>

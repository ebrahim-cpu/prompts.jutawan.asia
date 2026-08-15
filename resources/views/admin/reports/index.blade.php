<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                    <span class="p-2 rounded-xl bg-purple-500/20 text-purple-400 border border-purple-500/30">📊</span>
                    Laporan Interactive Panel Admin
                </h2>
                <p class="text-sm text-gray-400 mt-1">
                    Analisis data pelawat, log masuk berjaya, dan prompt baharu yang ditambah mengikut Harian, Mingguan, atau Bulanan.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 text-sm font-medium rounded-xl border border-white/10 transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Include Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- 1. VISITORS REPORT CARD -->
            <div class="bg-gray-800/60 backdrop-blur-md rounded-2xl border border-white/10 overflow-hidden shadow-2xl"
                 x-data="reportComponent('visitors', {{ $visitorYear }}, '{{ $visitorPeriod }}', @js($visitorData))">
                <div class="p-6 border-b border-white/10 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-gradient-to-r from-purple-900/20 to-transparent">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-400 text-xl font-bold">
                            👁️
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">1. Laporan Pelawat (Visitors Report)</h3>
                            <p class="text-xs text-gray-400">Statistik jumlah kunjungan pelawat ke platform.</p>
                        </div>
                    </div>

                    <!-- Filter & Action Bar -->
                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Year Selector -->
                        <div class="flex items-center gap-2 bg-gray-900/80 px-3 py-1.5 rounded-xl border border-white/10">
                            <span class="text-xs font-semibold text-gray-400">Tahun:</span>
                            <select x-model="year" @change="fetchData()" class="bg-transparent text-xs font-bold text-purple-400 border-none focus:ring-0 cursor-pointer py-1 pr-6 pl-1">
                                @foreach($years as $y)
                                    <option value="{{ $y }}" class="bg-gray-900 text-white">{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Period Options (Daily, Weeks, Month) -->
                        <div class="flex items-center bg-gray-900/80 p-1 rounded-xl border border-white/10">
                            <button @click="setPeriod('daily')" :class="period === 'daily' ? 'bg-purple-600 text-white shadow-md' : 'text-gray-400 hover:text-white'" class="px-3 py-1 text-xs font-semibold rounded-lg transition">
                                Harian
                            </button>
                            <button @click="setPeriod('weeks')" :class="period === 'weeks' ? 'bg-purple-600 text-white shadow-md' : 'text-gray-400 hover:text-white'" class="px-3 py-1 text-xs font-semibold rounded-lg transition">
                                Mingguan
                            </button>
                            <button @click="setPeriod('month')" :class="period === 'month' ? 'bg-purple-600 text-white shadow-md' : 'text-gray-400 hover:text-white'" class="px-3 py-1 text-xs font-semibold rounded-lg transition">
                                Bulanan
                            </button>
                        </div>

                        <!-- Export Buttons -->
                        <div class="flex items-center gap-2">
                            <a :href="getExportUrl('excel')" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/30 text-xs font-bold rounded-xl transition shadow-sm" title="Eksport ke Excel / CSV">
                                📗 Excel
                            </a>
                            <a :href="getExportUrl('pdf')" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/30 text-xs font-bold rounded-xl transition shadow-sm" title="Eksport ke PDF">
                                📕 PDF
                            </a>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Summary Metric Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="bg-gray-900/50 p-4 rounded-xl border border-white/5">
                            <span class="text-xs text-gray-400 uppercase font-semibold">Jumlah Pelawat</span>
                            <div class="text-2xl font-extrabold text-white mt-1" x-text="reportData.total"></div>
                        </div>
                        <div class="bg-gray-900/50 p-4 rounded-xl border border-white/5">
                            <span class="text-xs text-gray-400 uppercase font-semibold">Tempoh Kemuncak</span>
                            <div class="text-xl font-bold text-purple-400 mt-1 flex items-center gap-2">
                                <span x-text="reportData.peakLabel"></span>
                                <span class="text-xs font-semibold bg-purple-500/20 text-purple-300 px-2 py-0.5 rounded-full" x-text="reportData.peakValue + ' rekod'"></span>
                            </div>
                        </div>
                        <div class="bg-gray-900/50 p-4 rounded-xl border border-white/5">
                            <span class="text-xs text-gray-400 uppercase font-semibold">Purata per Tempoh</span>
                            <div class="text-2xl font-extrabold text-white mt-1" x-text="reportData.avgValue"></div>
                        </div>
                    </div>

                    <!-- Line Chart Container -->
                    <div class="bg-gray-900/60 p-4 rounded-2xl border border-white/5 relative">
                        <div x-show="loading" class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm rounded-2xl z-10 flex items-center justify-center">
                            <div class="flex items-center gap-2 text-purple-400 text-sm font-semibold animate-pulse">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Memuatkan data...
                            </div>
                        </div>
                        <div class="h-72 w-full">
                            <canvas x-ref="chartCanvas"></canvas>
                        </div>
                    </div>

                    <!-- Table Breakdown -->
                    <div x-data="{ showTable: false }" class="border-t border-white/5 pt-4">
                        <button @click="showTable = !showTable" class="text-xs font-bold text-purple-400 hover:text-purple-300 flex items-center gap-1.5">
                            <span x-text="showTable ? '▲ Sembunyikan Jadual Data' : '▼ Papar Jadual Data Terperinci'"></span>
                        </button>
                        <div x-show="showTable" x-transition class="mt-4 overflow-x-auto">
                            <table class="w-full text-left text-xs text-gray-300 divide-y divide-white/5">
                                <thead class="bg-gray-900/80 text-gray-400 uppercase font-bold">
                                    <tr>
                                        <th class="px-4 py-3">Tempoh / Nisbah</th>
                                        <th class="px-4 py-3 text-right">Jumlah Pelawat</th>
                                        <th class="px-4 py-3 text-right">Peratusan (%)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    <template x-for="(label, idx) in reportData.labels" :key="idx">
                                        <tr class="hover:bg-white/5 transition">
                                            <td class="px-4 py-2.5 font-medium text-white" x-text="label"></td>
                                            <td class="px-4 py-2.5 text-right font-semibold text-purple-300" x-text="reportData.values[idx]"></td>
                                            <td class="px-4 py-2.5 text-right text-gray-400" x-text="reportData.total > 0 ? ((reportData.values[idx] / reportData.total) * 100).toFixed(1) + '%' : '0%'"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <!-- 2. LOGINS REPORT CARD -->
            <div class="bg-gray-800/60 backdrop-blur-md rounded-2xl border border-white/10 overflow-hidden shadow-2xl"
                 x-data="reportComponent('logins', {{ $loginYear }}, '{{ $loginPeriod }}', @js($loginData), '#10b981', 'rgba(16, 185, 129, 0.15)')">
                <div class="p-6 border-b border-white/10 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-gradient-to-r from-emerald-900/20 to-transparent">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-xl font-bold">
                            🔑
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">2. Laporan Log Masuk Berjaya (Successfully Authenticated Logins)</h3>
                            <p class="text-xs text-gray-400">Statistik pengguna yang berjaya log masuk ke dalam akaun.</p>
                        </div>
                    </div>

                    <!-- Filter & Action Bar -->
                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Year Selector -->
                        <div class="flex items-center gap-2 bg-gray-900/80 px-3 py-1.5 rounded-xl border border-white/10">
                            <span class="text-xs font-semibold text-gray-400">Tahun:</span>
                            <select x-model="year" @change="fetchData()" class="bg-transparent text-xs font-bold text-emerald-400 border-none focus:ring-0 cursor-pointer py-1 pr-6 pl-1">
                                @foreach($years as $y)
                                    <option value="{{ $y }}" class="bg-gray-900 text-white">{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Period Options (Daily, Weeks, Month) -->
                        <div class="flex items-center bg-gray-900/80 p-1 rounded-xl border border-white/10">
                            <button @click="setPeriod('daily')" :class="period === 'daily' ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-400 hover:text-white'" class="px-3 py-1 text-xs font-semibold rounded-lg transition">
                                Harian
                            </button>
                            <button @click="setPeriod('weeks')" :class="period === 'weeks' ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-400 hover:text-white'" class="px-3 py-1 text-xs font-semibold rounded-lg transition">
                                Mingguan
                            </button>
                            <button @click="setPeriod('month')" :class="period === 'month' ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-400 hover:text-white'" class="px-3 py-1 text-xs font-semibold rounded-lg transition">
                                Bulanan
                            </button>
                        </div>

                        <!-- Export Buttons -->
                        <div class="flex items-center gap-2">
                            <a :href="getExportUrl('excel')" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/30 text-xs font-bold rounded-xl transition shadow-sm" title="Eksport ke Excel / CSV">
                                📗 Excel
                            </a>
                            <a :href="getExportUrl('pdf')" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/30 text-xs font-bold rounded-xl transition shadow-sm" title="Eksport ke PDF">
                                📕 PDF
                            </a>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Summary Metric Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="bg-gray-900/50 p-4 rounded-xl border border-white/5">
                            <span class="text-xs text-gray-400 uppercase font-semibold">Jumlah Log Masuk Berjaya</span>
                            <div class="text-2xl font-extrabold text-white mt-1" x-text="reportData.total"></div>
                        </div>
                        <div class="bg-gray-900/50 p-4 rounded-xl border border-white/5">
                            <span class="text-xs text-gray-400 uppercase font-semibold">Tempoh Kemuncak</span>
                            <div class="text-xl font-bold text-emerald-400 mt-1 flex items-center gap-2">
                                <span x-text="reportData.peakLabel"></span>
                                <span class="text-xs font-semibold bg-emerald-500/20 text-emerald-300 px-2 py-0.5 rounded-full" x-text="reportData.peakValue + ' log masuk'"></span>
                            </div>
                        </div>
                        <div class="bg-gray-900/50 p-4 rounded-xl border border-white/5">
                            <span class="text-xs text-gray-400 uppercase font-semibold">Purata per Tempoh</span>
                            <div class="text-2xl font-extrabold text-white mt-1" x-text="reportData.avgValue"></div>
                        </div>
                    </div>

                    <!-- Line Chart Container -->
                    <div class="bg-gray-900/60 p-4 rounded-2xl border border-white/5 relative">
                        <div x-show="loading" class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm rounded-2xl z-10 flex items-center justify-center">
                            <div class="flex items-center gap-2 text-emerald-400 text-sm font-semibold animate-pulse">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Memuatkan data...
                            </div>
                        </div>
                        <div class="h-72 w-full">
                            <canvas x-ref="chartCanvas"></canvas>
                        </div>
                    </div>

                    <!-- Table Breakdown -->
                    <div x-data="{ showTable: false }" class="border-t border-white/5 pt-4">
                        <button @click="showTable = !showTable" class="text-xs font-bold text-emerald-400 hover:text-emerald-300 flex items-center gap-1.5">
                            <span x-text="showTable ? '▲ Sembunyikan Jadual Data' : '▼ Papar Jadual Data Terperinci'"></span>
                        </button>
                        <div x-show="showTable" x-transition class="mt-4 overflow-x-auto">
                            <table class="w-full text-left text-xs text-gray-300 divide-y divide-white/5">
                                <thead class="bg-gray-900/80 text-gray-400 uppercase font-bold">
                                    <tr>
                                        <th class="px-4 py-3">Tempoh / Nisbah</th>
                                        <th class="px-4 py-3 text-right">Log Masuk Berjaya</th>
                                        <th class="px-4 py-3 text-right">Peratusan (%)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    <template x-for="(label, idx) in reportData.labels" :key="idx">
                                        <tr class="hover:bg-white/5 transition">
                                            <td class="px-4 py-2.5 font-medium text-white" x-text="label"></td>
                                            <td class="px-4 py-2.5 text-right font-semibold text-emerald-300" x-text="reportData.values[idx]"></td>
                                            <td class="px-4 py-2.5 text-right text-gray-400" x-text="reportData.total > 0 ? ((reportData.values[idx] / reportData.total) * 100).toFixed(1) + '%' : '0%'"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <!-- 3. PROMPTS ADDED REPORT CARD -->
            <div class="bg-gray-800/60 backdrop-blur-md rounded-2xl border border-white/10 overflow-hidden shadow-2xl"
                 x-data="reportComponent('prompts', {{ $promptYear }}, '{{ $promptPeriod }}', @js($promptData), '#ec4899', 'rgba(236, 72, 153, 0.15)')">
                <div class="p-6 border-b border-white/10 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-gradient-to-r from-pink-900/20 to-transparent">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-pink-500/20 border border-pink-500/30 flex items-center justify-center text-pink-400 text-xl font-bold">
                            ✨
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">3. Laporan Prompt Ditambah (Prompts Added Report)</h3>
                            <p class="text-xs text-gray-400">Statistik penambahan koleksi prompt AI baharu dalam sistem.</p>
                        </div>
                    </div>

                    <!-- Filter & Action Bar -->
                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Year Selector -->
                        <div class="flex items-center gap-2 bg-gray-900/80 px-3 py-1.5 rounded-xl border border-white/10">
                            <span class="text-xs font-semibold text-gray-400">Tahun:</span>
                            <select x-model="year" @change="fetchData()" class="bg-transparent text-xs font-bold text-pink-400 border-none focus:ring-0 cursor-pointer py-1 pr-6 pl-1">
                                @foreach($years as $y)
                                    <option value="{{ $y }}" class="bg-gray-900 text-white">{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Period Options (Daily, Weeks, Month) -->
                        <div class="flex items-center bg-gray-900/80 p-1 rounded-xl border border-white/10">
                            <button @click="setPeriod('daily')" :class="period === 'daily' ? 'bg-pink-600 text-white shadow-md' : 'text-gray-400 hover:text-white'" class="px-3 py-1 text-xs font-semibold rounded-lg transition">
                                Harian
                            </button>
                            <button @click="setPeriod('weeks')" :class="period === 'weeks' ? 'bg-pink-600 text-white shadow-md' : 'text-gray-400 hover:text-white'" class="px-3 py-1 text-xs font-semibold rounded-lg transition">
                                Mingguan
                            </button>
                            <button @click="setPeriod('month')" :class="period === 'month' ? 'bg-pink-600 text-white shadow-md' : 'text-gray-400 hover:text-white'" class="px-3 py-1 text-xs font-semibold rounded-lg transition">
                                Bulanan
                            </button>
                        </div>

                        <!-- Export Buttons -->
                        <div class="flex items-center gap-2">
                            <a :href="getExportUrl('excel')" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/30 text-xs font-bold rounded-xl transition shadow-sm" title="Eksport ke Excel / CSV">
                                📗 Excel
                            </a>
                            <a :href="getExportUrl('pdf')" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/30 text-xs font-bold rounded-xl transition shadow-sm" title="Eksport ke PDF">
                                📕 PDF
                            </a>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Summary Metric Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="bg-gray-900/50 p-4 rounded-xl border border-white/5">
                            <span class="text-xs text-gray-400 uppercase font-semibold">Jumlah Prompt Ditambah</span>
                            <div class="text-2xl font-extrabold text-white mt-1" x-text="reportData.total"></div>
                        </div>
                        <div class="bg-gray-900/50 p-4 rounded-xl border border-white/5">
                            <span class="text-xs text-gray-400 uppercase font-semibold">Tempoh Kemuncak</span>
                            <div class="text-xl font-bold text-pink-400 mt-1 flex items-center gap-2">
                                <span x-text="reportData.peakLabel"></span>
                                <span class="text-xs font-semibold bg-pink-500/20 text-pink-300 px-2 py-0.5 rounded-full" x-text="reportData.peakValue + ' prompt'"></span>
                            </div>
                        </div>
                        <div class="bg-gray-900/50 p-4 rounded-xl border border-white/5">
                            <span class="text-xs text-gray-400 uppercase font-semibold">Purata per Tempoh</span>
                            <div class="text-2xl font-extrabold text-white mt-1" x-text="reportData.avgValue"></div>
                        </div>
                    </div>

                    <!-- Line Chart Container -->
                    <div class="bg-gray-900/60 p-4 rounded-2xl border border-white/5 relative">
                        <div x-show="loading" class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm rounded-2xl z-10 flex items-center justify-center">
                            <div class="flex items-center gap-2 text-pink-400 text-sm font-semibold animate-pulse">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Memuatkan data...
                            </div>
                        </div>
                        <div class="h-72 w-full">
                            <canvas x-ref="chartCanvas"></canvas>
                        </div>
                    </div>

                    <!-- Table Breakdown -->
                    <div x-data="{ showTable: false }" class="border-t border-white/5 pt-4">
                        <button @click="showTable = !showTable" class="text-xs font-bold text-pink-400 hover:text-pink-300 flex items-center gap-1.5">
                            <span x-text="showTable ? '▲ Sembunyikan Jadual Data' : '▼ Papar Jadual Data Terperinci'"></span>
                        </button>
                        <div x-show="showTable" x-transition class="mt-4 overflow-x-auto">
                            <table class="w-full text-left text-xs text-gray-300 divide-y divide-white/5">
                                <thead class="bg-gray-900/80 text-gray-400 uppercase font-bold">
                                    <tr>
                                        <th class="px-4 py-3">Tempoh / Nisbah</th>
                                        <th class="px-4 py-3 text-right">Prompt Ditambah</th>
                                        <th class="px-4 py-3 text-right">Peratusan (%)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    <template x-for="(label, idx) in reportData.labels" :key="idx">
                                        <tr class="hover:bg-white/5 transition">
                                            <td class="px-4 py-2.5 font-medium text-white" x-text="label"></td>
                                            <td class="px-4 py-2.5 text-right font-semibold text-pink-300" x-text="reportData.values[idx]"></td>
                                            <td class="px-4 py-2.5 text-right text-gray-400" x-text="reportData.total > 0 ? ((reportData.values[idx] / reportData.total) * 100).toFixed(1) + '%' : '0%'"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Alpine.js Component Script for Charts & AJAX -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('reportComponent', (type, initialYear, initialPeriod, initialData, colorHex = '#a855f7', bgRgba = 'rgba(168, 85, 247, 0.15)') => ({
                type: type,
                year: initialYear,
                period: initialPeriod,
                reportData: initialData,
                loading: false,
                chartInstance: null,

                init() {
                    this.renderChart();
                },

                setPeriod(p) {
                    if (this.period !== p) {
                        this.period = p;
                        this.fetchData();
                    }
                },

                getExportUrl(format) {
                    return `{{ route('admin.reports.export') }}?type=${this.type}&year=${this.year}&period=${this.period}&format=${format}`;
                },

                fetchData() {
                    this.loading = true;
                    fetch(`{{ route('admin.reports.data') }}?type=${this.type}&year=${this.year}&period=${this.period}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.reportData = data.data;
                                this.updateChart();
                            }
                        })
                        .catch(err => console.error(err))
                        .finally(() => {
                            this.loading = false;
                        });
                },

                renderChart() {
                    const ctx = this.$refs.chartCanvas.getContext('2d');
                    this.chartInstance = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: this.reportData.labels,
                            datasets: [{
                                label: 'Jumlah',
                                data: this.reportData.values,
                                borderColor: colorHex,
                                backgroundColor: bgRgba,
                                borderWidth: 3,
                                fill: true,
                                tension: 0.35,
                                pointBackgroundColor: colorHex,
                                pointBorderColor: '#0f172a',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 7
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#1e293b',
                                    titleColor: '#ffffff',
                                    bodyColor: '#e2e8f0',
                                    borderColor: 'rgba(255,255,255,0.1)',
                                    borderWidth: 1,
                                    padding: 10,
                                    displayColors: false,
                                    callbacks: {
                                        label: function(context) {
                                            return `Jumlah: ${context.parsed.y} rekod`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                    ticks: { color: '#94a3b8', font: { size: 11 } }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                    ticks: { color: '#94a3b8', font: { size: 11 }, precision: 0 }
                                }
                            }
                        }
                    });
                },

                updateChart() {
                    if (this.chartInstance) {
                        this.chartInstance.data.labels = this.reportData.labels;
                        this.chartInstance.data.datasets[0].data = this.reportData.values;
                        this.chartInstance.update();
                    }
                }
            }));
        });
    </script>
</x-app-layout>

<x-app-layout>
    <div class="max-w-7xl mx-auto">
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-6 shadow-[0_12px_40px_rgba(0,0,0,0.45)]">
            <h1 class="text-xl font-extrabold text-indigo-200 mb-1">Dashboard</h1>
            <p class="text-sm text-slate-400">Selamat datang, {{ auth()->user()->name }}.</p>
        </div>

        <!-- Chart Section -->
        @php
            $playerStats = \App\Models\Player::selectRaw('DATE(created_at) as d, COUNT(*) as c')
                ->groupBy('d')->orderBy('d')->limit(30)->get();
        @endphp
        <div class="mt-6 rounded-2xl border border-slate-800 bg-slate-900/60 p-6 shadow-[0_8px_28px_rgba(0,0,0,0.45)]">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-sm font-semibold text-slate-300">Grafik Pendaftaran Pemain (30 hari)</h2>
            </div>
            <canvas id="playersChart" height="120"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('playersChart');
            if (ctx){
                const labels = @json(($playerStats ?? collect())->pluck('d'));
                const data = @json(($playerStats ?? collect())->pluck('c'));
                if(labels.length === 0){
                    labels.push(new Date().toISOString().slice(0,10));
                    data.push(0);
                }
                const gradient = ctx.getContext('2d').createLinearGradient(0,0,0,200);
                gradient.addColorStop(0, 'rgba(99,102,241,.45)');
                gradient.addColorStop(1, 'rgba(99,102,241,0)');
                new Chart(ctx, {
                    type: 'line',
                    data: { labels, datasets: [{ label: 'Pemain', data, tension: .35, borderColor: '#818cf8', backgroundColor: gradient, fill: true, pointRadius: 3, pointHoverRadius: 5 }]},
                    options: { responsive: true, plugins: { legend: { display:false } }, scales: { x: { grid: { color:'rgba(148,163,184,.1)'} }, y:{ ticks:{ color:'#94a3b8' }, grid:{ color:'rgba(148,163,184,.08)'} } }, animations:{ tension: { duration: 800, easing: 'easeOutQuad' } } }
                });
            }
        });
    </script>
</x-app-layout>

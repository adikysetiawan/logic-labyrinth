<x-app-layout>
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header Card -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-6 shadow-[0_12px_40px_rgba(0,0,0,0.45)]">
            <h1 class="text-xl font-extrabold text-indigo-200">Rekap Permainan</h1>
            <p class="text-sm text-slate-400 mt-1">Ringkasan permainan pemain</p>
        </div>

        <!-- Metrics -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 shadow-[0_8px_28px_rgba(0,0,0,0.45)]">
                <div class="text-xs text-slate-400">Total</div>
                <div class="text-2xl font-extrabold text-indigo-200">{{ $metrics['total'] ?? 0 }}</div>
            </div>
            <div class="rounded-2xl border border-emerald-800/40 bg-emerald-900/30 p-4 shadow-[0_8px_28px_rgba(0,0,0,0.45)]">
                <div class="text-xs text-emerald-300">Berhasil</div>
                <div class="text-2xl font-extrabold text-emerald-200">{{ $metrics['success'] ?? 0 }}</div>
            </div>
            <div class="rounded-2xl border border-rose-800/40 bg-rose-900/30 p-4 shadow-[0_8px_28px_rgba(0,0,0,0.45)]">
                <div class="text-xs text-rose-300">Gagal</div>
                <div class="text-2xl font-extrabold text-rose-200">{{ $metrics['failed'] ?? 0 }}</div>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 shadow-[0_8px_28px_rgba(0,0,0,0.45)] col-span-2 md:col-span-1">
                <div class="text-xs text-slate-400">Mudah / Susah</div>
                <div class="text-lg text-slate-200">{{ $metrics['easy'] ?? 0 }} / {{ $metrics['hard'] ?? 0 }}</div>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 shadow-[0_8px_28px_rgba(0,0,0,0.45)] flex flex-wrap items-end gap-3">
            <div class="relative">
                <label class="block text-xs text-slate-400 mb-1">Kesulitan</label>
                <select name="difficulty" class="rounded-xl bg-slate-950/70 border border-slate-700 text-slate-100 px-3 py-2">
                    <option value="">Semua</option>
                    <option value="easy" @selected(request('difficulty')==='easy')>Mudah</option>
                    <option value="hard" @selected(request('difficulty')==='hard')>Susah</option>
                </select>
            </div>
            <div class="relative">
                <label class="block text-xs text-slate-400 mb-1">Hasil</label>
                <select name="success" class="rounded-xl bg-slate-950/70 border border-slate-700 text-slate-100 px-3 py-2">
                    <option value="">Semua</option>
                    <option value="1" @selected(request('success')==='1')>Berhasil</option>
                    <option value="0" @selected(request('success')==='0')>Gagal</option>
                </select>
            </div>
            <div class="flex items-end gap-3">
                <div class="relative">
                    <label class="block text-xs text-slate-400 mb-1">Urutkan</label>
                    <select name="sort" class="rounded-xl bg-slate-950/70 border border-slate-700 text-slate-100 px-3 py-2">
                        <option value="-created_at" @selected(request('sort','-created_at')==='-created_at')>Terbaru</option>
                        <option value="created_at" @selected(request('sort')==='created_at')>Terlama</option>
                        <option value="player" @selected(request('sort')==='player')>Nama Pemain (A-Z)</option>
                        <option value="-player" @selected(request('sort')==='-player')>Nama Pemain (Z-A)</option>
                        <option value="success" @selected(request('sort')==='success')>Hasil (Gagal→Berhasil)</option>
                        <option value="-success" @selected(request('sort')==='-success')>Hasil (Berhasil→Gagal)</option>
                    </select>
                </div>
            </div>
            <div class="ml-auto flex items-center gap-3">
                <button class="btn-ll px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700">Terapkan</button>
                <a href="{{ route('admin.game-plays') }}" class="px-4 py-2.5 rounded-xl border border-slate-700 text-slate-200 hover:bg-white/5">Reset</a>
            </div>
        </form>

        <!-- Table -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 shadow-[0_8px_28px_rgba(0,0,0,0.45)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-slate-900/70 text-slate-300 text-xs uppercase">
                            <th class="px-4 py-3 text-left">ID</th>
                            <th class="px-4 py-3 text-left">Waktu</th>
                            <th class="px-4 py-3 text-left">Pemain</th>
                            <th class="px-4 py-3 text-left">Kesulitan</th>
                            <th class="px-4 py-3 text-left">Level</th>
                            <th class="px-4 py-3 text-left">Hasil</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200">
                        @forelse($plays as $p)
                            <tr class="hover:bg-white/5">
                                <td class="px-4 py-3">{{ $p->id }}</td>
                                <td class="px-4 py-3 text-sm">{{ $p->created_at?->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-3 text-sm">{{ $p->player->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm capitalize">{{ $p->difficulty }}</td>
                                <td class="px-4 py-3 text-sm">#{{ $p->level_id }}</td>
                                <td class="px-4 py-3">
                                    @if($p->success)
                                        <span class="px-2 py-1 text-xs rounded bg-emerald-900/40 text-emerald-200 border border-emerald-800/40">Berhasil</span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded bg-rose-900/40 text-rose-200 border border-rose-800/40">Gagal</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-slate-400">Belum ada data permainan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $plays->links() }}
        </div>
    </div>
</x-app-layout>

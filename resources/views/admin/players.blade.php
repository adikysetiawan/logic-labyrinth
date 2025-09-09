<x-app-layout>
    <div class="max-w-7xl mx-auto">
        <!-- Header Card -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-6 shadow-[0_12px_40px_rgba(0,0,0,0.45)] mb-6">
            <h1 class="text-xl font-extrabold text-indigo-200">Data Pemain</h1>
            <p class="text-sm text-slate-400 mt-1">Kelola data pemain Logic Labyrinth</p>
        </div>

        <!-- Search + Sort Card -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 shadow-[0_8px_28px_rgba(0,0,0,0.45)] mb-4">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Cari nama/universitas/prodi..." class="flex-1 min-w-[240px] rounded-xl bg-slate-950/70 border border-slate-700 text-slate-100 placeholder-slate-400 px-4 py-2 focus:ring-2 focus:ring-indigo-500/70 focus:border-indigo-500/70" />
                <div class="relative">
                    <label class="block text-xs text-slate-400 mb-1">Urutkan</label>
                    <select name="sort" class="appearance-none pr-8 rounded-xl bg-slate-950/70 border border-slate-700 text-slate-100 px-3 py-2">
                        <option value="-id" @selected(request('sort')==='-id')>Terbaru</option>
                        <option value="id" @selected(request('sort')==='id')>Terlama</option>
                        <option value="name" @selected(request('sort')==='name')>Nama (A-Z)</option>
                        <option value="-name" @selected(request('sort')==='-name')>Nama (Z-A)</option>
                        <option value="total_main" @selected(request('sort')==='total_main')>Total Main (Naik)</option>
                        <option value="-total_main" @selected(request('sort')==='-total_main')>Total Main (Turun)</option>
                        <option value="score" @selected(request('sort')==='score')>Score (Poin) Naik</option>
                        <option value="-score" @selected(request('sort')==='-score')>Score (Poin) Turun</option>
                    </select>
                </div>
                <button class="btn-ll px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700">Cari</button>
                @if($q || request('sort'))
                    <a href="{{ route('admin.players') }}" class="px-4 py-2.5 rounded-xl border border-slate-700 text-slate-200 hover:bg-white/5">Reset</a>
                @endif
            </form>
        </div>

        @if (session('status'))
            <div class="mb-4 p-3 rounded-xl border border-emerald-800/40 bg-emerald-900/40 text-emerald-200">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 p-3 rounded-xl border border-rose-800/40 bg-rose-900/40 text-rose-200">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Table Card -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 shadow-[0_8px_28px_rgba(0,0,0,0.45)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-slate-900/70 text-slate-300 text-xs uppercase">
                            <th class="px-4 py-3 text-left">Nama</th>
                            <th class="px-4 py-3 text-left">Universitas</th>
                            <th class="px-4 py-3 text-left">Prodi</th>
                            <th class="px-4 py-3 text-center">Score</th>
                            <th class="px-4 py-3 text-center">Total Main</th>
                            <th class="px-4 py-3 text-center">Berhasil</th>
                            <th class="px-4 py-3 text-center">Gagal</th>
                            <th class="px-4 py-3 text-center">Waktu Terakhir Main</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200">
                        @forelse($players as $player)
                            <tr class="hover:bg-white/5">
                                <td class="px-4 py-3">{{ $player->name }}</td>
                                <td class="px-4 py-3 text-sm">{{ $player->university }}</td>
                                <td class="px-4 py-3 text-sm">{{ $player->program }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 text-xs rounded bg-slate-800 text-slate-200 border border-slate-700">{{ number_format($player->total_score ?? 0, 2) }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">{{ $player->total_main }}</td>
                                <td class="px-4 py-3 text-center">{{ $player->total_berhasil }}</td>
                                <td class="px-4 py-3 text-center">{{ $player->total_gagal }}</td>
                                <td class="px-4 py-3 text-center text-xs">{{ optional($player->gamePlays->first())->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <form action="{{ route('admin.players.destroy', $player) }}" method="POST" onsubmit="return confirm('Hapus player ini?')" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 text-xs rounded-lg bg-rose-600 text-white hover:bg-rose-700">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-slate-400">Belum ada player.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $players->links() }}
        </div>
    </div>
</x-app-layout>

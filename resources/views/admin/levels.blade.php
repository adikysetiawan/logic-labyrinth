<x-app-layout>
    <div class="max-w-7xl mx-auto">
        <!-- Header Card -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-6 shadow-[0_12px_40px_rgba(0,0,0,0.45)] mb-6">
            <h1 class="text-xl font-extrabold text-indigo-200">Data Level</h1>
            <p class="text-sm text-slate-400 mt-1">Kelola level permainan</p>
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

        <!-- Create Level Card -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 shadow-[0_8px_28px_rgba(0,0,0,0.45)] mb-4">
            <h3 class="text-sm font-semibold text-slate-300 mb-3">Buat Level Baru</h3>
            <form action="{{ route('admin.levels.store') }}" method="POST" class="grid md:grid-cols-2 gap-4">
                @csrf
                <div class="md:col-span-2">
                    <label class="block text-xs text-slate-400 mb-1">Kode (snippet)</label>
                    <textarea name="code" rows="4" class="w-full rounded-xl bg-slate-950/70 border border-slate-700 text-slate-100 placeholder-slate-400 px-3 py-2 focus:ring-2 focus:ring-indigo-500/70 focus:border-indigo-500/70" placeholder="contoh: moveRight(); moveDown();"></textarea>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Tingkat Kesulitan</label>
                    <select name="difficulty" class="w-full rounded-xl bg-slate-950/70 border border-slate-700 text-slate-100 px-3 py-2 focus:ring-2 focus:ring-indigo-500/70 focus:border-indigo-500/70">
                        <option value="easy">Mudah</option>
                        <option value="hard">Susah</option>
                    </select>
                </div>
                <!-- Start Position -->
                <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-3">
                    <div class="text-xs text-slate-400 mb-2">Posisi Awal (Start)</div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Start X (0-4)</label>
                            <input type="number" name="start_x" min="0" max="4" class="w-full rounded-xl bg-slate-950/70 border border-slate-700 text-slate-100 px-3 py-2 focus:ring-2 focus:ring-indigo-500/70 focus:border-indigo-500/70" />
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Start Y (0-4)</label>
                            <input type="number" name="start_y" min="0" max="4" class="w-full rounded-xl bg-slate-950/70 border border-slate-700 text-slate-100 px-3 py-2 focus:ring-2 focus:ring-indigo-500/70 focus:border-indigo-500/70" />
                        </div>
                    </div>
                </div>
                <!-- Correct Answer -->
                <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-3">
                    <div class="text-xs text-slate-400 mb-2">Jawaban Benar (Target)</div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">X (0-4)</label>
                            <input type="number" name="x" min="0" max="4" class="w-full rounded-xl bg-slate-950/70 border border-slate-700 text-slate-100 px-3 py-2 focus:ring-2 focus:ring-indigo-500/70 focus:border-indigo-500/70" />
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Y (0-4)</label>
                            <input type="number" name="y" min="0" max="4" class="w-full rounded-xl bg-slate-950/70 border border-slate-700 text-slate-100 px-3 py-2 focus:ring-2 focus:ring-indigo-500/70 focus:border-indigo-500/70" />
                        </div>
                    </div>
                </div>
                <div class="md:col-span-2 flex justify-end">
                    <button type="submit" class="btn-ll px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700">Simpan</button>
                </div>
            </form>
        </div>

        <!-- Filter Bar -->
        <form method="GET" action="{{ route('admin.levels') }}" class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 shadow-[0_8px_28px_rgba(0,0,0,0.45)] mb-4 flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs text-slate-400 mb-1">Cari Kode</label>
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="cari di kode perintah" class="rounded-xl bg-slate-950/70 border border-slate-700 text-slate-100 px-3 py-2" />
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1">Kesulitan</label>
                <select name="difficulty" class="rounded-xl bg-slate-950/70 border border-slate-700 text-slate-100 px-3 py-2">
                    <option value="" @selected(($difficulty ?? '')==='')>Semua</option>
                    <option value="easy" @selected(($difficulty ?? '')==='easy')>Mudah</option>
                    <option value="hard" @selected(($difficulty ?? '')==='hard')>Susah</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1">Urutkan</label>
                <select name="sort" class="rounded-xl bg-slate-950/70 border border-slate-700 text-slate-100 px-3 py-2">
                    <option value="-created_at" @selected(($sort ?? '-created_at')==='-created_at')>Terbaru</option>
                    <option value="created_at" @selected(($sort ?? '')==='created_at')>Terlama</option>
                    <option value="difficulty" @selected(($sort ?? '')==='difficulty')>Kesulitan (A-Z)</option>
                    <option value="-difficulty" @selected(($sort ?? '')==='-difficulty')>Kesulitan (Z-A)</option>
                </select>
            </div>
            <div class="ml-auto flex items-center gap-3">
                <button class="btn-ll px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700">Terapkan</button>
                @if(($q ?? '') !== '' || ($difficulty ?? '') !== '' || ($sort ?? '-created_at') !== '-created_at')
                    <a href="{{ route('admin.levels') }}" class="px-4 py-2.5 rounded-xl border border-slate-700 text-slate-200 hover:bg-white/5">Reset</a>
                @endif
            </div>
        </form>

        <!-- Levels Table Card -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 shadow-[0_8px_28px_rgba(0,0,0,0.45)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-slate-900/70 text-slate-300 text-xs uppercase">
                            <th class="px-4 py-3 text-left">ID</th>
                            <th class="px-4 py-3 text-left">Kesulitan</th>
                            <th class="px-4 py-3 text-left">Kode</th>
                            <th class="px-4 py-3 text-left">Jawaban Benar</th>
                            <th class="px-4 py-3 text-left">Dibuat</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200">
                        @forelse($levels as $level)
                            <tr class="hover:bg-white/5">
                                <td class="px-4 py-3">{{ $level->id }}</td>
                                <td class="px-4 py-3 text-sm">{{ ucfirst($level->difficulty ?? 'easy') }}</td>
                                <td class="px-4 py-3 whitespace-pre text-xs bg-slate-950/60 border border-slate-800 rounded">{{ \Illuminate\Support\Str::limit($level->code, 120) }}</td>
                                <td class="px-4 py-3 text-sm">{{ json_encode($level->correct_answer) }}</td>
                                <td class="px-4 py-3">{{ $level->created_at->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <details>
                                        <summary class="cursor-pointer text-xs text-indigo-300">Edit</summary>
                                        <form action="{{ route('admin.levels.update', $level) }}" method="POST" class="mt-2 grid md:grid-cols-2 gap-3">
                                            @csrf
                                            @method('PUT')
                                            <div class="md:col-span-2">
                                                <label class="block text-xs text-slate-400 mb-1">Kode</label>
                                                <textarea name="code" rows="3" class="w-full rounded-xl bg-slate-950/70 border border-slate-700 text-slate-100 px-3 py-2 focus:ring-2 focus:ring-indigo-500/70 focus:border-indigo-500/70">{{ $level->code }}</textarea>
                                            </div>
                                            <div>
                                                <label class="block text-xs text-slate-400 mb-1">Tingkat Kesulitan</label>
                                                <select name="difficulty" class="w-full rounded-xl bg-slate-950/70 border border-slate-700 text-slate-100 px-3 py-2 focus:ring-2 focus:ring-indigo-500/70 focus:border-indigo-500/70">
                                                    <option value="easy" @selected(($level->difficulty ?? 'easy')==='easy')>Mudah</option>
                                                    <option value="hard" @selected(($level->difficulty ?? '')==='hard')>Susah</option>
                                                </select>
                                            </div>
                                            <div class="grid grid-cols-4 gap-3">
                                                <div>
                                                    <label class="block text-xs text-slate-400 mb-1">X</label>
                                                    <input type="number" name="x" min="0" max="4" class="w-full rounded-xl bg-slate-950/70 border border-slate-700 text-slate-100 px-3 py-2" value="{{ $level->correct_answer['x'] ?? '' }}" />
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-slate-400 mb-1">Y</label>
                                                    <input type="number" name="y" min="0" max="4" class="w-full rounded-xl bg-slate-950/70 border border-slate-700 text-slate-100 px-3 py-2" value="{{ $level->correct_answer['y'] ?? '' }}" />
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-slate-400 mb-1">Start X</label>
                                                    <input type="number" name="start_x" min="0" max="4" class="w-full rounded-xl bg-slate-950/70 border border-slate-700 text-slate-100 px-3 py-2" value="{{ $level->start_at['x'] ?? '' }}" />
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-slate-400 mb-1">Start Y</label>
                                                    <input type="number" name="start_y" min="0" max="4" class="w-full rounded-xl bg-slate-950/70 border border-slate-700 text-slate-100 px-3 py-2" value="{{ $level->start_at['y'] ?? '' }}" />
                                                </div>
                                            </div>
                                            <div class="md:col-span-2 flex gap-2 justify-end">
                                                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700">Update</button>
                                                <form action="{{ route('admin.levels.destroy', $level) }}" method="POST" onsubmit="return confirm('Hapus level ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 text-white text-xs font-semibold hover:bg-rose-700">Hapus</button>
                                                </form>
                                            </div>
                                        </form>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-slate-400">Belum ada level.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $levels->links() }}
        </div>
    </div>
</x-app-layout>

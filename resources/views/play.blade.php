<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Play - {{ config('app.name', 'Logic Labyrinth') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Prism.js for code highlighting -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/themes/prism-tomorrow.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/plugins/line-numbers/prism-line-numbers.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/prism.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-python.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/plugins/line-numbers/prism-line-numbers.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Background dots + soft glows (dark) */
        body.bg-ll::before{
            content:""; position:fixed; inset:0; z-index:-1; pointer-events:none;
            background-image:
                radial-gradient(closest-side, rgba(37,99,235,0.16), rgba(15,23,42,0) 60%),
                radial-gradient(closest-side, rgba(249,115,22,0.12), rgba(15,23,42,0) 60%),
                radial-gradient(circle at 1px 1px, rgba(255,255,255,0.06) 1px, transparent 1px);
            background-repeat: no-repeat, no-repeat, repeat;
            background-size: 32rem 32rem, 36rem 36rem, 24px 24px;
            background-position: -12rem -10rem, calc(100% + 8rem) calc(100% + 6rem), 0 0;
        }
        .poppins{ font-family: 'Poppins', Arial, Helvetica, sans-serif; }
        /* Glossy button (shared style) */
        .btn-ll { position: relative; overflow: hidden; transition-property: background, box-shadow, transform; transition-duration: 300ms; transition-timing-function: cubic-bezier(.4,0,.2,1); box-shadow: 0 2px 12px 0 rgba(0,0,0,0.07); }
        .btn-ll:hover { filter: brightness(1.05); transform: translateY(-1px); box-shadow: 0 8px 26px 0 rgba(0,0,0,0.12); }
        .btn-ll::after { content: ""; position: absolute; top: -40%; left: -30%; width: 40%; height: 180%; background: linear-gradient(120deg, rgba(255,255,255,0) 0%, rgba(255,255,255,.35) 50%, rgba(255,255,255,0) 100%); transform: translateX(-120%) rotate(12deg); transition: transform .5s ease; }
        .btn-ll:hover::after { transform: translateX(260%) rotate(12deg); }
        .btn-ll::before { content:""; position:absolute; left:50%; transform:translateX(-50%); bottom:-8px; width:72%; height:12px; background: radial-gradient(closest-side, rgba(0,0,0,0.18), rgba(0,0,0,0)); filter: blur(6px); transition: all .3s ease; border-radius:999px; }
        .btn-ll:hover::before { bottom:-10px; width:78%; filter: blur(8px); opacity:.95; }
        /* Timer card (dedicated, red attention) */
        .timer-card{ position:relative; overflow:hidden; background: rgba(239,68,68,.9); color:#fff; box-shadow: 0 8px 26px rgba(239,68,68,.25); border-radius:.5rem; border:1px solid rgba(255,255,255,.22); backdrop-filter: blur(2px); transition: transform .25s ease, box-shadow .25s ease; }
        .timer-card:hover{ transform: translateY(-1px); box-shadow: 0 12px 34px rgba(239,68,68,.35); }
        .timer-card::after{ content:""; position:absolute; top:-40%; left:-30%; width:40%; height:180%; background: linear-gradient(120deg, rgba(255,255,255,0) 0%, rgba(255,255,255,.45) 50%, rgba(255,255,255,0) 100%); transform: translateX(-120%) rotate(12deg); transition: transform .6s ease; }
        .timer-card:hover::after{ transform: translateX(260%) rotate(12deg); }
        /* Game grid */
        .grid-5x5 { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: .5rem; }
        .cell { position: relative; border-radius: .75rem; background: rgba(2,6,23,.9); border: 1px solid rgba(51,65,85,.6); box-shadow: 0 6px 14px rgba(0,0,0,.25); }
        .cell.active { background: #0b1220; border: 2px solid #60a5fa; box-shadow: 0 10px 22px rgba(37,99,235,.28); }
        /* Avatar overlay */
        #gridWrap{ position: relative; }
        #avatar{ position:absolute; top:0; left:0; display:flex; align-items:center; justify-content:center; width: 0; height: 0; pointer-events:none; transition: transform .25s ease; }
        #avatar img{ width: 64%; height: 64%; object-fit: contain; filter: drop-shadow(0 6px 10px rgba(0,0,0,.35)); }
        /* Axis labels (subtle) */
        #xAxis, #yAxis{ position:absolute; inset:0; pointer-events:none; z-index: 2; color:#94a3b8; opacity:.55; font-size:.75rem; font-weight:600; text-shadow: 0 1px 0 rgba(0,0,0,.35); }
        #xAxis .tick, #yAxis .tick{ position:absolute; transform: translate(-50%, -50%); }
        #yAxis .tick{ transform: translate(-50%, -50%); }
        .axis-hidden{ display:none; }
        /* Code card emphasis */
        .code-card{ position:relative; border-radius:1.25rem; background: linear-gradient(180deg, rgba(15,23,42,.92), rgba(2,6,23,.92)); border:1px solid rgba(99,102,241,.35); box-shadow: 0 12px 36px rgba(0,0,0,.45); }
        /* removed code tabs per request */
        .code-label{ color:#c7d2fe; text-shadow:0 1px 0 rgba(0,0,0,.3); }
        .code-area{ font-family: ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace; font-size: .95rem; line-height: 1.4; }
        /* Indentation guides + tab sizing for highlighted code */
        .code-box{ --tab-size: 28px; }
        .code-box code{ tab-size: 4; -moz-tab-size: 4; white-space: pre; display:block; }
        .code-box code{ background-image: repeating-linear-gradient(to right,
            rgba(255,255,255,0.0), rgba(255,255,255,0.0) calc(var(--tab-size) - 1px),
            rgba(255,255,255,0.06) calc(var(--tab-size) - 1px), rgba(255,255,255,0.06) var(--tab-size));
            background-clip: padding-box; }
        /* Overlay */
        .overlay-card{ position:relative; background: radial-gradient(1200px 600px at 50% -10%, rgba(99,102,241,.25), transparent 60%), linear-gradient(180deg, rgba(30,41,59,.98), rgba(17,24,39,.96)); border: 1px solid rgba(71,85,105,.7); box-shadow: 0 24px 60px rgba(0,0,0,.6); color:#e5e7eb; overflow:hidden; }
        .overlay-show{ animation: overlayFade .35s ease-out both; }
        @keyframes overlayFade{ from{ opacity:0; transform: translateY(8px) scale(.98);} to{ opacity:1; transform: translateY(0) scale(1);} }
        .result-img{ width: 200px; height: 200px; object-fit: contain; filter: drop-shadow(0 10px 22px rgba(0,0,0,.35)); animation: popIn .4s ease-out both; }
        @keyframes popIn{ 0%{ transform: scale(.7); opacity:0;} 60%{ transform: scale(1.08); opacity:1;} 100%{ transform: scale(1);} }
        .stars{ display:flex; gap:.35rem; justify-content:center; margin-top:.35rem; }
        .star{ color:#f59e0b; filter: drop-shadow(0 6px 10px rgba(245,158,11,.35)); animation: twinkle 1.2s ease-in-out infinite; }
        .star:nth-child(2){ animation-delay: .15s; }
        .star:nth-child(3){ animation-delay: .3s; }
        .cross{ color:#ef4444; filter: drop-shadow(0 6px 10px rgba(239,68,68,.35)); animation: twinkle 1.2s ease-in-out infinite; }
        .cross:nth-child(2){ animation-delay: .15s; }
        .cross:nth-child(3){ animation-delay: .3s; }
        @keyframes twinkle{ 0%,100%{ transform: translateY(0) scale(1);} 50%{ transform: translateY(-2px) scale(1.06);} }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const codeEl = document.getElementById('code');
            const upBtn = document.getElementById('upBtn');
            const downBtn = document.getElementById('downBtn');
            const leftBtn = document.getElementById('leftBtn');
            const rightBtn = document.getElementById('rightBtn');
            const restartBtn = document.getElementById('restartBtn');
            const finishBtn = document.getElementById('finishBtn');
            const statusEl = document.getElementById('status');
            const gridEl = document.getElementById('grid');
            const overlay = document.getElementById('resultOverlay');
            const overlayText = document.getElementById('resultText');
            const timerEl = document.getElementById('ll-timer');
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const state = { x: 0, y: 0, levelId: null, difficulty: null };
            const tigerImg = '/images/icons/play_icon.png';

            function wrapIdx(v){
                // keep within 0..4, wrapping around
                return (v + 5) % 5;
            }
            // timer (persist with localStorage so refresh will continue)
            let timerId = null; let startAt = Date.now();
            const LS_KEY = 'll-play-start-at';
            const LS_LEVEL_ID = 'll-play-level-id';
            const LS_POS = 'll-play-pos'; // {levelId:number,x:number,y:number}
            const LS_AXIS = 'll-play-axis-visible';
            function fmtTime(ms){
                const total = Math.floor(ms/1000);
                const m = Math.floor(total/60).toString().padStart(2,'0');
                const s = (total%60).toString().padStart(2,'0');
                return `${m}:${s}`;
            }
            function startTimer(){
                const saved = parseInt(localStorage.getItem(LS_KEY) || '0', 10);
                if(!saved || Number.isNaN(saved)){
                    startAt = Date.now();
                    localStorage.setItem(LS_KEY, String(startAt));
                } else {
                    startAt = saved;
                }
                if(timerEl){ timerEl.textContent = fmtTime(Date.now()-startAt); }
                if(timerId) clearInterval(timerId);
                timerId = setInterval(() => { if(timerEl) timerEl.textContent = fmtTime(Date.now()-startAt); }, 1000);
            }
            function stopTimer(){ if(timerId){ clearInterval(timerId); timerId=null; } }
            function elapsedSeconds(){ return Math.floor((Date.now() - startAt)/1000); }
            let cellsCache = [];
            const gridWrap = document.getElementById('gridWrap');
            const avatar = document.getElementById('avatar');
            const xAxis = document.getElementById('xAxis');
            const yAxis = document.getElementById('yAxis');
            function measureCells(){
                const rectWrap = gridWrap.getBoundingClientRect();
                cellsCache = Array.from(gridEl.children).map(el => {
                    const r = el.getBoundingClientRect();
                    return { x: r.left - rectWrap.left, y: r.top - rectWrap.top, w: r.width, h: r.height };
                });
                // position axis ticks based on first row/col cells
                if (cellsCache.length >= 25) {
                    // clear existing ticks
                    xAxis.innerHTML = ''; yAxis.innerHTML = '';
                    // top x labels (0..4) - outside grid (above first row)
                    for (let col=0; col<5; col++){
                        const c = cellsCache[col];
                        const tick = document.createElement('div');
                        tick.className = 'tick';
                        tick.textContent = String(col);
                        tick.style.left = (c.x + c.w/2) + 'px';
                        tick.style.top = (c.y - 15) + 'px';
                        xAxis.appendChild(tick);
                    }
                    // left y labels (0..4) - outside grid
                    for (let row=0; row<5; row++){
                        const idx = row * 5;
                        const c = cellsCache[idx];
                        const tick = document.createElement('div');
                        tick.className = 'tick';
                        tick.textContent = String(row);
                        tick.style.left = (c.x - 16) + 'px';
                        tick.style.top = (c.y + c.h/2) + 'px';
                        yAxis.appendChild(tick);
                    }
                }
            }
            function moveAvatar(){
                if(!cellsCache.length) measureCells();
                const idx = state.y * 5 + state.x;
                const c = cellsCache[idx];
                if(!c) return;
                // size avatar box to cell and move to cell's top-left
                avatar.style.width = `${c.w}px`;
                avatar.style.height = `${c.h}px`;
                avatar.style.transform = `translate(${c.x}px, ${c.y}px)`;
            }
            function savePos(){
                try{ localStorage.setItem(LS_POS, JSON.stringify({ levelId: state.levelId, x: state.x, y: state.y })); }catch(e){}
            }
            function loadPosIfSame(){
                try{
                    const raw = localStorage.getItem(LS_POS);
                    if(!raw) return false;
                    const obj = JSON.parse(raw);
                    if(obj && Number(obj.levelId) === Number(state.levelId)){
                        state.x = Math.max(0, Math.min(4, Number(obj.x)||0));
                        state.y = Math.max(0, Math.min(4, Number(obj.y)||0));
                        return true;
                    }
                }catch(e){}
                return false;
            }
            function renderGrid(){
                gridEl.innerHTML = '';
                for(let row=0; row<5; row++){
                    for(let col=0; col<5; col++){
                        const cell = document.createElement('div');
                        cell.className = 'cell aspect-square flex items-center justify-center text-xs ' + (state.x===col && state.y===row ? 'active' : '');
                        gridEl.appendChild(cell);
                    }
                }
                // measure and place avatar after cells rendered
                requestAnimationFrame(() => {
                    measureCells();
                    moveAvatar();
                });
            }

            async function loadRandomLevel(){
                statusEl.textContent = 'Memuat level...';
                const savedIdRaw = localStorage.getItem(LS_LEVEL_ID);
                let lv = null;
                try{
                    if(savedIdRaw){
                        const id = parseInt(savedIdRaw,10);
                        const res = await fetch(`/levels/${id}`);
                        if(res.ok){ lv = await res.json(); }
                    }
                    if(!lv){
                        const res = await fetch('/levels/random');
                        if(!res.ok){ throw new Error('random-failed'); }
                        lv = await res.json();
                        if(lv && lv.id) localStorage.setItem(LS_LEVEL_ID, String(lv.id));
                    }
                }catch(e){
                    statusEl.textContent = 'Level tidak tersedia.';
                    return;
                }
                state.levelId = lv.id;
                state.difficulty = lv.difficulty || null;
                codeEl.value = lv.code || '';
                // Hapus posisi tersimpan bila berganti level
                const lastLevelId = parseInt(localStorage.getItem(LS_LEVEL_ID) || '0', 10);
                if (lastLevelId !== Number(state.levelId)) {
                    localStorage.removeItem(LS_POS);
                }
                // Pulihkan posisi jika level sama, jika tidak gunakan start_at dari server
                if(!loadPosIfSame()) {
                    const s = (lv.start_at || {});
                    const sx = Number(s.x);
                    const sy = Number(s.y);
                    state.x = Number.isFinite(sx) ? Math.max(0, Math.min(4, sx)) : 0;
                    state.y = Number.isFinite(sy) ? Math.max(0, Math.min(4, sy)) : 0;
                }
                renderGrid(); moveAvatar(); statusEl.textContent='';
                // Update code highlight view
                const cv = document.getElementById('codeView');
                if (cv) { cv.textContent = codeEl.value || ''; if (window.Prism) Prism.highlightElement(cv); }
            }

            async function checkAnswer(){
                if(!state.levelId){ return; }
                statusEl.textContent = 'Memeriksa...';
                try{
                    const url = `/levels/${state.levelId}/check?x=${state.x}&y=${state.y}`;
                    const res = await fetch(url);
                    const data = await res.json();
                    // Hide inline status text; we only use overlay for feedback
                    statusEl.textContent = '';
                    statusEl.classList.add('hidden');

                    // Record gameplay result
                    try {
                        await fetch('/game-plays', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                            },
                            body: JSON.stringify({ level_id: state.levelId, success: !!data.success, time_seconds: elapsedSeconds() }),
                        });
                    } catch (e) { /* ignore post error for UX */ }

                    // Show overlay result then redirect
                    // Compute client-side score for display (server will compute authoritative score on save)
                    const ok = !!data.success;
                    const elapsed = elapsedSeconds();
                    let base = 0; if (ok) { base = (String(state.difficulty || '').toLowerCase()==='hard') ? 120 : 100; }
                    const showScore = Math.max(0, +(base - (elapsed/60)).toFixed(2));

                    // Build overlay content
                    const isWin = ok;
                    const title = isWin ? 'Selamat! 🎉' : 'Coba Lagi! 😞';
                    const imgSrc = isWin ? '/images/icons/congratulation.png' : '/images/icons/failed.png';
                    const sub = isWin ? `Skor kamu: <span class="font-extrabold text-indigo-200">${showScore}</span>` : `Skor kamu: <span class="font-extrabold text-indigo-200">0</span>`;

                    overlay.innerHTML = `
                        <div class="overlay-card rounded-2xl px-12 py-10 overlay-show text-center">
                            <div class="inline-flex items-center justify-center w-48 h-48 mx-auto rounded-full bg-white/5 ring-1 ring-white/10 mb-3">
                                <img src="${imgSrc}" alt="${isWin?'Selamat':'Gagal'}" class="result-img" />
                            </div>
                            ${isWin ? `<div class=\"stars\">${'<svg class=\"star w-5 h-5\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"currentColor\" viewBox=\"0 0 20 20\"><path d=\"M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z\"/></svg>'.repeat(3)}</div>` : `<div class=\"stars\">${'<svg class=\"cross w-5 h-5\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 24 24\" fill=\"currentColor\"><path d=\"M6.225 4.811a1 1 0 011.414 0L12 9.172l4.361-4.361a1 1 0 111.415 1.414L13.415 10.586l4.36 4.361a1 1 0 01-1.414 1.415L12 12.001l-4.361 4.361a1 1 0 01-1.414-1.415l4.36-4.36-4.36-4.36a1 1 0 010-1.415z\"/></svg>'.repeat(3)}</div>`}
                            <p class="text-2xl font-extrabold text-indigo-100">${title}</p>
                            <p class="mt-1 text-sm text-slate-300">${sub}</p>
                            <div class="mt-5">
                                <a href="/" class="btn-ll inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700">Kembali ke Beranda</a>
                            </div>
                        </div>`;

                    overlay.classList.remove('hidden');
                    stopTimer();
                    localStorage.removeItem(LS_KEY);
                    localStorage.removeItem(LS_LEVEL_ID);
                    localStorage.removeItem(LS_POS);
                }catch(e){
                    // Also keep hidden on errors to avoid clutter
                    statusEl.textContent = '';
                    statusEl.classList.add('hidden');
                }
            }

            upBtn.addEventListener('click', () => { state.y = wrapIdx(state.y - 1); renderGrid(); moveAvatar(); savePos(); });
            downBtn.addEventListener('click', () => { state.y = wrapIdx(state.y + 1); renderGrid(); moveAvatar(); savePos(); });
            leftBtn.addEventListener('click', () => { state.x = wrapIdx(state.x - 1); renderGrid(); moveAvatar(); savePos(); });
            rightBtn.addEventListener('click', () => { state.x = wrapIdx(state.x + 1); renderGrid(); moveAvatar(); savePos(); });
            window.addEventListener('resize', () => { measureCells(); moveAvatar(); });

            // Axis visibility toggle
            const axisToggle = document.getElementById('axisToggle');
            function applyAxisVisibility(){
                const show = localStorage.getItem(LS_AXIS) !== '0';
                if (axisToggle) axisToggle.checked = show;
                xAxis.classList.toggle('axis-hidden', !show);
                yAxis.classList.toggle('axis-hidden', !show);
            }
            if (axisToggle){
                axisToggle.addEventListener('change', () => {
                    const show = axisToggle.checked; localStorage.setItem(LS_AXIS, show ? '1' : '0'); applyAxisVisibility();
                });
            }
            finishBtn.addEventListener('click', checkAnswer);
            if (restartBtn) {
                restartBtn.addEventListener('click', () => {
                    // clear stored level & timer, then force new random level
                    localStorage.removeItem(LS_LEVEL_ID);
                    localStorage.removeItem(LS_KEY);
                    localStorage.removeItem(LS_POS);
                    window.location.reload();
                });
            }

            loadRandomLevel();
            startTimer();

            // keyboard controls (WASD + Arrow keys). Ignore when typing in inputs/textarea
            document.addEventListener('keydown', (e) => {
                const tag = (document.activeElement && document.activeElement.tagName || '').toLowerCase();
                const isTyping = tag === 'textarea' || tag === 'input';
                if (isTyping) return;
                let handled = true;
                switch (e.key) {
                    case 'ArrowUp': case 'w': case 'W':
                        state.y = wrapIdx(state.y - 1); renderGrid(); moveAvatar(); savePos(); break;
                    case 'ArrowDown': case 's': case 'S':
                        state.y = wrapIdx(state.y + 1); renderGrid(); moveAvatar(); savePos(); break;
                    case 'ArrowLeft': case 'a': case 'A':
                        state.x = wrapIdx(state.x - 1); renderGrid(); moveAvatar(); savePos(); break;
                    case 'ArrowRight': case 'd': case 'D':
                        state.x = wrapIdx(state.x + 1); renderGrid(); moveAvatar(); savePos(); break;
                    case 'Enter':
                        finishBtn.click(); break;
                    default:
                        handled = false;
                }
                if (handled) { e.preventDefault(); }
            });
        });
    </script>
</head>
    <body class="min-h-screen poppins bg-slate-950 bg-ll text-slate-100">
    <main class="max-w-6xl mx-auto px-6 lg:px-8 py-10">
        <div class="mb-8 flex items-center justify-between">
            <h1 class="text-2xl md:text-3xl font-extrabold text-blue-300">Logic Labyrinth</h1>
            <div class="mx-4 flex-1 flex justify-center">
                <div class="timer-card inline-flex items-center gap-2 px-4 py-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3M12 22a10 10 0 110-20 10 10 0 010 20z"/></svg>
                    <span id="ll-timer">00:00</span>
                </div>
            </div>
            <a href="/" class="btn-ll px-4 py-1.5 rounded-lg bg-white/10 text-blue-200 font-semibold ring-1 ring-white/20 hover:bg-white/15">Kembali ke Beranda</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
            <!-- Grid & status -->
            <div class="rounded-3xl border border-slate-700 bg-slate-900/70 p-6 md:p-8 shadow-[0_8px_24px_rgba(0,0,0,0.5)]">
                <div id="gridWrap">
                    <div id="grid" class="grid-5x5"></div>
                    <!-- Subtle axis labels -->
                    <div id="xAxis"></div>
                    <div id="yAxis"></div>
                    <div id="avatar"><img src="/images/icons/play_icon.png" alt="Player Icon"/></div>
                </div>
                <p id="status" class="mt-3 text-sm text-slate-300 hidden"></p>

                <!-- Controls moved under the grid -->
                <div class="mt-6 flex items-center justify-between gap-4">
                    <div class="flex flex-col items-center gap-3">
                        <div>
                            <button id="upBtn" class="btn-ll px-6 py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 text-lg">↑</button>
                        </div>
                        <div class="flex items-center gap-3">
                            <button id="leftBtn" class="btn-ll px-6 py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 text-lg">←</button>
                            <button id="downBtn" class="btn-ll px-6 py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 text-lg">↓</button>
                            <button id="rightBtn" class="btn-ll px-6 py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 text-lg">→</button>
                        </div>
                    </div>
                    <div>
                        <button id="finishBtn" class="btn-ll px-7 py-3 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 text-lg">Selesai</button>
                    </div>
                </div>
            </div>

            <!-- Editor -->
            <div class="code-card p-0 overflow-hidden">
                <div class="px-6 pt-4 pb-6 flex items-center justify-between">
                    <label class="block text-xl font-extrabold code-label">Kode Perintah</label>
                    <button id="restartBtn" class="btn-ll w-9 h-9 rounded-full bg-rose-600 text-white hover:bg-rose-700 flex items-center justify-center" aria-label="Mulai Ulang" title="Mulai Ulang">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v6h6M20 20v-6h-6"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 9a7 7 0 00-12-4M4 15a7 7 0 0012 4"/>
                        </svg>
                    </button>
                </div>
                <div class="px-6 pb-6">
                    <!-- Highlighted view -->
                    <pre class="rounded-xl border border-indigo-400/30 bg-slate-950/80 p-4 overflow-auto"><code id="codeView" class="language-python"></code></pre>
                    <!-- Hidden textarea as data source / fallback -->
                    <textarea id="code" rows="14" class="hidden code-area w-full rounded-xl border border-indigo-400/30 bg-slate-950/80 text-indigo-100 px-4 py-3"></textarea>
                </div>
            </div>
        </div>
        
    </main>

    <!-- Result Overlay -->
    <div id="resultOverlay" class="hidden fixed inset-0 bg-black/60 backdrop-blur-[2px] flex items-center justify-center z-50">
        <div class="overlay-card rounded-2xl px-10 py-7">
            <p id="resultText" class="text-2xl font-extrabold text-center text-blue-900"></p>
            <p class="text-center mt-2 text-sm text-gray-600">Mengalihkan ke beranda...</p>
        </div>
    </div>
    </body>
    </html>

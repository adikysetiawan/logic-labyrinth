<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Logic Labyrinth') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .navbar-poppins {
            font-family: 'Poppins', Arial, Helvetica, sans-serif;
        }
        /* Button base + sheen effect */
        .btn-ll { position: relative; overflow: hidden; transition-property: background, box-shadow, transform; transition-duration: 300ms; transition-timing-function: cubic-bezier(.4,0,.2,1); box-shadow: 0 2px 12px 0 rgba(0,0,0,0.07); }
        .btn-ll:hover { filter: brightness(1.05); transform: translateY(-1px); box-shadow: 0 8px 26px 0 rgba(0,0,0,0.12); }
        /* sheen */
        .btn-ll::after { content: ""; position: absolute; top: -40%; left: -30%; width: 40%; height: 180%; background: linear-gradient(120deg, rgba(255,255,255,0) 0%, rgba(255,255,255,.35) 50%, rgba(255,255,255,0) 100%); transform: translateX(-120%) rotate(12deg); transition: transform .5s ease; }
        .btn-ll:hover::after { transform: translateX(260%) rotate(12deg); }
        /* subtle 3D ground shadow */
        .btn-ll::before { content:""; position:absolute; left:50%; transform:translateX(-50%); bottom:-8px; width:72%; height:12px; background: radial-gradient(closest-side, rgba(0,0,0,0.18), rgba(0,0,0,0)); filter: blur(6px); transition: all .3s ease; border-radius:999px; }
        .btn-ll:hover::before { bottom:-10px; width:78%; filter: blur(8px); opacity:.95; }
        /* Navbar link underline animation */
        .nav-link{ position:relative; }
        .nav-link::after{ content:""; position:absolute; left:0; bottom:-6px; height:2px; width:0; background: linear-gradient(90deg, #2563eb, #f97316); transition: width .3s ease; }
        .nav-link:hover::after{ width:100%; }
        .poppins { font-family: 'Poppins', Arial, Helvetica, sans-serif; }
        /* Non-intrusive background decorations via fixed pseudo-element */
        body.bg-ll::before{
            content:""; position:fixed; inset:0; z-index:-1; pointer-events:none;
            background-image:
                radial-gradient(closest-side, rgba(37,99,235,0.18), rgba(37,99,235,0) 60%),
                radial-gradient(closest-side, rgba(249,115,22,0.16), rgba(249,115,22,0) 60%),
                radial-gradient(circle at 1px 1px, rgba(37,99,235,0.07) 1px, transparent 1px);
            background-repeat: no-repeat, no-repeat, repeat;
            background-size: 38rem 38rem, 42rem 42rem, 24px 24px;
            background-position: -12rem -12rem, calc(100% + 8rem) calc(100% + 6rem), 0 0;
        }
        /* Fixed navbar states */
        .nav-fixed { position: fixed; top: 0; left: 0; right: 0; z-index: 50; }
        .nav-shell { 
            transition: background-color .35s ease, box-shadow .35s ease, border-color .35s ease, transform .35s ease, padding .35s ease;
            padding: .5rem 18px; /* default */
            background-color: transparent;
        }
        .nav-shell.scrolled { 
            background-color: #ffffff; 
            box-shadow: 0 10px 26px rgba(0,0,0,0.07); 
            border-color: rgba(229,231,235,.9); 
            padding: .75rem 26px; /* expand subtly */
            transform: scale(1.01);
        }
        /* Celebration visuals (trumpet + confetti) */
        .celebrate-layer{ position:absolute; inset:0; pointer-events:none; overflow:visible; opacity:0; transition:opacity .25s ease; }
        .celebrate-layer.show{ opacity:1; }
        .celebrate-layer .trumpet{ position:absolute; top:22%; font-size:30px; filter: drop-shadow(0 6px 10px rgba(0,0,0,.15)); opacity:.95; }
        .celebrate-layer .trumpet.left{ left:-14px; transform: translateX(-22px) rotate(-18deg); animation: trumpet-in-left .7s ease-out both; }
        .celebrate-layer .trumpet.right{ right:-14px; transform: translateX(22px) rotate(18deg) scaleX(-1); animation: trumpet-in-right .7s ease-out both; }
        @keyframes trumpet-in-left{ 0%{ transform: translateX(-50px) rotate(-28deg); opacity:0; } 100%{ transform: translateX(-22px) rotate(-18deg); opacity:.95; } }
        @keyframes trumpet-in-right{ 0%{ transform: translateX(50px) rotate(28deg) scaleX(-1); opacity:0; } 100%{ transform: translateX(22px) rotate(18deg) scaleX(-1); opacity:.95; } }
        .celebrate-layer .confetti{ position:absolute; width:7px; height:10px; border-radius:2px; opacity:0; }
        @keyframes confetti-fall{
            0%{ transform: translate3d(var(--x,0), -20px, 0) rotate(0deg); opacity:0; }
            10%{ opacity:1; }
            100%{ transform: translate3d(var(--x,0), 120%, 0) rotate(720deg); opacity:0; }
        }
        /* Floating tooltip (di luar card) */
        .ll-tt-floating{ position: fixed; display:none; white-space: nowrap; padding:.55rem .8rem; border-radius:.6rem; background: rgba(17,24,39,.96); color:#fff; font-size:.85rem; box-shadow:0 12px 28px rgba(0,0,0,.22); z-index: 60; pointer-events:none; }
        .ll-tt-floating .muted{ color: rgba(255,255,255,.82); }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-orange-50 flex flex-col relative overflow-x-hidden bg-ll">
    <!-- Navbar -->
    <nav id="topNav" class="navbar-poppins nav-fixed w-full flex justify-center py-3">
    <div class="max-w-6xl w-full px-6">
        <div id="navShell" class="nav-shell flex justify-between items-center w-full border border-gray-200/80 rounded-xl bg-transparent">

        <div class="flex items-center gap-3">
    <img src="/images/logos/logic labyrinth_logo.png" alt="Logic Labyrinth Logo" class="h-9 w-9 object-contain bg-transparent shadow-none" style="margin-bottom:2px;">
    <span class="text-2xl font-bold text-blue-700 tracking-wide">Logic Labyrinth</span>
</div>
        <div class="space-x-6">
            <a href="#home" class="nav-link text-blue-700 font-semibold hover:text-orange-500 transition">Home</a>
            <a href="#leaderboard" class="nav-link text-blue-700 font-semibold hover:text-orange-500 transition">Leaderboard</a>
            <a href="#about" class="nav-link text-blue-700 font-semibold hover:text-orange-500 transition">About</a>
        </div>
        <a href="{{ route('login') }}" class="btn-ll px-5 py-2 rounded-lg bg-orange-500 text-white font-semibold shadow hover:bg-orange-600 hover:scale-105 hover:shadow-2xl transition-all duration-300 ease-out">Login</a>
    </div>
    </div>
    </nav>
    <!-- spacer to prevent layout shift when navbar is fixed -->
    <div aria-hidden="true" class="h-[64px]"></div>
    
    <!-- Hero Section -->
    <section id="home" class="flex flex-col md:flex-row items-center justify-between max-w-6xl mx-auto px-8 py-10 mt-14 mb-16 flex-1 w-full gap-10">
        <div class="flex-1">
            <h1 class="text-4xl md:text-5xl font-extrabold text-blue-800 leading-tight mb-6">
                Uji <span class="text-orange-500">Logika</span> & <span class="text-orange-500">Skill Coding</span> Kamu!<br>
                <span class="text-blue-700">Pecahkan Puzzle Seru di Logic Labyrinth</span>
            </h1>
            <p class="text-lg text-gray-700 mb-8">Mainkan berbagai level, naikkan peringkatmu, dan jadilah juara di leaderboard mingguan!</p>
            <div class="flex gap-4">
                <a href="{{ route('play') }}" class="btn-ll px-6 py-3 rounded-lg bg-blue-700 text-white font-bold shadow-lg hover:bg-blue-800 hover:scale-105 hover:shadow-2xl transition-all duration-300 ease-out">Mulai Bermain</a>
            </div>
        </div>
        <div class="flex-1 flex justify-center">
            <img src="/images/icons/avatar_macan.png" alt="Avatar Macan" class="w-[23rem] h-[23rem] md:w-[25.5rem] md:h-[25.5rem] object-contain bg-transparent shadow-none" />
        </div>
    </section>

    
    
    
    
    
    

    
    <!-- Tutorial Section -->
    <section class="section-tutorial max-w-6xl mx-auto px-8 pb-14 mb-6 w-full">
        <h2 class="text-2xl md:text-3xl font-extrabold text-blue-800 mb-8">Cara Main Logic Labyrinth</h2>
        <!-- Steps grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 gap-y-8">
            <!-- Step 1 -->
            <div class="group rounded-2xl border border-gray-200 bg-gradient-to-b from-white/80 to-white/40 p-6 shadow-[0_4px_16px_rgba(0,0,0,0.06)] transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_14px_28px_rgba(37,99,235,0.12)] hover:border-blue-200">
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-semibold">1</span>
                    <span class="text-lg poppins font-bold text-blue-700">Klik "Mulai Bermain"</span>
                </div>
                <img src="/images/icons/step%201_icon.png" alt="Step 1" class="mx-auto mt-3 mb-3 w-16 md:w-20 lg:w-24 object-contain bg-transparent shadow-none" />
                <p class="text-gray-700">Mulai petualanganmu dengan menekan tombol. Kamu akan diarahkan ke proses setup awal.</p>
            </div>
            <!-- Step 2 -->
            <div class="group rounded-2xl border border-gray-200 bg-gradient-to-b from-white/80 to-white/40 p-6 shadow-[0_4px_16px_rgba(0,0,0,0.06)] transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_14px_28px_rgba(249,115,22,0.12)] hover:border-orange-200">
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-500 text-white text-xs font-semibold">2</span>
                    <span class="text-lg poppins font-bold text-orange-500">Isi Data Diri Singkat</span>
                </div>
                <img src="/images/icons/step%202_icon.png" alt="Step 2" class="mx-auto mt-3 mb-3 w-16 md:w-20 lg:w-24 object-contain bg-transparent shadow-none" />
                <p class="text-gray-700">Lengkapi profil dasar untuk menyimpan progres dan leaderboard. Proses cepat & sederhana.</p>
            </div>
            <!-- Step 3 -->
            <div class="group rounded-2xl border border-gray-200 bg-gradient-to-b from-white/80 to-white/40 p-6 shadow-[0_4px_16px_rgba(0,0,0,0.06)] transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_14px_28px_rgba(37,99,235,0.12)] hover:border-blue-200">
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-semibold">3</span>
                    <span class="text-lg poppins font-bold text-blue-700">Pilih Tingkat Kesulitan</span>
                </div>
                <img src="/images/icons/step%203_icon.png" alt="Step 3" class="mx-auto mt-3 mb-3 w-16 md:w-20 lg:w-24 object-contain bg-transparent shadow-none" />
                <p class="text-gray-700">Mulai dari level mudah hingga expert. Setiap tingkat punya aturan dan tantangan berbeda.</p>
            </div>
            <!-- Step 4 -->
            <div class="group rounded-2xl border border-gray-200 bg-gradient-to-b from-white/80 to-white/40 p-6 shadow-[0_4px_16px_rgba(0,0,0,0.06)] transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_14px_28px_rgba(249,115,22,0.12)] hover:border-orange-200">
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-500 text-white text-xs font-semibold">4</span>
                    <span class="text-lg poppins font-bold text-orange-500">Baca Aturan & Target</span>
                </div>
                <img src="/images/icons/step%204_icon.png" alt="Step 4" class="mx-auto mt-3 mb-3 w-16 md:w-20 lg:w-24 object-contain bg-transparent shadow-none" />
                <p class="text-gray-700">Perhatikan instruksi pada tiap level, pahami input-output, lalu rancang solusimu.</p>
            </div>
            <!-- Step 5 -->
            <div class="group rounded-2xl border border-gray-200 bg-gradient-to-b from-white/80 to-white/40 p-6 shadow-[0_4px_16px_rgba(0,0,0,0.06)] transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_14px_28px_rgba(37,99,235,0.12)] hover:border-blue-200">
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-semibold">5</span>
                    <span class="text-lg poppins font-bold text-blue-700">Jalankan & Raih Skor</span>
                </div>
                <img src="/images/icons/step%205_icon.png" alt="Step 5" class="mx-auto mt-3 mb-3 w-16 md:w-20 lg:w-24 object-contain bg-transparent shadow-none" />
                <p class="text-gray-700">Uji solusi, lihat skor dan waktu, lalu perbaiki untuk memanjat leaderboard.</p>
            </div>
        </div>
    </section>

    <!-- Leaderboard Section -->
    <section id="leaderboard" class="section-leaderboard max-w-6xl mx-auto px-8 pb-20 w-full">
        <div class="mb-14 text-center">
            <div class="text-2xl tracking-[0.22em] font-extrabold text-lime-500 uppercase">Leaderboard</div>
            <h2 class="mt-2 text-[1.9rem] md:text-5xl font-extrabold leading-tight tracking-tight text-blue-900">
                Peringkat Teratas <span class="text-blue-900">Minggu Ini</span>
            </h2>
        </div>
        <div id="leaderboard-dynamic">
            <!-- Placeholder awal (akan terganti oleh data API) -->
            <div class="text-center text-gray-500 py-10">Memuat leaderboard...</div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="max-w-6xl mx-auto px-8 pb-8 w-full">
        <div class="mb-6">
            <div class="text-2xl tracking-[0.22em] font-extrabold text-lime-500 uppercase">About Logic Labyrinth</div>
            <h2 class="mt-2 text-[1.9rem] md:text-5xl font-extrabold leading-tight tracking-tight text-blue-900">
                Logic Labyrinth adalah permainan puzzle logika untuk mengasah <span class="text-gray-400">analisis dan problem solving</span> kamu.
            </h2>
        </div>
        <p class="text-gray-700 max-w-3xl mb-8">
            Jelajahi level dari mudah sampai expert, selesaikan tantangan dengan strategi dan kode yang tepat, lalu panjatkan namamu pada leaderboard. Nikmati interaksi mikro yang halus, UI modern, dan pengalaman bermain yang fokus pada kepuasan menyelesaikan puzzle.
        </p>
        <div class="mt-8 grid grid-cols-1 md:[grid-template-columns:auto_1fr] gap-6 items-start justify-start justify-items-start w-full">
            <!-- Left: gradient border hanya untuk label Creator + foto -->
            <div class="rounded-2xl p-[1.6px] bg-gradient-to-r from-blue-500/60 via-cyan-400/60 to-emerald-400/60 inline-block">
                <div class="rounded-2xl bg-white/80 p-0 inline-block">
                    <div class="ml-6 mt-5 w-36 md:w-40 text-sm uppercase tracking-wider text-blue-700 font-semibold">Creator</div>
                    <img src="/images/assets/creator.png" alt="Foto Creator" class="mt-3 mb-5 ml-6 mr-6 w-36 h-36 md:w-40 md:h-40 object-cover rounded-xl shadow-lg ring-1 ring-black/5 bg-white" />
                </div>
            </div>
            <!-- Right: biodata polos -->
            <div>
                <div class="text-xl md:text-2xl font-extrabold text-blue-900 mb-2">Achmad Diky Setiawan</div>
                <div class="text-gray-700 mb-1"><span class="font-semibold">Universitas:</span> Universitas Negeri Surabaya</div>
                <div class="text-gray-700 mb-4"><span class="font-semibold">Prodi:</span> Manajemen Informatika</div>
                <div class="flex flex-wrap gap-3">
                    <a href="https://www.instagram.com/dkystwnn._/" class="btn-ll px-3 py-1.5 rounded-lg bg-blue-700 text-white text-sm font-semibold hover:bg-blue-800 inline-flex items-center gap-2" target="_blank" rel="noopener">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M7.5 2h9A5.5 5.5 0 0 1 22 7.5v9A5.5 5.5 0 0 1 16.5 22h-9A5.5 5.5 0 0 1 2 16.5v-9A5.5 5.5 0 0 1 7.5 2zm0 2A3.5 3.5 0 0 0 4 7.5v9A3.5 3.5 0 0 0 7.5 20h9a3.5 3.5 0 0 0 3.5-3.5v-9A3.5 3.5 0 0 0 16.5 4h-9zm9.25 1.75a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5zM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10zm0 2a3 3 0 1 0 .001 6.001A3 3 0 0 0 12 9z"/></svg>
                        Instagram
                    </a>
                    <a href="https://www.linkedin.com/in/achmaddikysetiawan/" class="btn-ll px-3 py-1.5 rounded-lg bg-blue-700 text-white text-sm font-semibold hover:bg-blue-800 inline-flex items-center gap-2" target="_blank" rel="noopener">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M4.983 3.5C4.983 4.88 3.88 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1s2.483 1.12 2.483 2.5zM.5 8h4V23h-4V8zM8 8h3.842v2.041h.055c.535-1.013 1.84-2.082 3.79-2.082C20.02 7.959 21 10.57 21 14.084V23h-4v-7.84c0-1.871-.033-4.279-2.607-4.279-2.61 0-3.01 2.037-3.01 4.144V23H7.999L8 8z"/></svg>
                        LinkedIn
                    </a>
                    <a href="https://www.facebook.com/achmaddikysetiawan/" class="btn-ll px-3 py-1.5 rounded-lg bg-blue-700 text-white text-sm font-semibold hover:bg-blue-800 inline-flex items-center gap-2" target="_blank" rel="noopener">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M22 12.07C22 6.48 17.52 2 11.93 2S2 6.48 2 12.07C2 17.1 5.66 21.22 10.44 22v-7.03H7.9v-2.9h2.54V9.41c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.23.2 2.23.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56v1.87h2.78l-.44 2.9h-2.34V22C18.34 21.22 22 17.1 22 12.07z"/></svg>
                        Facebook
                    </a>
                    <a href="https://github.com/adikysetiawan" class="btn-ll px-3 py-1.5 rounded-lg bg-blue-700 text-white text-sm font-semibold hover:bg-blue-800 inline-flex items-center gap-2" target="_blank" rel="noopener">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M12 2C6.477 2 2 6.477 2 12c0 4.418 2.865 8.166 6.839 9.489.5.092.682-.217.682-.483 0-.237-.009-.868-.013-1.704-2.782.604-3.369-1.34-3.369-1.34-.454-1.154-1.11-1.462-1.11-1.462-.907-.62.069-.607.069-.607 1.002.07 1.53 1.029 1.53 1.029.892 1.529 2.341 1.087 2.91.832.091-.647.35-1.087.636-1.338-2.22-.253-4.555-1.112-4.555-4.944 0-1.091.39-1.984 1.029-2.682-.103-.253-.446-1.272.098-2.65 0 0 .84-.269 2.75 1.025A9.563 9.563 0 0 1 12 6.844c.85.004 1.705.115 2.504.337 1.909-1.294 2.748-1.025 2.748-1.025.546 1.378.202 2.397.1 2.65.64.698 1.028 1.59 1.028 2.682 0 3.841-2.338 4.688-4.566 4.937.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .268.18.58.688.481A10.004 10.004 0 0 0 22 12c0-5.523-4.477-10-10-10z" clip-rule="evenodd"/></svg>
                        GitHub
                    </a>
                </div>
            </div>
        </div>
    </section>

            

        <!-- Footer -->
        <footer class="mt-16 bg-gradient-to-t from-gray-900 to-gray-800 text-gray-300">
            <div class="max-w-6xl mx-auto px-8 py-8">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-3">
                            <img src="/images/logos/logic labyrinth_logo.png" alt="Logic Labyrinth Logo" class="h-8 w-8 object-contain" />
                            <span class="text-lg font-extrabold tracking-wide">Logic Labyrinth</span>
                        </div>
                        <div class="text-sm text-gray-400 mt-2">Puzzle logika untuk mengasah analisis dan problem solving kamu.</div>
                    </div>
                    <div class="text-sm text-gray-400">
                        &copy; <span id="ll-year"></span> Logic Labyrinth. All rights reserved.
                    </div>
                </div>
            </div>
        </footer>

        <script>
            // navbar scroll behavior (apply to inner shell only)
            (function(){
                const shell = document.getElementById('navShell');
                let last = window.scrollY;
                const onScroll = () => {
                    if(window.scrollY > 6){ shell.classList.add('scrolled'); } else { shell.classList.remove('scrolled'); }
                };
                window.addEventListener('scroll', onScroll, { passive: true });
                onScroll();
            })();

            // micro parallax for Top 3 cards
            function initParallax(section){
                const limit = 6; // px max translate (subtle)
                section.querySelectorAll('.card-parallax').forEach(card => {
                    const items = card.querySelectorAll('.parallax-el');
                    const onMove = (e) => {
                        const rect = card.getBoundingClientRect();
                        const x = (e.clientX - rect.left) / rect.width - 0.5; // -0.5..0.5
                        const y = (e.clientY - rect.top) / rect.height - 0.5;
                        card.style.transform = `translateY(-2px) rotateX(${(-y*2)}deg) rotateY(${(x*2)}deg)`;
                        items.forEach((el, idx) => {
                            const depth = (idx+1)/items.length; // 0..1
                            const tx = x * limit * depth;
                            const ty = y * limit * depth;
                            el.style.transform = `translate3d(${tx}px, ${ty}px, 0)`;
                        });
                    };
                    const onLeave = () => {
                        card.style.transform = '';
                        items.forEach(el => { el.style.transform = ''; });
                    };
                    card.addEventListener('mousemove', onMove);
                    card.addEventListener('mouseleave', onLeave);
                });
            }

            // Floating tooltip engine
            function initTooltipEngine(){
                let tip = document.getElementById('llTip');
                if(!tip){
                    tip = document.createElement('div');
                    tip.id = 'llTip';
                    tip.className = 'll-tt-floating';
                    document.body.appendChild(tip);
                }
                const show = (html, rect) => {
                    tip.innerHTML = html;
                    tip.style.display = 'block';
                    const padding = 8;
                    const x = Math.round(rect.left + rect.width/2);
                    const y = Math.round(rect.top - padding);
                    tip.style.left = `${x}px`;
                    tip.style.top = `${y}px`;
                    tip.style.transform = 'translate(-50%, -100%)';
                };
                const hide = () => { tip.style.display = 'none'; };

                // Delegate events
                document.addEventListener('mouseover', (e) => {
                    const el = e.target.closest('.ll-tt-trigger');
                    if(!el) return;
                    const data = el.dataset;
                    const html = `
                        <div><strong>${data.fullname || ''}</strong></div>
                        <div class="muted">Univ: ${data.university || '-'} | Prodi: ${data.program || '-'}</div>
                        <div class="muted">Skor: ${data.score || '0'} | Waktu: ${data.seconds || '0'}s | Level: ${data.difficulty || '-'}</div>
                    `;
                    show(html, el.getBoundingClientRect());
                }, { passive: true });
                document.addEventListener('mouseout', (e) => {
                    if(e.target.closest('.ll-tt-trigger')) hide();
                }, { passive: true });
            }

            // Celebration trigger: click any Top-3 card
            function initCelebrate(section){
                const colors = ['#f43f5e','#f59e0b','#10b981','#3b82f6','#8b5cf6'];
                const playOn = (card) => {
                    const layer = card.querySelector('.celebrate-layer');
                    if(!layer) return;
                    // clear previous confetti
                    layer.innerHTML = '<div class="trumpet left">🎺</div><div class="trumpet right">🎺</div>';
                    for(let i=0;i<40;i++){
                        const piece = document.createElement('div');
                        piece.className = 'confetti';
                        piece.style.background = colors[Math.floor(Math.random()*colors.length)];
                        piece.style.left = (45 + Math.random()*10) + '%';
                        piece.style.top = (35 + Math.random()*10) + '%';
                        piece.style.setProperty('--x', (Math.random()*160 - 80) + 'px');
                        piece.style.animation = `confetti-fall ${1.1 + Math.random()*0.9}s ease-out ${Math.random()*0.2}s forwards`;
                        layer.appendChild(piece);
                    }
                    layer.classList.add('show');
                    setTimeout(()=>{ layer.classList.remove('show'); }, 2000);
                };
                section.querySelectorAll('.card-parallax').forEach(card => {
                    card.addEventListener('click', ()=> playOn(card));
                });
            }

            // Fetch leaderboard mingguan dan render dinamis
            (function(){
                const section = document.getElementById('leaderboard');
                const mount = document.getElementById('leaderboard-dynamic');
                if(!section || !mount) return;

                const icons = [ '/images/icons/winner%202.png', '/images/icons/winner%201.png', '/images/icons/winner%203.png' ];
                const fmtScore = (n) => {
                    if (Number.isInteger(n)) return n.toString();
                    return (Math.round(n * 100) / 100).toFixed(2);
                };
                // Show up to first 8 characters of full name; append '...' if overflow
                const shortName = (name) => {
                    const s = (name || '').trim();
                    if (s.length <= 8) return s || 'Pemain';
                    return (s.slice(0, 8).trimEnd()) + '...';
                };

                fetch('/leaderboard/top-week?limit=10', { headers: { 'Accept': 'application/json' }})
                    .then(r => r.json())
                    .then(payload => {
                        const rows = Array.isArray(payload.data) ? payload.data : [];
                        if (rows.length === 0) {
                            mount.innerHTML = '<div class="text-center text-gray-500 py-10">Belum ada data minggu ini.</div>';
                            return;
                        }

                        const top = rows.slice(0, 3);
                        // Susun urutan kartu: kiri=rank2 (index 1), tengah=rank1 (index 0), kanan=rank3 (index 2)
                        const orderedTop = [ top[1] || top[0], top[0], top[2] || top[0] ].filter(Boolean);
                        const cardWidths = ['w-[200px]', 'w-[200px]', 'w-[200px]'];
                        const cardHeights = ['min-h-[310px]', 'min-h-[360px]', 'min-h-[270px]'];
                        const cardClasses = ['card-rank-2', 'card-rank-1', 'card-rank-3'];

                        const topCards = orderedTop.map((p, i) => {
                            const padding = (i===1) ? 'pt-5 pb-5 px-0' : 'p-5';
                            const imgClass = (i===1) ? 'w-full h-auto' : (i===0 ? 'w-full h-auto' : 'w-36 h-36');
                            const rankNum = (i===0?2:(i===1?1:3));
                            const rankColor = rankNum===1 ? 'bg-green-600' : (rankNum===2 ? 'bg-amber-400' : 'bg-rose-500');
                            return `
                                <div class="relative flex flex-col items-center">
                                    <div class="absolute -top-6 z-10">
                                        <span class="px-3.5 py-1.5 rounded-full text-white text-base font-extrabold ${rankColor} ring-2 ring-white/70 shadow-[0_10px_20px_rgba(0,0,0,0.18)] drop-shadow-md">#${rankNum}</span>
                                    </div>
                                    <div class="card-frame inline-block rounded-3xl border border-gray-200/70 bg-white/40 p-2 outline outline-1 outline-gray-300/50 shadow-[0_8px_24px_rgba(0,0,0,0.04)] ${cardClasses[i]}">
                                        <div class="rounded-2xl p-[1.6px] bg-gradient-to-b from-indigo-400/70 via-fuchsia-400/60 to-amber-300/70 shadow-[0_10px_30px_rgba(79,70,229,0.18)]">
                                            <div class="card-parallax relative rounded-2xl bg-gradient-to-b from-white/95 to-white/70 ${padding} overflow-hidden transition-transform duration-300 will-change-transform flex flex-col justify-end items-center text-center ${cardHeights[i]} ${cardWidths[i]} ${cardClasses[i]}">
                                                <img src="${icons[i]}" alt="Icon Juara" class="parallax-el object-contain mb-4 select-none ${imgClass}" draggable="false" />
                                                <div class="celebrate-layer">
                                                    <div class="trumpet left">🎺</div>
                                                    <div class="trumpet right">🎺</div>
                                                </div>
                                                <div class="parallax-el flex flex-col items-center gap-2">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-white/80 backdrop-blur-sm ring-1 ring-black/5 shadow-sm text-lg poppins font-bold text-blue-800 ll-tt-trigger" 
                                                        data-fullname="${p.name ?? ''}"
                                                        data-university="${p.university ?? ''}"
                                                        data-program="${p.program ?? ''}"
                                                        data-score="${fmtScore(p.best_score ?? 0)}"
                                                        data-seconds="${p.time_seconds ?? 0}"
                                                        data-difficulty="${p.difficulty ?? '-'}"
                                                    >${shortName(p.name)}</span>
                                                    <div class="text-gray-600">Skor: <span class="font-semibold">${fmtScore(p.best_score ?? 0)}</span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
                        }).join('');

                        const rest = rows.slice(3);
                        const list = rest.map((p, idx) => {
                            const rank = idx + 4;
                            const color = (idx % 2 === 0) ? 'bg-blue-600' : 'bg-orange-500';
                            return `
                            <div class="grid grid-cols-12 items-center px-4 py-3 border-b last:border-b-0 border-gray-200/70">
                                <div class="col-span-1 flex items-center">
                                    <span class="w-7 h-7 inline-flex items-center justify-center rounded-full ${color} text-white text-xs font-bold">${rank}</span>
                                </div>
                                <div class="col-span-3 text-blue-800 font-semibold">
                                    <span class="ll-tt-trigger" 
                                        data-fullname="${p.name ?? ''}" 
                                        data-university="${p.university ?? ''}" 
                                        data-program="${p.program ?? ''}" 
                                        data-score="${fmtScore(p.best_score ?? 0)}" 
                                        data-seconds="${p.time_seconds ?? 0}" 
                                        data-difficulty="${p.difficulty ?? '-'}"
                                    >${shortName(p.name)}</span>
                                </div>
                                <div class="col-span-4 text-gray-800">${p.university ?? '-'}</div>
                                <div class="col-span-2 text-gray-800">${p.program ?? '-'}</div>
                                <div class="col-span-2 text-gray-900 font-semibold text-right">${fmtScore(p.best_score ?? 0)}</div>
                            </div>`;
                        }).join('');

                        const header = `
                            <div class="px-4 py-2 text-xs uppercase tracking-wider text-gray-600 grid grid-cols-12">
                                <div class="col-span-1">Peringkat</div>
                                <div class="col-span-3">Nama</div>
                                <div class="col-span-4">Universitas</div>
                                <div class="col-span-2">Prodi</div>
                                <div class="col-span-2 text-right">Skor</div>
                            </div>`;

                        mount.innerHTML = `
                            <div class="leaderboard-cards flex flex-row flex-wrap md:flex-nowrap justify-center items-end gap-6 mb-8">${topCards}</div>
                            <div class="rounded-2xl border border-gray-200 bg-white/70 p-2">${header}${list}</div>
                        `;

                        // Inisialisasi efek setelah render
                        initParallax(section);
                        initCelebrate(section);
                        initTooltipEngine();
                    })
                    .catch(() => {
                        mount.innerHTML = '<div class="text-center text-red-500 py-10">Gagal memuat leaderboard.</div>';
                    });
            })();

            // Footer year
            (function(){
                const el = document.getElementById('ll-year');
                if(el) el.textContent = new Date().getFullYear();
            })();
        </script>
    </body>
    </html>

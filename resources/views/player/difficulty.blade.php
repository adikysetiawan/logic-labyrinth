<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pilih Tingkat Kesulitan - {{ config('app.name', 'Logic Labyrinth') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .bg-ll::before{ content:""; position:fixed; inset:0; background-image: radial-gradient(rgba(0,0,0,0.04) 1px, transparent 1px); background-size: 18px 18px; pointer-events:none; }
        .navbar-poppins{ font-family: 'Poppins', Arial, Helvetica, sans-serif; }
        .nav-fixed{ position:fixed; top:0; left:0; right:0; z-index:50; }
        .nav-shell{ transition: background-color .35s ease, box-shadow .35s ease, border-color .35s ease, transform .35s ease, padding .35s ease; padding:.5rem 18px; background-color:transparent; }
        .nav-shell.scrolled{ background-color:#ffffff; box-shadow:0 10px 26px rgba(0,0,0,.07); border-color:rgba(229,231,235,.9); padding:.75rem 26px; transform:scale(1.01); }
        .nav-link{ position:relative; }
        .nav-link::after{ content:""; position:absolute; left:0; bottom:-6px; height:2px; width:0; background:linear-gradient(90deg,#2563eb,#f97316); transition:width .3s ease; }
        .nav-link:hover::after{ width:100%; }
        .btn-ll{ position:relative; overflow:hidden; transition-property: background, box-shadow, transform; transition-duration:300ms; transition-timing-function:cubic-bezier(.4,0,.2,1); box-shadow:0 2px 12px rgba(0,0,0,.07); }
        .btn-ll:hover{ filter:brightness(1.05); transform:translateY(-1px); box-shadow:0 8px 26px rgba(0,0,0,.12); }
        .btn-ll::after{ content:""; position:absolute; top:-40%; left:-30%; width:40%; height:180%; background:linear-gradient(120deg,rgba(255,255,255,0) 0%, rgba(255,255,255,.35) 50%, rgba(255,255,255,0) 100%); transform:translateX(-120%) rotate(12deg); transition:transform .5s ease; }
        .btn-ll:hover::after{ transform:translateX(260%) rotate(12deg); }
        .btn-ll::before{ content:""; position:absolute; left:50%; transform:translateX(-50%); bottom:-8px; width:72%; height:12px; background:radial-gradient(closest-side, rgba(0,0,0,.18), rgba(0,0,0,0)); filter:blur(6px); transition:all .3s ease; border-radius:999px; }
        .btn-ll:hover::before{ bottom:-10px; width:78%; filter:blur(8px); opacity:.95; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-orange-50 flex flex-col relative overflow-x-hidden bg-ll text-gray-900">
    <!-- Navbar -->
    <nav id="topNav" class="navbar-poppins nav-fixed w-full flex justify-center py-3">
        <div class="max-w-6xl w-full px-6">
            <div id="navShell" class="nav-shell flex justify-between items-center w-full border border-gray-200/80 rounded-xl bg-transparent">
                <a href="/" class="flex items-center gap-3">
                    <img src="/images/logos/logic labyrinth_logo.png" alt="Logic Labyrinth Logo" class="h-9 w-9 object-contain bg-transparent shadow-none" style="margin-bottom:2px;" />
                    <span class="text-2xl font-bold text-blue-700 tracking-wide">Logic Labyrinth</span>
                </a>
                <a href="/" class="nav-link text-blue-700 font-semibold hover:text-orange-500 transition">Kembali ke Beranda</a>
            </div>
        </div>
    </nav>
    <!-- spacer to prevent layout shift when navbar is fixed -->
    <div aria-hidden="true" class="h-[64px]"></div>

    <main class="max-w-6xl mx-auto px-6 md:px-8 pb-16 w-full flex-1">
        <div class="grid grid-cols-1 md:[grid-template-columns:1.2fr_.8fr] gap-10 items-start mt-20">

            <!-- Left: Difficulty selection -->
            <div class="rounded-3xl border border-gray-200/80 bg-white/80 backdrop-blur-sm shadow-[0_10px_30px_rgba(79,70,229,0.08)] p-7 md:p-10">
                <div class="mb-5">
                    <h1 class="text-[1.8rem] md:text-4xl font-extrabold leading-tight">
                        <span class="text-blue-800">Pilih </span><span class="text-orange-500">Tingkat Kesulitan</span>
                    </h1>
                    <p class="text-gray-600 mt-2">Tentukan tantangan yang kamu inginkan. Kamu bisa mengubah tingkatnya nanti.</p>
                </div>

                @if (session('status'))
                    <div class="mb-4 p-3 rounded-xl bg-green-50 text-green-700 ring-1 ring-green-200 text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('player.difficulty.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @csrf
                    <button name="difficulty" value="easy" type="submit" class="group text-left rounded-2xl border border-blue-200 bg-white/70 p-7 md:p-8 shadow-sm hover:shadow-lg transition-all hover:-translate-y-0.5">
                        <div class="text-lg font-extrabold text-blue-800 mb-2">Mudah</div>
                        <div class="text-sm text-gray-600">Direkomendasikan untuk pemula. Pola sederhana.</div>
                        <div class="mt-4">
                            <span class="btn-ll inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-700 text-white text-base font-semibold hover:bg-blue-800">
                                Pilih
                            </span>
                        </div>
                    </button>
                    <button name="difficulty" value="hard" type="submit" class="group text-left rounded-2xl border border-orange-200 bg-white/70 p-7 md:p-8 shadow-sm hover:shadow-lg transition-all hover:-translate-y-0.5">
                        <div class="text-lg font-extrabold text-orange-600 mb-2">Susah</div>
                        <div class="text-sm text-gray-600">Untuk yang menantang. Pola lebih kompleks.</div>
                        <div class="mt-4">
                            <span class="btn-ll inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-orange-500 text-white text-base font-semibold hover:bg-orange-600">
                                Pilih
                            </span>
                        </div>
                    </button>
                </form>
            </div>

            <!-- Right: Illustration -->
            <div class="hidden md:flex items-center justify-center">
                <div class="rounded-[22px] p-[1.6px] bg-gradient-to-b from-indigo-400/70 via-fuchsia-400/60 to-amber-300/70 shadow-[0_10px_30px_rgba(79,70,229,0.14)]">
                    <div class="rounded-[20px] bg-white/80 p-4">
                        <img src="/images/icons/pilih_level.png" alt="Pilih Level" class="w-[360px] h-auto object-contain" />
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer (identik dengan home) -->
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
            if(!shell) return;
            const onScroll = () => { if(window.scrollY > 6){ shell.classList.add('scrolled'); } else { shell.classList.remove('scrolled'); } };
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
        })();
        // footer year
        (function(){ const el = document.getElementById('ll-year'); if(el) el.textContent = new Date().getFullYear(); })();
    </script>
</body>
</html>

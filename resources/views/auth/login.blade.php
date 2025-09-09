<x-guest-layout>
    <div class="min-h-screen bg-slate-950 bg-[radial-gradient(ellipse_at_top,rgba(30,64,175,0.15),transparent_60%),radial-gradient(ellipse_at_bottom_right,rgba(14,165,233,0.12),transparent_55%)] flex items-center justify-center px-6 py-10">
        <div class="w-full max-w-md">
            <!-- Branding -->
            <div class="flex flex-col items-center mb-6">
                <div class="size-12 rounded-xl bg-white/5 ring-1 ring-white/10 flex items-center justify-center mb-3">
                    <!-- Simple lock icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-6 h-6 text-indigo-300"><path stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M7 10V7a5 5 0 1110 0v3M6 10h12a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6a2 2 0 012-2z"/></svg>
                </div>
                <h1 class="text-xl font-extrabold text-indigo-200">Admin Login</h1>
                <p class="text-xs text-slate-400 mt-1">Masuk untuk mengelola Logic Labyrinth</p>
            </div>

            <!-- Card -->
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 shadow-[0_12px_40px_rgba(0,0,0,0.5)] p-6">
                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" class="text-slate-200 font-semibold" />
                        <x-text-input id="email" class="block mt-1 w-full rounded-xl bg-slate-950/80 border border-slate-700 text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-indigo-500/70 focus:border-indigo-500/70" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="__('Password')" class="text-slate-200 font-semibold" />
                        <div class="relative mt-1">
                            <x-text-input id="password" class="block w-full pr-11 rounded-xl bg-slate-950/80 border border-slate-700 text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-indigo-500/70 focus:border-indigo-500/70"
                                            type="password"
                                            name="password"
                                            required autocomplete="current-password" />
                            <button type="button" id="togglePwd" class="absolute inset-y-0 right-0 flex items-center justify-center w-10 text-slate-400 hover:text-slate-200 transition" aria-label="Toggle password">
                                <!-- eye -->
                                <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/><circle cx="12" cy="12" r="3"/></svg>
                                <!-- eye slash -->
                                <svg id="eyeClose" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-5 h-5 hidden"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18M10.584 10.587A3 3 0 0012 15a3 3 0 002.828-4.003M9.88 4.603A9.966 9.966 0 0112 4c4.477 0 8.268 2.943 9.542 7a10.97 10.97 0 01-4.043 5.177M6.61 6.61A10.97 10.97 0 002.458 12c.65 2.068 2.012 3.85 3.79 5.02"/></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me & Actions -->
                    <div class="flex items-center justify-between pt-2">
                        <label for="remember_me" class="inline-flex items-center gap-2">
                            <input id="remember_me" type="checkbox" class="rounded bg-slate-900 border-slate-700 text-indigo-600 shadow-sm focus:ring-indigo-500 focus:ring-offset-0" name="remember">
                            <span class="text-sm text-slate-300">{{ __('Remember me') }}</span>
                        </label>

                        <div class="flex items-center gap-3">
                            <x-primary-button class="btn-ll px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700">
                                {{ __('Log in') }}
                            </x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('togglePwd');
            const input = document.getElementById('password');
            const eyeOpen = document.getElementById('eyeOpen');
            const eyeClose = document.getElementById('eyeClose');
            if(btn && input){
                btn.addEventListener('click', () => {
                    const isPwd = input.getAttribute('type') === 'password';
                    input.setAttribute('type', isPwd ? 'text' : 'password');
                    eyeOpen.classList.toggle('hidden', !isPwd);
                    eyeClose.classList.toggle('hidden', isPwd);
                    // small ripple animation
                    btn.classList.add('scale-95');
                    setTimeout(()=>btn.classList.remove('scale-95'), 120);
                });
            }
        });
    </script>
</x-guest-layout>

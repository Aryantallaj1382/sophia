<x-guest-layout>
    <div class="flex items-center justify-center min-h-screen bg-gradient-to-tr from-indigo-100 to-purple-200 dark:from-slate-900  p-4">
        <div class="w-full max-w-md sm:max-w-lg lg:max-w-2xl bg-white dark:bg-slate-800 rounded-3xl shadow-2xl p-8 sm:p-10 relative overflow-hidden">
            <!-- Decorative Circles -->
            <div class="absolute -top-20 -right-20 w-56 h-56 bg-indigo-400/20 rounded-full filter blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 w-56 h-56 bg-purple-400/20 rounded-full filter blur-3xl"></div>

            <h2 class="text-2xl sm:text-3xl font-extrabold mb-6 text-center text-slate-800 dark:text-slate-100">ورود به پنل ادمین</h2>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 dark:text-slate-300">
                        📧
                    </span>
                    <x-text-input id="email"
                                  class="block w-full rounded-xl border border-gray-300 dark:border-gray-600 px-10 py-2 dark:bg-slate-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                  type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <!-- Password -->
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 dark:text-slate-300">
                        🔒
                    </span>
                    <x-text-input id="password"
                                  class="block w-full rounded-xl border border-gray-300 dark:border-gray-600 px-10 py-2 dark:bg-slate-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                  type="password" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <!-- Remember & Forgot -->
                <div class="flex flex-col sm:flex-row items-center justify-between text-sm text-slate-600 dark:text-slate-300 gap-2 sm:gap-0">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                        مرا به خاطر بسپار
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-indigo-500 hover:underline">فراموشی رمز عبور؟</a>
                    @endif
                </div>

                <!-- Submit -->
                <div class="flex justify-center">
                    <x-primary-button class="w-full sm:w-auto py-3 mt-2 text-white bg-indigo-500 hover:bg-indigo-600 rounded-xl font-semibold shadow-lg transition duration-300 text-center">
                        ورود
                    </x-primary-button>
                </div>
            </form>

            <div class="mt-8 text-center text-sm text-slate-500 dark:text-slate-400">
              sophia Admin
            </div>
        </div>
    </div>
</x-guest-layout>

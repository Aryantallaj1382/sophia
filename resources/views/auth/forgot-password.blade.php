<x-guest-layout>
    <div class="flex items-center justify-center min-h-screen bg-slate-50 dark:bg-slate-950 p-4">
        <div class="w-full max-w-md bg-white dark:bg-slate-800 rounded-3xl shadow-lg p-8">
            <h2 class="text-2xl font-bold mb-4 text-center text-slate-800 dark:text-slate-100">بازیابی رمز عبور</h2>

            <p class="mb-6 text-sm text-slate-600 dark:text-slate-300">
                اگر رمز عبور خود را فراموش کرده‌اید، ایمیل خود را وارد کنید تا لینک بازنشانی برایتان ارسال شود.
            </p>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('ایمیل')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-primary-button class="w-full text-center">
                        ارسال لینک بازیابی
                    </x-primary-button>
                </div>
            </form>

            <div class="mt-6 text-center text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('login') }}" class="underline hover:text-indigo-500">بازگشت به ورود</a>
            </div>
        </div>
    </div>
</x-guest-layout>

@extends('layouts.app')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-slate-50 dark:bg-slate-900">
        <div class="w-full max-w-md bg-white dark:bg-slate-800 rounded-xl shadow p-8">
            <h2 class="text-2xl font-bold mb-6 text-center text-slate-800 dark:text-slate-100">ورود به پنل ادمین</h2>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-200">ایمیل</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                    @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-200">رمز عبور</label>
                    <input type="password" name="password" required
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                    @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-200">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 dark:border-gray-600">
                        مرا به خاطر بسپار
                    </label>
                    <a href="#" class="text-sm text-indigo-500 hover:underline">فراموشی رمز عبور؟</a>
                </div>

                <button type="submit"
                        class="w-full bg-indigo-500 text-white rounded-lg py-2 hover:bg-indigo-600 transition">ورود</button>
            </form>
        </div>
    </div>
@endsection

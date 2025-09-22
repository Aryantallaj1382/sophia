<header class="sticky top-0 z-30 bg-white/80 dark:bg-slate-900/70 backdrop-blur border-b border-slate-200 dark:border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center gap-2">
                <button class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="flex items-center gap-2">
                    <div class="h-9 w-9 rounded-2xl bg-gradient-to-tr from-indigo-500 to-violet-500 shadow"></div>
                    <span class="font-semibold">پنل ادمین</span>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2">
                <button class="p-2 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800">🌙</button>
                <button class="relative p-2 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800">
                    🔔
                    <span class="absolute -top-0.5 -right-0.5 h-2.5 w-2.5 rounded-full bg-rose-500"></span>
                </button>
                <div class="flex items-center gap-2 rounded-2xl bg-slate-100 dark:bg-slate-800 px-2 py-1">
{{--                    <img src="https://i.pravatar.cc/40" class="h-8 w-8 rounded-xl" alt="avatar">--}}
                    <span class="hidden sm:block text-sm">{{ auth()->user()?->name ?? 'کاربر' }}</span>
                </div>
            </div>
        </div>
    </div>
</header>

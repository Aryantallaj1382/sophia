<aside class="col-span-3">
    <div class="sticky top-20">
        <div id="sidebar" class="rounded-3xl border border-slate-200 bg-white shadow-sm p-3 w-64 transition-all duration-300">
            <div class="flex justify-between items-center mb-2">
                <span id="sidebar-title" class="text-sm font-semibold opacity-70">منو</span>
                <button id="sidebar-toggle" class="p-1 rounded hover:bg-slate-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            <nav class="flex flex-col gap-1 mt-2">
                <a href="#" class="flex items-center gap-3 rounded-2xl px-3 py-2 text-sm hover:bg-indigo-50">
                    <span class="text-lg">📊</span>
                    <span class="menu-text">داشبورد</span>
                </a>
                <a href="#" class="flex items-center gap-3 rounded-2xl px-3 py-2 text-sm hover:bg-indigo-50">
                    <span class="text-lg">👥</span>
                    <span class="menu-text">کاربران</span>
                </a>
                <a href="#" class="flex items-center gap-3 rounded-2xl px-3 py-2 text-sm hover:bg-indigo-50">
                    <span class="text-lg">⚙️</span>
                    <span class="menu-text">تنظیمات</span>
                </a>
                <a href="#" class="flex items-center gap-3 rounded-2xl px-3 py-2 text-sm hover:bg-indigo-50">
                    <span class="text-lg">📊</span>
                    <span class="menu-text">داشبورد</span>
                </a>
                <a href="#" class="flex items-center gap-3 rounded-2xl px-3 py-2 text-sm hover:bg-indigo-50">
                    <span class="text-lg">👥</span>
                    <span class="menu-text">کاربران</span>
                </a>
                <a href="#" class="flex items-center gap-3 rounded-2xl px-3 py-2 text-sm hover:bg-indigo-50">
                    <span class="text-lg">⚙️</span>
                    <span class="menu-text">تنظیمات</span>
                </a>
                <a href="#" class="flex items-center gap-3 rounded-2xl px-3 py-2 text-sm hover:bg-indigo-50">
                    <span class="text-lg">⚙️</span>
                    <span class="menu-text">تنظیمات</span>
                </a>
                <a href="#" class="flex items-center gap-3 rounded-2xl px-3 py-2 text-sm hover:bg-indigo-50">
                    <span class="text-lg">📊</span>
                    <span class="menu-text">داشبورد</span>
                </a>
                <a href="#" class="flex items-center gap-3 rounded-2xl px-3 py-2 text-sm hover:bg-indigo-50">
                    <span class="text-lg">👥</span>
                    <span class="menu-text">کاربران</span>
                </a>
                <a href="#" class="flex items-center gap-3 rounded-2xl px-3 py-2 text-sm hover:bg-indigo-50">
                    <span class="text-lg">⚙️</span>
                    <span class="menu-text">تنظیمات</span>
                </a>
                <a href="#" class="flex items-center gap-3 rounded-2xl px-3 py-2 text-sm hover:bg-indigo-50">
                    <span class="text-lg">⚙️</span>
                    <span class="menu-text">تنظیمات</span>
                </a>
                <a href="#" class="flex items-center gap-3 rounded-2xl px-3 py-2 text-sm hover:bg-indigo-50">
                    <span class="text-lg">📊</span>
                    <span class="menu-text">داشبورد</span>
                </a>
                <a href="#" class="flex items-center gap-3 rounded-2xl px-3 py-2 text-sm hover:bg-indigo-50">
                    <span class="text-lg">👥</span>
                    <span class="menu-text">کاربران</span>
                </a>
                <a href="#" class="flex items-center gap-3 rounded-2xl px-3 py-2 text-sm hover:bg-indigo-50">
                    <span class="text-lg">⚙️</span>
                    <span class="menu-text">تنظیمات</span>
                </a>
                <a href="#" class="flex items-center gap-3 rounded-2xl px-3 py-2 text-sm hover:bg-indigo-50">
                    <span class="text-lg">📊</span>
                    <span class="menu-text">داشبورد</span>
                </a>
                <a href="#" class="flex items-center gap-3 rounded-2xl px-3 py-2 text-sm hover:bg-indigo-50">
                    <span class="text-lg">👥</span>
                    <span class="menu-text">کاربران</span>
                </a>
                <a href="#" class="flex items-center gap-3 rounded-2xl px-3 py-2 text-sm hover:bg-indigo-50">
                    <span class="text-lg">⚙️</span>
                    <span class="menu-text">تنظیمات</span>
                </a>
            </nav>
        </div>
    </div>
</aside>

<script>
    const toggleBtn = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    const menuTexts = document.querySelectorAll('.menu-text, #sidebar-title');

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('w-64');
        sidebar.classList.toggle('w-16');
        menuTexts.forEach(el => el.classList.toggle('hidden'));
    });
</script>

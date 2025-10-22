<aside class="w-64 bg-white shadow-xl border-r min-h-screen flex flex-col">
    {{-- پروفایل / برند --}}
    <div class="p-6 border-b flex items-center gap-3">
        <img src="https://ui-avatars.com/api/?name=Admin+User&background=0D8ABC&color=fff&size=50"
             alt="Admin" class="w-12 h-12 rounded-full border">
        <div>
            <h2 class="font-bold text-gray-800 text-sm">ادمین</h2>
            <span class="text-xs text-gray-500">مدیر کل سیستم</span>
        </div>
    </div>

    {{-- منو --}}
    <nav class="flex-1 p-4">
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.welcome')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">داشبورد</span>

                </a>
            </li>


        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.professors.index')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">اساتید</span>

                </a>
            </li>


        </ul>


        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.exams.index')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">آزمون ها </span>
                </a>
            </li>
        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.group_class.index')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">گلاس های گروهی </span>
                </a>
            </li>
        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.webinar.index')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">وبینار</span>
                </a>
            </li>
        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.blogs.index')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">بلاگ ها </span>
                </a>
            </li>
        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.sliders.index')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">اسلایدر ها </span>
                </a>
            </li>
        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.users.index')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">کاربران</span>

                </a>
            </li>


        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.books.index')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">کتابخانه</span>

                </a>
            </li>


        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.tickets.index')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">تیکت ها</span>

                </a>
            </li>


        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.conversations.index')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">چت ها</span>

                </a>
            </li>


        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.plans.index')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600"> پلن ها</span>

                </a>
            </li>


        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.private-classes.index')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">کلاس های خصوصی</span>

                </a>
            </li>


        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.stories.main')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">استوری های صفحه اصلی</span>

                </a>
            </li>


        </ul>
    </nav>

    {{-- دکمه خروج --}}
    <div class="p-4 border-t">
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button
                class="w-full flex items-center justify-center gap-2 bg-red-500 hover:bg-red-600
                       text-white py-2 rounded-lg transition">
                <span class="material-icons text-sm">logout</span>
                خروج
            </button>
        </form>
    </div>
</aside>

@extends('admin.layouts.app')

@section('title', 'ویرایش استاد')

@section('content')
    <div class="max-w-5xl mx-auto py-8">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="flex flex-col md:flex-row items-stretch">
                <!-- سمت چپ: کارت پروفایل -->
                <div class="md:w-1/3 bg-gradient-to-b from-gray-50 to-white p-6 flex flex-col items-center gap-4">
                    <div class="w-36 h-36 rounded-full overflow-hidden shadow-lg border-4 border-white">
                        <img src="{{ asset($professor->user->profile ?? 'i1.png') }}" alt="Profile" class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">{{ $professor->user->name }}</h3>
                    <p class="text-sm text-gray-500 text-center px-2">{{ $professor->bio ?? 'بدون توضیحات' }}</p>

                    <div class="w-full mt-2">
                        <a href="{{ route('admin.professors.index') }}" class="block text-center bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg">بازگشت به لیست</a>
                    </div>
                    <div class="w-full mt-2">
                        <a href="{{ route('admin.professorsStory.index' , $professor) }}" class="block text-center bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg">استوری های استاد</a>
                    </div>
                    <div class="w-full mt-2">
                        <a href="{{ route('admin.professorsBook.index' , $professor) }}" class="block text-center bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg">کتاب های استاد</a>
                    </div>
                </div>

                <!-- سمت راست: فرم -->
                <div class="md:w-2/3 p-6">
                    <h2 class="text-2xl font-semibold mb-4">ویرایش اطلاعات استاد</h2>

                    @if ($errors->any())
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                            <ul class="list-disc pr-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.professors.update', $professor->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- اطلاعات پایه -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">نام</label>
                                <input type="text" name="name" value="{{ old('name', $professor->user->name) }}" class="mt-1 block w-full border rounded p-2" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">ایمیل</label>
                                <input type="email" name="email" value="{{ old('email', $professor->user->email) }}" class="mt-1 block w-full border rounded p-2" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">موبایل</label>
                                <input type="text" name="mobile" value="{{ old('mobile', $professor->user->mobile) }}" class="mt-1 block w-full border rounded p-2">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">سابقه تدریس (سال)</label>
                                <input type="number" name="years_of_experience" value="{{ old('years_of_experience', $professor->years_of_experience) }}" class="mt-1 block w-full border rounded p-2" min="0">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">بیو</label>
                                <textarea name="bio" rows="3" class="mt-1 block w-full border rounded p-2">{{ old('bio', $professor->bio) }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">رمز عبور (در صورت تغییر)</label>
                                <input type="password" name="password" class="mt-1 block w-full border rounded p-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">تایید رمز عبور</label>
                                <input type="password" name="password_confirmation" class="mt-1 block w-full border rounded p-2">
                            </div>
                        </div>

                        <!-- وضعیت‌ها -->
                        <div class="flex flex-wrap gap-3">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $professor->is_active) ? 'checked' : '' }}>
                                <span class="text-sm">فعال</span>
                            </label>

                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="is_verified" value="1" {{ old('is_verified', $professor->is_verified) ? 'checked' : '' }}>
                                <span class="text-sm">تایید شده</span>
                            </label>

                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="is_native" value="1" {{ old('is_native', $professor->is_native) ? 'checked' : '' }}>
                                <span class="text-sm">استاد محلی</span>
                            </label>
                        </div>

                        <!-- جنسیت و تاریخ تولد -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">جنسیت</label>
                                <div class="flex gap-4 mt-1">
                                    <label class="flex items-center gap-2">
                                        <input type="radio" name="gender" value="male" {{ old('gender', $professor->gender) == 'male' ? 'checked' : '' }}>
                                        <span>مرد</span>
                                    </label>
                                    <label class="flex items-center gap-2">
                                        <input type="radio" name="gender" value="female" {{ old('gender', $professor->gender) == 'female' ? 'checked' : '' }}>
                                        <span>زن</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">تاریخ تولد</label>
                                <input type="date" name="birth_date" value="{{ old('birth_date', $professor->birth_date) }}" class="mt-1 block w-full border rounded p-2">
                            </div>
                        </div>

                        <!-- ویدیوها و تصاویر -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700 mb-2">عکس پروفایل</label>

                                <div class="flex flex-col items-center">
                                    <!-- نمایش عکس پروفایل -->
                                    <div class="relative w-32 h-32 rounded-full overflow-hidden shadow-md border-2 border-gray-200">
                                        <img id="profilePreview"
                                             src="{{ $professor->user->profile ? asset($professor->user->profile) : asset('images/default-avatar.png') }}"
                                             alt="profile"
                                             class="w-full h-full object-cover">
                                        <!-- آیکن دوربین -->
                                        <label for="profileInput" class="absolute bottom-0 right-0 bg-gray-800 bg-opacity-70 text-white p-2 rounded-full cursor-pointer hover:bg-opacity-90 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h4l1.68-2.68A1 1 0 019.37 4h5.26a1 1 0 01.84.32L17 7h4a1 1 0 011 1v11a1 1 0 01-1 1H3a1 1 0 01-1-1V8a1 1 0 011-1z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </label>
                                    </div>

                                    <!-- اینپوت فایل -->
                                    <input type="file" name="profile" id="profileInput" accept="image/*" class="hidden">
                                    <p class="mt-2 text-xs text-gray-500">فرمت‌های مجاز: JPG, PNG | حداکثر 4MB</p>
                                </div>
                            </div>

                            <!-- اسکریپت پیش‌نمایش -->
                            <script>
                                document.getElementById('profileInput').addEventListener('change', function(e) {
                                    const file = e.target.files[0];
                                    if (file) {
                                        const reader = new FileReader();
                                        reader.onload = function(ev) {
                                            document.getElementById('profilePreview').src = ev.target.result;
                                        }
                                        reader.readAsDataURL(file);
                                    }
                                });
                            </script>


                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">ویدیو نمونه تدریس</label>
                                <div class="mt-2 p-3 border-2 border-dashed rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                                    @if($professor->sample_video)
                                        <video controls class="w-full rounded-lg shadow-md">
                                            <source src="{{ asset($professor->sample_video) }}">
                                            مرورگر شما از ویدیو پشتیبانی نمی‌کند.
                                        </video>
                                    @else
                                        <p class="text-gray-500 text-sm text-center">هنوز ویدیویی آپلود نشده است</p>
                                    @endif

                                    <label class="mt-3 flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm rounded-lg cursor-pointer hover:bg-blue-700 transition">
                                        <i class="fas fa-upload ml-2"></i> انتخاب ویدیو
                                        <input type="file" name="sample_video" accept="video/*" class="hidden">
                                    </label>
                                </div>
                            </div>

                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">ویدیو معرفی</label>
                                <div class="mt-2 p-3 border-2 border-dashed rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                                    @if($professor->teaching_video)
                                        <video controls class="w-full rounded-lg shadow-md">
                                            <source src="{{ asset($professor->teaching_video) }}">
                                            مرورگر شما از ویدیو پشتیبانی نمی‌کند.
                                        </video>
                                    @else
                                        <p class="text-gray-500 text-sm text-center">هنوز ویدیویی آپلود نشده است</p>
                                    @endif

                                    <label class="mt-3 flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg cursor-pointer hover:bg-green-700 transition">
                                        <i class="fas fa-upload ml-2"></i> انتخاب ویدیو
                                        <input type="file" name="teaching_video" accept="video/*" class="hidden">
                                    </label>
                                </div>
                            </div>

                        </div>

                        <!-- بخش انتخاب‌ها به صورت کارت/چک‌باکس (بدون دراپ‌داون) -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold">گروه‌های سنی</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach($age as $item)
                                    <label class="flex items-center gap-2 p-2 border rounded hover:shadow cursor-pointer">
                                        <input type="checkbox" name="age_groups[]" value="{{ $item->id }}" {{ in_array($item->id, old('age_groups', $professor->ageGroups->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <span class="text-sm">{{ $item->title }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <h3 class="text-lg font-semibold">سطوح زبان</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach($level as $item)
                                    <label class="flex items-center gap-2 p-2 border rounded hover:shadow cursor-pointer">
                                        <input type="checkbox" name="level[]" value="{{ $item->id }}" {{ in_array($item->id, old('level', $professor->languageLevels->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <span class="text-sm">{{ $item->title }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <h3 class="text-lg font-semibold">پلتفرم‌ها</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach($platform as $item)
                                    <label class="flex items-center gap-2 p-2 border rounded hover:shadow cursor-pointer">
                                        <input type="checkbox" name="platform[]" value="{{ $item->id }}" {{ in_array($item->id, old('platform', $professor->platforms->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <span class="text-sm">{{ $item->title }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <h3 class="text-lg font-semibold">لهجه‌ها</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach($accent as $item)
                                    <label class="flex items-center gap-2 p-2 border rounded hover:shadow cursor-pointer">
                                        <input type="checkbox" name="accent[]" value="{{ $item->id }}" {{ in_array($item->id, old('accent', $professor->accents->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <span class="text-sm">{{ $item->title }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <h3 class="text-lg font-semibold">مهارت‌ها</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach($skill as $item)
                                    <label class="flex items-center gap-2 p-2 border rounded hover:shadow cursor-pointer">
                                        <input type="checkbox" name="skill[]" value="{{ $item->id }}" {{ in_array($item->id, old('skill', $professor->skills->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <span class="text-sm">{{ $item->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <h3 class="text-lg font-semibold mb-2">استوری‌ها</h3>

                            <h3 class="text-lg font-semibold mb-2">اهداف یادگیری (زیرهدف‌ها)</h3>
                            <div class="space-y-2">
                                @foreach($goals as $g)
                                    <div x-data="{ open: false }" class="border rounded">
                                        <button type="button"
                                                @click="open = !open"
                                                class="w-full flex justify-between items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 transition font-semibold text-right">
                                            {{ $g->title }}
                                            <span :class="{ 'rotate-180': open }" class="transition-transform inline-block">▼</span>
                                        </button>
                                        <div x-show="open" class="px-4 py-2 grid grid-cols-1 gap-2">
                                            @foreach($g->subgoals as $sub)
                                                <label class="flex items-center gap-2">
                                                    <input type="checkbox" name="learning_goals[]" value="{{ $sub->id }}"
                                                        {{ in_array($sub->id, old('learning_goals', $professor->learningGoals->pluck('subgoal_id')->toArray())) ? 'checked' : '' }}>
                                                    text-sm">{{ $sub->title }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>


                        <div class="flex justify-between items-center mt-4">
                            <a href="{{ route('admin.professors.index') }}" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">انصراف</a>
                            <button type="submit" class="px-6 py-2 rounded bg-green-600 text-white hover:bg-green-700">ذخیره تغییرات</button>
                        </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

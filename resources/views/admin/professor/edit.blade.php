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
                                <input type="text" name="mobile" value="{{ old('mobile', $professor->user->mobile) }}" class="mt-1 block w-full border rounded p-2" required>
                            </div>

                            {{--                            <div>--}}
                            {{--                                <label class="block text-sm font-medium text-gray-700">موبایل</label>--}}
                            {{--                                <input type="text" name="mobile" value="{{ old('mobile', $professor->user->mobile) }}" class="mt-1 block w-full border rounded p-2">--}}
                            {{--                            </div>--}}

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

                            <!-- ویدیو معرفی -->
                            <div class="col-span-1 relative">
                                <label class="flex flex-col items-center justify-center border-2 border-dashed rounded-lg p-4 cursor-pointer hover:border-blue-500">
                                    آپلود ویدیو معرفی
                                    <input type="file" name="sample_video" accept="video/*" class="hidden">
                                </label>

                                @if($professor->sample_video)
                                    <div class="mt-3 relative">
                                        <video controls class="w-full rounded-lg shadow-md">
                                            <source src="{{ asset(old('sample_video', $professor->sample_video)) }}">
                                        </video>
                                        <button type="button" onclick="confirmDelete('sample_video')"
                                                class="absolute top-2 right-2 bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-red-700 shadow-lg">
                                            ×
                                        </button>
                                    </div>
                                    <input type="hidden" name="delete_sample_video" id="delete_sample_video" value="">
                                @endif
                            </div>

                            <!-- کاور ویدیو معرفی -->
                            <div class="col-span-1 relative">
                                <label class="flex flex-col items-center justify-center border-2 border-dashed rounded-lg p-4 cursor-pointer hover:border-blue-500">
                                    آپلود کاور معرفی
                                    <input type="file" name="sample_video_cover" accept="image/*" class="hidden">
                                </label>

                                @if($professor->sample_video_cover)
                                    <div class="mt-3 relative">
                                        <img src="{{ asset(old('sample_video_cover', $professor->sample_video_cover)) }}" class="w-32 h-32 rounded object-cover shadow-md">
                                        <button type="button" onclick="confirmDelete('sample_video_cover')"
                                                class="absolute top-2 right-2 bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-red-700 shadow-lg">
                                            ×
                                        </button>
                                    </div>
                                    <input type="hidden" name="delete_sample_video_cover" id="delete_sample_video_cover" value="">
                                @endif
                            </div>

                            <!-- ویدیو نمونه تدریس -->
                            <div class="col-span-1 relative">
                                <label class="flex flex-col items-center justify-center border-2 border-dashed rounded-lg p-4 cursor-pointer hover:border-blue-500">
                                    آپلود نمونه تدریس
                                    <input type="file" name="teaching_video" accept="video/*" class="hidden">
                                </label>

                                @if($professor->teaching_video)
                                    <div class="mt-3 relative">
                                        <video controls class="w-full rounded-lg shadow-md">
                                            <source src="{{ asset(old('teaching_video', $professor->teaching_video)) }}">
                                        </video>
                                        <button type="button" onclick="confirmDelete('teaching_video')"
                                                class="absolute top-2 right-2 bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-red-700 shadow-lg">
                                            ×
                                        </button>
                                    </div>
                                    <input type="hidden" name="delete_teaching_video" id="delete_teaching_video" value="">
                                @endif
                            </div>

                            <!-- کاور ویدیو نمونه تدریس -->
                            <div class="col-span-1 relative">
                                <label class="flex flex-col items-center justify-center border-2 border-dashed rounded-lg p-4 cursor-pointer hover:border-blue-500">
                                    آپلود کاور نمونه تدریس
                                    <input type="file" name="teaching_video_cover" accept="image/*" class="hidden">
                                </label>

                                @if($professor->teaching_video_cover)
                                    <div class="mt-3 relative">
                                        <img src="{{ asset(old('teaching_video_cover', $professor->teaching_video_cover)) }}" class="w-32 h-32 rounded object-cover shadow-md">
                                        <button type="button" onclick="confirmDelete('teaching_video_cover')"
                                                class="absolute top-2 right-2 bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-red-700 shadow-lg">
                                            ×
                                        </button>
                                    </div>
                                    <input type="hidden" name="delete_teaching_video_cover" id="delete_teaching_video_cover" value="">
                                @endif
                            </div>

                            <!-- عکس پروفایل -->
                            <div class="col-span-1 relative">
                                <label class="flex flex-col items-center justify-center border-2 border-dashed rounded-lg p-4 cursor-pointer hover:border-blue-500">
                                    آپلود عکس پروفایل
                                    <input type="file" name="profile" accept="image/*" id="profileInput" class="hidden">
                                </label>

                                @if($professor->user->profile)
                                    <div class="mt-3 relative">
                                        <img id="profilePreview" src="{{ asset(old('profile', $professor->user->profile)) }}" class="w-32 h-32 rounded-full object-cover shadow-md">
                                        <button type="button" onclick="confirmDelete('profile')"
                                                class="absolute top-2 right-2 bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-red-700 shadow-lg">
                                            ×
                                        </button>
                                    </div>
                                    <input type="hidden" name="delete_profile" id="delete_profile" value="">
                                @else
                                    <img id="profilePreview" src="{{ asset(old('profile', 'i1.png')) }}" class="mt-2 w-32 h-32 rounded-full object-cover">
                                @endif
                            </div>

                        </div>

                        <!-- اسکریپت پیش‌نمایش عکس پروفایل -->
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

                        <!-- اسکریپت حذف فایل‌ها -->
                        <script>
                            function confirmDelete(field) {
                                if (confirm('آیا از حذف این فایل مطمئن هستید؟')) {
                                    document.getElementById('delete_' + field).value = '1';

                                    // کم‌رنگ کردن پیش‌نمایش و پنهان کردن دکمه پس از تایید حذف (سمت کلاینت)
                                    const previewContainer = event.target.closest('.relative');
                                    const media = previewContainer.querySelector('video, img');
                                    if (media) media.style.opacity = '0.5';
                                    event.target.style.display = 'none';
                                }
                            }
                        </script>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="placement" class="block text-sm font-medium text-gray-700 mb-1">تعیین سطح</label>
                                <select id="placement" name="placement"
                                        class="block w-full rounded-md border border-gray-300 bg-white py-2 pl-3 pr-10 text-base focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">

                                    @php
                                        $placementValue = old('placement', $professor->placement);
                                    @endphp

                                    <option value="" {{ $placementValue === null || $placementValue === '' ? 'selected' : '' }}>برگزار نمیکند</option>
                                    <option value="0"  {{ $placementValue == '0' ? 'selected' : '' }}>بدون تخفیف</option>
                                    <option value="50" {{ $placementValue == '50' ? 'selected' : '' }}>50 درصد تخفیف</option>
                                    <option value="100" {{ $placementValue == '100' ? 'selected' : '' }}>رایگان</option>

                                </select>
                            </div>

                            <div>
                                <label for="trial" class="block text-sm font-medium text-gray-700 mb-1">کلاس آزمایشی</label>
                                <select id="trial" name="trial"
                                        class="block w-full rounded-md border border-gray-300 bg-white py-2 pl-3 pr-10 text-base focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">

                                    @php
                                        $trialValue = old('trial', $professor->trial);
                                    @endphp

                                    <option value="" {{ $trialValue === null || $trialValue === '' ? 'selected' : '' }}>برگزار نمیکند</option>
                                    <option value="0"  {{ $trialValue == '0' ? 'selected' : '' }}>بدون تخفیف</option>
                                    <option value="50" {{ $trialValue == '50' ? 'selected' : '' }}>50 درصد تخفیف</option>
                                    <option value="100" {{ $trialValue == '100' ? 'selected' : '' }}>رایگان</option>

                                </select>
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
                            <h4 class="text-sm font-semibold mt-4 mb-2">اطلاعات</h4>
                            <div class="grid grid-cols-4 gap-3">
                                <div>
                                    <label class="flex items-center gap-2">
                                        <span class="text-sm">teams</span>
                                    </label>
                                    <input type="text" name="teams" value="{{ old('teams', $professor->teams) }}" placeholder="teams" class="p-2 border rounded text-sm">

                                </div>
                             <div>
                                 <label class="flex items-center gap-2">
                                     <span class="text-sm">zoom_link</span>
                                 </label>
                                 <input type="text" name="zoom_link" value="{{ old('zoom_link', $professor->zoom_link) }}" placeholder="zoom_link" class="p-2 border rounded text-sm">

                             </div>
                                <div>
                                    <label class="flex items-center gap-2">
                                        <span class="text-sm">classin_link</span>
                                    </label>
                                    <input type="text" name="classin_link" value="{{ old('classin_link', $professor->classin_link) }}" placeholder="classin_link" class="p-2 border rounded text-sm">

                                </div>
                                <div>

                                    <label class="flex items-center gap-2">
                                        <span class="text-sm">voov_link</span>
                                    </label>
                                    <input type="text" name="voov_link" value="{{ old('voov_link', $professor->voov_link) }}" placeholder="voov_link" class="p-2 border rounded text-sm">

                                </div>


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
                                                    {{ $sub->title }}</span>
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

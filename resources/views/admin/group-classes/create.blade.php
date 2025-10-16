@extends('admin.layouts.app')

@section('content')
    <div class="container mx-auto p-8">
        <div class="max-w-4xl mx-auto bg-white shadow-2xl rounded-3xl p-10 backdrop-blur-sm border border-gray-200">
            <h2 class="text-4xl font-extrabold text-center text-gray-800 mb-10">➕ ایجاد کلاس گروهی جدید</h2>
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc pr-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{route('admin.group_class.store')}}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <!-- استاد و زبان -->
                <div class="grid md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-xl">
                    <div>
                        <label class="block font-semibold mb-2">استاد <span class="text-red-500">*</span></label>
                        <select name="professor_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition" required>
                            @foreach($professors as $professor)
                                <option value="{{ $professor->id }}">{{ $professor->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">زبان <span class="text-red-500">*</span></label>
                        <select name="language_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition" required>
                            @foreach($languages as $lang)
                                <option value="{{ $lang->id }}">{{ $lang->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- سطح و موضوع -->
                <div class="grid md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-xl">
                    <div>
                        <label class="block font-semibold mb-2">سطح <span class="text-red-500">*</span></label>
                        <select name="language_level_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition" required>
                            @foreach($levels as $level)
                                <option value="{{ $level->id }}">{{ $level->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">موضوع <span class="text-red-500">*</span></label>
                        <select name="subject_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition" required>
                            @foreach($subgoals as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- گروه سنی و پلتفرم -->
                <div class="grid md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-xl">
                    <div>
                        <label class="block font-semibold mb-2">گروه سنی <span class="text-red-500">*</span></label>
                        <select name="age_group_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition" required>
                            @foreach($ageGroups as $age)
                                <option value="{{ $age->id }}">{{ $age->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">پلتفرم <span class="text-red-500">*</span></label>
                        <select name="platform_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition" required>
                            @foreach($platforms as $platform)
                                <option value="{{ $platform->id }}">{{ $platform->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- کتاب -->
                <div class="bg-gray-50 p-6 rounded-xl">
                    <label class="block font-semibold mb-2">کتاب (اختیاری)</label>
                    <select name="book_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">
                        <option value="">بدون کتاب</option>
                        @foreach($books as $item)
                            <option value="{{ $item->id }}" data-img="{{ asset($item->image) }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- ظرفیت‌ها -->
                <div class="grid md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-xl">
                    <div>
                        <label class="block font-semibold mb-2">حداقل ظرفیت <span class="text-red-500">*</span></label>
                        <input type="number" name="min_capacity" placeholder="مثال: 5" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition" required>
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">حداکثر ظرفیت <span class="text-red-500">*</span></label>
                        <input type="number" name="max_capacity" placeholder="مثال: 20" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition" required>
                    </div>
                </div>

                <!-- تعداد جلسات و مدت -->
                <div class="grid md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-xl">
                    <div>
                        <label class="block font-semibold mb-2">تعداد جلسات <span class="text-red-500">*</span></label>
                        <input type="number" name="sessions_count" placeholder="مثال: 10" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition" required>
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">مدت هر جلسه (ساعت)</label>
                        <input type="number" name="hourly" placeholder="مثال: 1.5" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">
                    </div>
                </div>

                <!-- تاریخ و لینک کلاس -->
                <div class="grid md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-xl">
                    <div>
                        <label class="block font-semibold mb-2">تاریخ شروع</label>
                        <input type="date" name="start_date" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">تاریخ پایان</label>
                        <input type="date" name="end_date" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">
                    </div>
                </div>
                <!-- انتخاب روزهای هفته و زمان -->
                <div class="bg-gray-50 p-6 rounded-xl">
                    <label class="block font-semibold mb-2">روزهای هفته</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @php
                            // به انگلیسی تغییر دادم
                            $days = [
                                'Saturday' => 'شنبه',
                                'Sunday' => 'یک‌شنبه',
                                'Monday' => 'دو‌شنبه',
                                'Tuesday' => 'سه‌شنبه',
                                'Wednesday' => 'چهار‌شنبه',
                                'Thursday' => 'پنج‌شنبه',
                                'Friday' => 'جمعه',
                            ];
                        @endphp
                        @foreach($days as $en => $fa)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="days[]" value="{{ $en }}" class="rounded border-gray-300">
                                <span>{{ $fa }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="mt-4 grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block font-semibold mb-2">ساعت شروع</label>
                            <input type="time" name="start_time" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400">
                        </div>
                        <div>
                            <label class="block font-semibold mb-2">ساعت پایان</label>
                            <input type="time" name="end_time" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400">
                        </div>
                    </div>
                </div>



                <div class="bg-gray-50 p-6 rounded-xl">
                    <label class="block font-semibold mb-2">لینک کلاس</label>
                    <input type="text" name="class_link" placeholder="https://example.com" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">
                </div>


                <div class="bg-gray-50 p-6 rounded-xl shadow-md border border-gray-200">
                    <label class="block font-semibold mb-4 text-gray-700">تصویر کلاس</label>

                    <div class="flex flex-col items-center justify-center gap-4">
                        <!-- پیش‌نمایش تصویر -->
                        <div class="w-40 h-40 rounded-xl overflow-hidden border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-100">
                            <img id="classImagePreview" src="{{ asset('default-image.png') }}" alt="پیش‌نمایش تصویر" class="w-full h-full object-cover hidden">
                            <span id="placeholderText" class="text-gray-400 text-center">تصویر خود را انتخاب کنید</span>
                        </div>

                        <!-- input فایل -->
                        <input type="file" name="image" id="classImageInput"
                               class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition cursor-pointer">
                    </div>
                </div>

                <script>
                    const classImageInput = document.getElementById('classImageInput');
                    const classImagePreview = document.getElementById('classImagePreview');
                    const placeholderText = document.getElementById('placeholderText');

                    classImageInput.addEventListener('change', function() {
                        const file = this.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                classImagePreview.src = e.target.result;
                                classImagePreview.classList.remove('hidden');
                                placeholderText.classList.add('hidden');
                            }
                            reader.readAsDataURL(file);
                        } else {
                            classImagePreview.classList.add('hidden');
                            placeholderText.classList.remove('hidden');
                        }
                    });
                </script>

                <!-- دکمه‌ها -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('admin.group_class.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-xl hover:bg-gray-400 transition font-semibold">
                        لغو
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition font-semibold shadow-lg hover:scale-105">
                        ذخیره
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection


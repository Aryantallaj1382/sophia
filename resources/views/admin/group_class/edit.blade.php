@extends('admin.layouts.app')

@section('content')
    <div class="container mx-auto p-8">
        <div class="max-w-4xl mx-auto bg-white shadow-2xl rounded-3xl p-10">
            <h2 class="text-4xl font-extrabold text-center text-gray-800 mb-10">✏️ ویرایش کلاس گروهی</h2>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc pr-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.group_class.update', $groupClass->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- استاد و زبان -->
                <div class="grid md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-xl">
                    <div>
                        <label class="block font-semibold mb-2">استاد</label>
                        <select name="professor_id" class="w-full rounded-xl border-gray-300">
                            @foreach($professors as $professor)
                                <option value="{{ $professor->id }}" {{ $groupClass->professor_id == $professor->id ? 'selected' : '' }}>
                                    {{ $professor->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">زبان</label>
                        <select name="language_id" class="w-full rounded-xl border-gray-300">
                            @foreach($languages as $lang)
                                <option value="{{ $lang->id }}" {{ $groupClass->language_id == $lang->id ? 'selected' : '' }}>
                                    {{ $lang->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- سطح و موضوع -->
                <div class="grid md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-xl">
                    <div>
                        <label class="block font-semibold mb-2">سطح</label>
                        <select name="language_level_id" class="w-full rounded-xl border-gray-300">
                            @foreach($levels as $level)
                                <option value="{{ $level->id }}" {{ $groupClass->language_level_id == $level->id ? 'selected' : '' }}>
                                    {{ $level->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">موضوع</label>
                        <select name="subject_id" class="w-full rounded-xl border-gray-300">
                            @foreach($subgoals as $sub)
                                <option value="{{ $sub->id }}" {{ $groupClass->subject_id == $sub->id ? 'selected' : '' }}>
                                    {{ $sub->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- گروه سنی و پلتفرم -->
                <div class="grid md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-xl">
                    <div>
                        <label class="block font-semibold mb-2">گروه سنی</label>
                        <select name="age_group_id" class="w-full rounded-xl border-gray-300">
                            @foreach($ageGroups as $age)
                                <option value="{{ $age->id }}" {{ $groupClass->age_group_id == $age->id ? 'selected' : '' }}>
                                    {{ $age->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">پلتفرم</label>
                        <select name="platform_id" class="w-full rounded-xl border-gray-300">
                            @foreach($platforms as $platform)
                                <option value="{{ $platform->id }}" {{ $groupClass->platform_id == $platform->id ? 'selected' : '' }}>
                                    {{ $platform->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- کتاب -->
                <div class="bg-gray-50 p-6 rounded-xl">
                    <label class="block font-semibold mb-2">کتاب (اختیاری)</label>
                    <select name="book_id" class="w-full rounded-xl border-gray-300">
                        <option value="">بدون کتاب</option>
                        @foreach($books as $item)
                            <option value="{{ $item->id }}" {{ $groupClass->book_id == $item->id ? 'selected' : '' }}>
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- ظرفیت‌ها -->
                <div class="grid md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-xl">
                    <div>
                        <label class="block font-semibold mb-2">حداقل ظرفیت</label>
                        <input type="number" name="min_capacity" value="{{ $groupClass->min_capacity }}" class="w-full rounded-xl border-gray-300">
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">حداکثر ظرفیت</label>
                        <input type="number" name="max_capacity" value="{{ $groupClass->max_capacity }}" class="w-full rounded-xl border-gray-300">
                    </div>
                </div>

                <!-- تعداد جلسات و مدت -->
                <div class="grid md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-xl">
                    <div>
                        <label class="block font-semibold mb-2">تعداد جلسات</label>
                        <input type="number" name="sessions_count" value="{{ $groupClass->sessions_count }}" class="w-full rounded-xl border-gray-300">
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">مدت هر جلسه (ساعت)</label>
                        <input type="number" name="hourly" value="{{ $groupClass->hourly }}" class="w-full rounded-xl border-gray-300">
                    </div>
                </div>

                <!-- تاریخ و لینک کلاس -->
                <div class="grid md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-xl">
                    <div>
                        <label class="block font-semibold mb-2">تاریخ شروع</label>
                        <input type="date" name="start_date" value="{{ $groupClass->start_date }}" class="w-full rounded-xl border-gray-300">
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">تاریخ پایان</label>
                        <input type="date" name="end_date" value="{{ $groupClass->end_date }}" class="w-full rounded-xl border-gray-300">
                    </div>
                </div>

                <!-- روزهای هفته و زمان -->
                <div class="bg-gray-50 p-6 rounded-xl">
                    <label class="block font-semibold mb-2">روزهای هفته</label>
                    @php
                        $days = [
                            'Saturday' => 'شنبه',
                            'Sunday' => 'یک‌شنبه',
                            'Monday' => 'دو‌شنبه',
                            'Tuesday' => 'سه‌شنبه',
                            'Wednesday' => 'چهار‌شنبه',
                            'Thursday' => 'پنج‌شنبه',
                            'Friday' => 'جمعه',
                        ];
                        $selectedDays = $groupClass->schedules->pluck('day')->toArray();
                        $startTime = $groupClass->schedules->first()->start_time ?? '';
                        $endTime   = $groupClass->schedules->first()->end_time ?? '';
                    @endphp
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach($days as $en => $fa)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="days[]" value="{{ $en }}" {{ in_array($en, $selectedDays) ? 'checked' : '' }}>
                                <span>{{ $fa }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="mt-4 grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block font-semibold mb-2">ساعت شروع</label>
                            <input type="time" name="start_time" value="{{ $startTime }}" class="w-full rounded-xl border-gray-300">
                        </div>
                        <div>
                            <label class="block font-semibold mb-2">ساعت پایان</label>
                            <input type="time" name="end_time" value="{{ $endTime }}" class="w-full rounded-xl border-gray-300">
                        </div>
                    </div>
                </div>

                <!-- تصویر -->
                <div class="bg-gray-50 p-6 rounded-xl">
                    <label class="block font-semibold mb-2">تصویر کلاس</label>
                    <input type="file" name="image" class="w-full rounded-xl border-gray-300">
                    @if($groupClass->image)
                        <img src="{{ asset($groupClass->image) }}" alt="" class="w-40 mt-3 rounded-xl">
                    @endif
                </div>

                <!-- لینک کلاس -->
                <div class="bg-gray-50 p-6 rounded-xl">
                    <label class="block font-semibold mb-2">لینک کلاس</label>
                    <input type="text" name="class_link" value="{{ $groupClass->class_link }}" class="w-full rounded-xl border-gray-300">
                </div>

                <!-- دکمه -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('admin.group_class.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-xl hover:bg-gray-400">لغو</a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700">بروزرسانی</button>
                </div>

            </form>
        </div>
    </div>
@endsection

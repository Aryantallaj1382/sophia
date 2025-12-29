@extends('admin.layouts.app')

@section('title', 'جزئیات کلاس رزرو شده')

@section('content')
    <div class="max-w-4xl mx-auto p-6 bg-white shadow-md rounded-lg space-y-6">

        {{-- فرم بروزرسانی لینک کلاس --}}
        <div class="mt-6 p-4 border rounded bg-gray-50">
            <h3 class="text-lg font-semibold mb-2">لینک کلاس آنلاین</h3>
            @if(session('success'))
                <p class="text-green-600 mb-2">{{ session('success') }}</p>
            @endif
            <form action="{{ route('admin.private-classes.update-link', $reservations->id) }}" method="POST" class="flex flex-col md:flex-row gap-2">
                @csrf
                <input type="url" name="class_link" placeholder="لینک کلاس را وارد کنید"
                       value="{{ old('class_link', $reservations->class_link) }}"
                       class="flex-1 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                    بروزرسانی
                </button>
            </form>
            @error('class_link')
            <p class="text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex items-center space-x-4 border-b pb-4">
            <img src="{{ $reservations->professor->user->profile ?? 'https://ui-avatars.com/api/?name='.$reservations->professor->user->name }}"
                 alt="استاد" class="w-16 h-16 rounded-full object-cover">
            <div>
                <h2 class="text-xl font-bold">{{ $reservations->professor->user->name ?? 'نامشخص' }}</h2>
                <p class="text-gray-500">استاد</p>
            </div>
        </div>

        {{-- دانش‌آموز --}}
        <div class="flex items-center space-x-4 border-b pb-4">
            <img src="{{ $reservations?->user?->profile ?? 'https://ui-avatars.com/api/?name='.$reservations?->user?->name }}"
                 alt="دانش‌آموز" class="w-16 h-16 rounded-full object-cover">
            <div>
                <h2 class="text-xl font-bold">{{ $reservations?->user?->name }}</h2>
                <p class="text-gray-500">دانش‌آموز</p>
            </div>
        </div>

        {{-- جزئیات کلاس --}}
        <div class="space-y-3">
            <h3 class="text-lg font-semibold border-b pb-2">اطلاعات کلاس</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <p>نوع کلاس: <span class="font-medium">{{ ucfirst($reservations->class_type) }}</span></p>
                <p>تعداد جلسات: <span class="font-medium">{{ $reservations->sessions_count }}</span></p>
                <p>وضعیت: <span class="capitalize font-medium text-blue-600">{{ $reservations->status }}</span></p>
                <p>پلتفرم: <span class="font-medium">{{ $reservations->platform?->title ?? 'نامشخص' }}</span></p>
                <p>سطح زبان: <span class="font-medium">{{ $reservations->languageLevel?->title ?? 'نامشخص' }}</span></p>
                <p>گروه سنی: <span class="font-medium">{{ $reservations->ageGroup?->title ?? 'نامشخص' }}</span></p>
                <p>مهارت: <span class="font-medium">{{ $reservations->skill?->name ?? 'نامشخص' }}</span></p>
                <p>هدف فرعی: <span class="font-medium">{{ $reservations->subgoal?->title ?? 'نامشخص' }}</span></p>
            </div>
        </div>

        {{-- زمان جلسات --}}
        <div class="space-y-3">
            <h3 class="text-lg font-semibold border-b pb-2">جلسات کلاس</h3>
            @if($reservations->timeSlots->isNotEmpty())
                <ul class="space-y-2">
                    @foreach($reservations->timeSlots as $slot)
                        @php
                            $isCancelled = $slot->cancel_by !== null;
                        @endphp
                        <li class="p-3 border rounded flex flex-col md:flex-row justify-between items-start md:items-center
                           {{ $isCancelled ? 'bg-red-50 border-red-400' : 'hover:bg-gray-50 transition' }}">

                            <div class="flex flex-col md:flex-row gap-4 items-start md:items-center">
                                <span class="font-medium">{{$slot->date}}</span>
                                <span>{{ $slot->time }}</span>
                                <span>جلسه {{ $slot->session_number }}</span>
                            </div>

                            @if($isCancelled)
                                <div class="mt-2 md:mt-0 text-red-600">
                                    <p>کنسل شده توسط: <span class="font-medium">{{ ucfirst($slot->cancel_by) }}</span></p>
                                    <p>دلیل: <span class="font-medi um">{{ $slot->cancel_reason }}</span></p>
                                    @if($slot->cancel_reason_file)
                                        <p>فایل دلیل:
                                            <a href="{{ asset($slot->cancel_reason_file) }}"
                                               target="_blank" class="underline text-blue-600">
                                                مشاهده فایل
                                            </a>
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500">هیچ جلسه‌ای برای این کلاس ثبت نشده است.</p>
            @endif
        </div>


        {{-- دکمه بازگشت --}}
        <div class="text-right">
            <a href="{{ route('admin.private-classes.index') }}"
               class="inline-block bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 transition">
                بازگشت به لیست کلاس‌ها
            </a>
        </div>
    </div>
    {{-- فرم ویرایش اطلاعات کلاس --}}
    <div class="max-w-4xl mx-auto p-6 bg-white shadow-md rounded-lg space-y-6 mt-10">
        <h3 class="text-lg font-semibold mb-4">ویرایش اطلاعات کلاس</h3>

        <form action="{{ route('admin.private-classes.update', $reservations->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            @method('PUT')

            {{-- وضعیت کلاس --}}
            <div>
                <label class="text-sm font-medium">وضعیت</label>
                <select name="status" class="w-full border px-3 py-2 rounded">
                    <option value="pending" {{ $reservations->status == 'pending' ? 'selected' : '' }}>در انتظار</option>
                    <option value="confirmed" {{ $reservations->status == 'confirmed' ? 'selected' : '' }}>تأیید شده</option>
                    <option value="canceled" {{ $reservations->status == 'canceled' ? 'selected' : '' }}>کنسل شده</option>
                </select>
            </div>

            {{-- تعداد جلسات --}}
            <div>
                <label class="text-sm font-medium">تعداد جلسات</label>
                <input type="number" name="sessions_count" value="{{ $reservations->sessions_count }}" class="w-full border px-3 py-2 rounded">
            </div>

            {{-- نوع کلاس --}}
            <div>
                <label class="text-sm font-medium">نوع کلاس</label>
                <select name="class_type" class="w-full border px-3 py-2 rounded">
                    <option value="placement" {{ $reservations->class_type == 'placement' ? 'selected' : '' }}>تعیین سطح</option>
                    <option value="trial" {{ $reservations->class_type == 'trial' ? 'selected' : '' }}>آزمایشی</option>
                    <option value="sessional" {{ $reservations->class_type == 'sessional' ? 'selected' : '' }}>چند جلسه ای</option>
                </select>
            </div>

            {{-- پلتفرم --}}
            <div>
                <label class="text-sm font-medium">پلتفرم</label>
                <select name="platform_id" class="w-full border px-3 py-2 rounded">
                    @foreach($platforms as $item)
                        <option value="{{ $item->id }}" {{ $reservations->platform_id == $item->id ? 'selected' : '' }}>
                            {{ $item->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- سطح زبان --}}
            <div>
                <label class="text-sm font-medium">سطح زبان</label>
                <select name="language_level_id" class="w-full border px-3 py-2 rounded">
                    @foreach($levels as $level)
                        <option value="{{ $level->id }}" {{ $reservations->language_level_id == $level->id ? 'selected' : '' }}>
                            {{ $level->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- گروه سنی --}}
            <div>
                <label class="text-sm font-medium">گروه سنی</label>
                <select name="age_group_id" class="w-full border px-3 py-2 rounded">
                    @foreach($ageGroups as $grp)
                        <option value="{{ $grp->id }}" {{ $reservations->age_group_id == $grp->id ? 'selected' : '' }}>
                            {{ $grp->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- مهارت --}}
            <div>
                <label class="text-sm font-medium">مهارت</label>
                <select name="skill_id" class="w-full border px-3 py-2 rounded">
                    @foreach($skills as $skill)
                        <option value="{{ $skill->id }}" {{ $reservations->skill_id == $skill->id ? 'selected' : '' }}>
                            {{ $skill->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-medium">استاد</label>
                <select name="professor_id" class="w-full border px-3 py-2 rounded">
                    @foreach($professors as $professor)
                        <option value="{{ $professor->id }}" {{ $reservations->professor_id == $professor->id ? 'selected' : '' }}>
                            {{ $professor->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-medium">دانش آموز</label>
                <select name="user_id" class="w-full border px-3 py-2 rounded">
                    @foreach($students as $student)
                        <option value="{{ $student->user_id }}" {{ $reservations->user_id == $student->user_id ? 'selected' : '' }}>
                            {{ $student->first_name.' '.$student->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>


            {{-- هدف فرعی --}}
            <div>
                <label class="text-sm font-medium">هدف فرعی</label>
                <select name="subgoal_id" class="w-full border px-3 py-2 rounded">
                    @foreach($subgoals as $sub)
                        <option value="{{ $sub->id }}" {{ $reservations->subgoal_id == $sub->id ? 'selected' : '' }}>
                            {{ $sub->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- دکمه ذخیره --}}
            <div class="col-span-1 md:col-span-2 text-left">

                <button class="px-5 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                    ذخیره تغییرات
                </button>
            </div>
        </form>
    </div>

@endsection

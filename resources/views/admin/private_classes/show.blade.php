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
            <img src="{{ $reservations->user->profile ?? 'https://ui-avatars.com/api/?name='.$reservations->user->name }}"
                 alt="دانش‌آموز" class="w-16 h-16 rounded-full object-cover">
            <div>
                <h2 class="text-xl font-bold">{{ $reservations->user->name }}</h2>
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
@endsection

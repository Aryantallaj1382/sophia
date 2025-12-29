@extends('admin.layouts.app')

@section('title', 'لیست کلاس‌های رزرو شده')

@section('content')
    <form method="GET" class="p-6">
        <div class="flex gap-3 items-center">
            <select name="status" class="border p-2 rounded text-sm">
                <option value="all">همه وضعیت‌ها</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>در انتظار</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>تأیید شده</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>کنسل شده</option>
            </select>

            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                فیلتر
            </button>
        </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
        @foreach($reservations as $res)
            <div class="bg-white shadow-md rounded-lg overflow-hidden hover:shadow-xl transition relative">
                {{-- تصویر پروفایل استاد --}}
                <div class="flex items-center p-4 border-b">
                    <img src="{{ $res->professor->user->profile ?? 'https://ui-avatars.com/api/?name='.$res->professor->user->name }}"
                         alt="استاد"
                         class="w-12 h-12 rounded-full object-cover mr-3">
                    <div>
                        <h3 class="text-md p-3 font-semibold">{{ $res->professor->user->name ?? 'نامشخص' }}</h3>
                        <p class="text-sm text-gray-500">استاد</p>
                    </div>
                </div>

                {{-- تصویر پروفایل دانش‌آموز --}}
                <div class="flex items-center p-4 border-b">
                    <img src="{{ $res?->user?->profile ?? 'https://ui-avatars.com/api/?name='.$res?->user?->name }}"
                         alt="دانش‌آموز"
                         class="w-12 h-12 rounded-full object-cover mr-3">
                    <div>
                        <h3 class="text-md p-3 font-semibold">{{ $res?->user?->name }}</h3>
                        <p class="text-sm text-gray-500">دانش‌آموز</p>
                    </div>
                </div>

                {{-- جزئیات کلاس --}}
                <div class="p-4 space-y-2">
                    <p>نوع کلاس: <span class="font-medium">{{ ucfirst($res->class_type) }}</span></p>
                    <p>تعداد جلسات: <span class="font-medium">{{ $res->sessions_count }}</span></p>
                    <p>وضعیت: <span class="capitalize font-medium text-blue-600">{{ $res->status }}</span></p>
                </div>

                {{-- دکمه جزئیات --}}
                <div class="p-4 border-t">
                    <a href="{{ route('admin.private-classes.show', $res->id) }}"
                       class="w-full inline-block text-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        جزئیات کلاس
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@extends('admin.layouts.app')

@section('content')
    <div class="container mx-auto p-6">

        <h2 class="text-3xl font-extrabold text-gray-800 mb-8 text-center">
            📘 جزئیات کلاس: {{ $class->name }}
        </h2>

        <div class="bg-white/80 backdrop-blur-md shadow-xl rounded-2xl overflow-hidden border border-gray-200">
            <!-- هدر تصویر -->
            <div class="relative">
                <img src="{{ $class->image ?? asset('images/default-class.jpg') }}"
                     alt="{{ $class->name }}"
                     class="w-full h-72 object-cover">

                <div class="absolute bottom-0 w-full bg-gradient-to-t from-black/70 to-transparent p-4">
                    <h3 class="text-2xl font-bold text-white">{{ $class->name }}</h3>
                </div>
            </div>

            <!-- اطلاعات -->
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-700">
                <div class="space-y-3">
                    <p><span class="font-semibold">👨‍🏫 استاد:</span> {{ $class->professor->user->name ?? '---' }}</p>
                    <p><span class="font-semibold">🌐 زبان:</span> {{ $class->language->title ?? '---' }}</p>
                    <p><span class="font-semibold">📚 سطح:</span> {{ $class->level->title ?? '---' }}</p>
                </div>
                <div class="space-y-3">
                    <p><span class="font-semibold">👥 گروه سنی:</span> {{ $class->ageGroup->title ?? '---' }}</p>
                    <p><span class="font-semibold">💻 پلتفرم:</span> {{ $class->platform->title ?? '---' }}</p>
                    <p><span class="font-semibold">⭐ امتیاز میانگین:</span>
                        <span class="text-yellow-500">
                            {{ number_format($class->ratings()->avg('rating'), 1) ?? '---' }} / 5
                        </span>
                    </p>
                </div>
            </div>

            <!-- زمان‌بندی -->
            <div class="px-6 pb-6">
                <h4 class="text-lg font-bold text-gray-800 mb-3">⏰ زمان‌بندی کلاس:</h4>
                @if($class->schedules->count())
                    <ul class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        @foreach($class->schedules as $schedule)
                            <li x-data="{ open: false }" class="bg-gray-100 rounded-lg px-4 py-2 shadow-sm flex items-center justify-between">
                                <span class="font-medium">{{ $schedule->date->format('d , M') }}</span>
                                <span class="text-gray-600">
        {{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}
    </span>

                                <button @click="open = true" class="text-blue-600 hover:underline ml-3">ویرایش</button>

                                <!-- Modal -->
                                <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                                    <div class="bg-white rounded-xl p-6 w-80">
                                        <h3 class="text-lg font-bold mb-4">ویرایش زمان</h3>
                                        <form action="{{route('admin.group_class.updateSchedule', $schedule)}}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="mb-3">
                                                <label class="block font-medium mb-1">ساعت شروع</label>
                                                <input type="time" name="start_time" value="{{ $schedule->start_time->format('H:i') }}" class="w-full border rounded p-2">
                                            </div>
                                            <div class="mb-3">
                                                <label class="block font-medium mb-1">ساعت پایان</label>
                                                <input type="time" name="end_time" value="{{ $schedule->end_time->format('H:i') }}" class="w-full border rounded p-2">
                                            </div>
                                            <div class="flex justify-end gap-2">
                                                <button type="button" @click="open=false" class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">لغو</button>
                                                <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">ذخیره</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 italic">برنامه‌ای ثبت نشده.</p>
                @endif
            </div>


            <!-- دکمه‌ها -->
            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-3">
                <a href="{{ route('admin.group_class.edit', $class) }}"
                   class="px-5 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition shadow">
                    ✏️ ویرایش
                </a>
                <a href="{{ route('admin.group_class.groupClassReservations', $class) }}"
                   class="px-5 py-2 bg-green-600 text-white rounded-xl hover:bg-blue-700 transition shadow">
                  لیست دانش آموزان
                </a>
            </div>

        </div>

    </div>
@endsection

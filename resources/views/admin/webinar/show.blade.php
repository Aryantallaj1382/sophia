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
                    <p><span class="font-semibold">📚 روز:</span> {{ $class->date?->format('Y , m , d') ?? '---' }}</p>
                    <p><span class="font-semibold">🌐 زبان:</span> {{ $class->language->title ?? '---' }}</p>
                    <p><span class="font-semibold">📚 سطح:</span> {{ $class->languageLevel->title ?? '---' }}</p>
                </div>
                <div class="space-y-3">
                    <p><span class="font-semibold">👥 گروه سنی:</span> {{ $class->ageGroup->title ?? '---' }}</p>
                    <p><span class="font-semibold">👥 ساعت:</span> {{ $class->time?->format('H:i') ?? '---' }}</p>
                    <p><span class="font-semibold">💻 پلتفرم:</span> {{ $class->platform->title ?? '---' }}</p>
                    <p><span class="font-semibold">⭐ امتیاز میانگین:</span>
                        <span class="text-yellow-500">
                            {{ number_format($class->ratings()->avg('rating'), 1) ?? '---' }} / 5
                        </span>
                    </p>
                </div>
            </div>


            <!-- دکمه‌ها -->
            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-3">
                <a href="{{ route('admin.group_class.edit', $class) }}"
                   class="px-5 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition shadow">
                    ✏️ ویرایش
                </a>

            </div>
        </div>

    </div>
@endsection

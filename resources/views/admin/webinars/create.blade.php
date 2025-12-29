@extends('admin.layouts.app')

@section('title', 'افزودن وبینار')

@section('content')
    <div class="container mx-auto p-8">
        <div class="max-w-4xl mx-auto bg-white shadow-2xl rounded-3xl p-10 backdrop-blur-sm border border-gray-200">
            <h2 class="text-4xl font-extrabold text-center text-gray-800 mb-10">🎥 افزودن وبینار جدید</h2>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc pr-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.webinar.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <!-- استاد، زبان و موضوع -->
                <div class="grid md:grid-cols-3 gap-6 bg-gray-50 p-6 rounded-xl">
                    <div>
                        <label class="block font-semibold mb-2">استاد <span class="text-red-500">*</span></label>
                        <select name="professor_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">
                            @foreach($professors as $professor)
                                <option value="{{ $professor->id }}">{{ $professor->user->name ?? 'بدون نام' }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div>
                        <label class="block font-semibold mb-2">زبان <span class="text-red-500">*</span></label>
                        <select name="language_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">
                            @foreach($languages as $language)
                                <option value="{{ $language->id }}">{{ $language->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold mb-2">موضوع <span class="text-red-500">*</span></label>
                        <select name="subject_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="bg-gray-50 p-6 rounded-xl">
                    <label class="block font-semibold mb-2">گروه سنی <span class="text-red-500">*</span></label>
                    <select name="age_group_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400" required>
                        @foreach($ageGroups as $age)
                            <option value="{{ $age->id }}">{{ $age->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="bg-gray-50 p-6 rounded-xl">
                    <label class="block font-semibold mb-2">سطح زبان <span class="text-red-500">*</span></label>
                    <select name="language_level_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400" required>
                        @foreach($languageLevels as $age)
                            <option value="{{ $age->id }}">{{ $age->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="bg-gray-50 p-6 rounded-xl">
                    <label class="block font-semibold mb-2">پلتفرم ها <span class="text-red-500">*</span></label>
                    <select name="platform_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400" required>
                        @foreach($platforms as $age)
                            <option value="{{ $age->id }}">{{ $age->title }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- تاریخ و ساعت -->
                <div class="grid md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-xl">
                    <div>
                        <label class="block font-semibold mb-2">تاریخ برگزاری</label>
                        <input type="date" name="date" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">ساعت برگزاری</label>
                        <input type="time" name="time" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">
                    </div>
                </div>

                <!-- لینک کلاس -->
                <div class="bg-gray-50 p-6 rounded-xl">
                    <label class="block font-semibold mb-2">لینک وبینار</label>
                    <input type="text" name="class_link" placeholder="https://example.com"
                           class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">
                </div>
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

                <div class="bg-gray-50 p-6 rounded-xl">
                    <label class="block font-semibold mb-2">کتاب (اختیاری)</label>
                    <select name="book_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">
                        <option value="">بدون کتاب</option>
                        @foreach($books as $item)
                            <option value="{{ $item->id }}" data-img="{{ asset($item->image) }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- تصویر -->
                <div class="bg-gray-50 p-6 rounded-xl shadow-md border border-gray-200">
                    <label class="block font-semibold mb-4 text-gray-700">تصویر وبینار</label>
                    <div class="flex flex-col items-center justify-center gap-4">
                        <div class="w-40 h-40 rounded-xl overflow-hidden border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-100">
                            <img id="webinarImagePreview" src="{{ asset('default-image.png') }}" alt="پیش‌نمایش تصویر" class="w-full h-full object-cover hidden">
                            <span id="placeholderText" class="text-gray-400 text-center">تصویر خود را انتخاب کنید</span>
                        </div>

                        <input type="file" name="image" id="webinarImageInput"
                               class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition cursor-pointer">
                    </div>
                </div>

                <script>
                    const webinarImageInput = document.getElementById('webinarImageInput');
                    const webinarImagePreview = document.getElementById('webinarImagePreview');
                    const placeholderText = document.getElementById('placeholderText');

                    webinarImageInput.addEventListener('change', function() {
                        const file = this.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                webinarImagePreview.src = e.target.result;
                                webinarImagePreview.classList.remove('hidden');
                                placeholderText.classList.add('hidden');
                            }
                            reader.readAsDataURL(file);
                        } else {
                            webinarImagePreview.classList.add('hidden');
                            placeholderText.classList.remove('hidden');
                        }
                    });
                </script>

                <!-- دکمه‌ها -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('admin.webinar.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-xl hover:bg-gray-400 transition font-semibold">
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

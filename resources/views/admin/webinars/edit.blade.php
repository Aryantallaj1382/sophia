@extends('admin.layouts.app')

@section('title', 'ویرایش وبینار')

@section('content')
    <div class="container mx-auto p-8">
        <div class="max-w-4xl mx-auto bg-white shadow-2xl rounded-3xl p-10 backdrop-blur-sm border border-gray-200">
            <h2 class="text-4xl font-extrabold text-center text-gray-800 mb-10">✏️ ویرایش وبینار</h2>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc pr-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.webinar.update', $webinar->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- استاد، زبان، موضوع -->
                <div class="grid md:grid-cols-3 gap-6 bg-gray-50 p-6 rounded-xl">
                    <div>
                        <label class="block font-semibold mb-2">استاد</label>
                        <select name="professor_id" class="w-full rounded-xl border-gray-300 shadow-sm">
                            @foreach($professors as $professor)
                                <option value="{{ $professor->id }}" {{ $webinar->professor_id == $professor->id ? 'selected' : '' }}>
                                    {{ $professor->user->name ?? 'بدون نام' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold mb-2">زبان</label>
                        <select name="language_id" class="w-full rounded-xl border-gray-300 shadow-sm">
                            @foreach($languages as $language)
                                <option value="{{ $language->id }}" {{ $webinar->language_id == $language->id ? 'selected' : '' }}>
                                    {{ $language->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold mb-2">موضوع</label>
                        <select name="subject_id" class="w-full rounded-xl border-gray-300 shadow-sm">
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ $webinar->subject_id == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- گروه سنی -->
                <div class="bg-gray-50 p-6 rounded-xl">
                    <label class="block font-semibold mb-2">گروه سنی</label>
                    <select name="age_group_id" class="w-full rounded-xl border-gray-300 shadow-sm">
                        @foreach($ageGroups as $age)
                            <option value="{{ $age->id }}" {{ $webinar->age_group_id == $age->id ? 'selected' : '' }}>
                                {{ $age->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- سطح زبان -->
                <div class="bg-gray-50 p-6 rounded-xl">
                    <label class="block font-semibold mb-2">سطح زبان</label>
                    <select name="language_level_id" class="w-full rounded-xl border-gray-300 shadow-sm">
                        @foreach($languageLevels as $level)
                            <option value="{{ $level->id }}" {{ $webinar->language_level_id == $level->id ? 'selected' : '' }}>
                                {{ $level->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- پلتفرم -->
                <div class="bg-gray-50 p-6 rounded-xl">
                    <label class="block font-semibold mb-2">پلتفرم</label>
                    <select name="platform_id" class="w-full rounded-xl border-gray-300 shadow-sm">
                        @foreach($platforms as $platform)
                            <option value="{{ $platform->id }}" {{ $webinar->platform_id == $platform->id ? 'selected' : '' }}>
                                {{ $platform->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- تاریخ و ساعت -->
                <div class="grid md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-xl">
                    <div>
                        <label class="block font-semibold mb-2">تاریخ برگزاری</label>
                        <input type="date" name="date" value="{{ $webinar->date ? $webinar->date->format('Y-m-d') : '' }}" class="w-full rounded-xl border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">ساعت برگزاری</label>
                        <input type="time" name="time" value="{{ $webinar->time ? $webinar->time->format('H:i') : '' }}" class="w-full rounded-xl border-gray-300 shadow-sm">
                    </div>
                </div>

                <!-- ظرفیت -->
                <div class="grid md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-xl">
                    <div>
                        <label class="block font-semibold mb-2">حداقل ظرفیت</label>
                        <input type="number" name="min_capacity" value="{{ $webinar->min_capacity }}" class="w-full rounded-xl border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">حداکثر ظرفیت</label>
                        <input type="number" name="max_capacity" value="{{ $webinar->max_capacity }}" class="w-full rounded-xl border-gray-300 shadow-sm">
                    </div>
                </div>

                <!-- لینک کلاس -->
                <div class="bg-gray-50 p-6 rounded-xl">
                    <label class="block font-semibold mb-2">لینک وبینار</label>
                    <input type="text" name="class_link" value="{{ $webinar->class_link }}" class="w-full rounded-xl border-gray-300 shadow-sm">
                </div>

                <!-- تصویر -->
                <div class="bg-gray-50 p-6 rounded-xl">
                    <label class="block font-semibold mb-4">تصویر وبینار</label>
                    @if($webinar->image)
                        <img src="{{ $webinar->image }}" class="w-48 h-48 rounded-xl mb-4 object-cover border">
                    @endif
                    <input type="file" name="image" class="w-full rounded-xl border-gray-300 shadow-sm">
                </div>

                <!-- دکمه‌ها -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('admin.webinar.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-xl hover:bg-gray-400 transition font-semibold">
                        لغو
                    </a>
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition font-semibold shadow-lg hover:scale-105">
                        ذخیره تغییرات
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

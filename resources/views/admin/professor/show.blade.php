@extends('admin.layouts.app')

@section('title', 'جزئیات استاد')

@section('content')
    <div class="bg-white shadow-xl rounded-lg p-8 max-w-5xl mx-auto">
        <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
            <!-- تصویر پروفایل -->
            <div class="flex-shrink-0">
                <img src="{{ $professor->user->profile ?? 'i1.png' }}"
                     class="w-40 h-40 rounded-full object-cover shadow-md border-4 border-green-400"
                     alt="Profile">
            </div>

            <!-- اطلاعات اصلی -->
            <div class="flex-1 space-y-3">
                <h2 class="text-3xl font-bold text-gray-800">{{ $professor->name }}</h2>
                <p class="text-gray-600">{{ $professor->bio ?? 'بدون توضیح' }}</p>

                <div class="grid grid-cols-2 gap-4 mt-4">
                    <p><span class="font-semibold">ایمیل:</span> {{ $professor?->user?->email }}</p>
                    <p><span class="font-semibold">موبایل:</span> {{ $professor->user?->mobile }}</p>
                    <p><span class="font-semibold">جنسیت:</span>
                        {{ $professor->gender === 'male' ? 'مرد' : 'زن' }}
                    </p>
                    <p><span class="font-semibold">سابقه تدریس:</span> {{ $professor->years_of_experience }} سال</p>
                    <p><span class="font-semibold">تاریخ تولد:</span> {{ $professor->birth_date }}</p>
                    <p><span class="font-semibold">وضعیت:</span>
                        @if($professor->is_active)
                            <span class="text-green-600 font-bold">فعال</span>
                        @else
                            <span class="text-red-600 font-bold">غیرفعال</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- ویدیوها -->
        <div class="mt-10 grid md:grid-cols-2 gap-6">
            @if($professor->sample_video)
                <div class="bg-gray-100 rounded-lg p-4 shadow">
                    <h3 class="font-semibold mb-2">ویدیو نمونه تدریس</h3>
                    <video controls class="w-full rounded">
                        <source src="{{ asset($professor->sample_video) }}" type="video/mp4">
                    </video>
                </div>
            @endif

            @if($professor->teaching_video)
                <div class="bg-gray-100 rounded-lg p-4 shadow">
                    <h3 class="font-semibold mb-2">ویدیو معرفی</h3>
                    <video controls class="w-full rounded">
                        <source src="{{ asset($professor->teaching_video) }}" type="video/mp4">
                    </video>
                </div>
            @endif
        </div>

        <!-- جزئیات بیشتر -->
        <div class="mt-10">
            <h3 class="text-xl font-bold mb-4">اطلاعات تکمیلی</h3>
            <div class="grid md:grid-cols-2 gap-6">

                <div class="bg-white border rounded-lg p-4 shadow">
                    <h4 class="font-semibold mb-2">گروه‌های سنی</h4>
                    <ul class="list-disc list-inside text-gray-600">
                        @foreach($professor->ageGroups as $item)
                            <li>{{ $item->title }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="bg-white border rounded-lg p-4 shadow">
                    <h4 class="font-semibold mb-2">سطوح زبان</h4>
                    <ul class="list-disc list-inside text-gray-600">
                        @foreach($professor->languageLevels as $item)
                            <li>{{ $item->title }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="bg-white border rounded-lg p-4 shadow">
                    <h4 class="font-semibold mb-2">پلتفرم‌ها</h4>
                    <ul class="list-disc list-inside text-gray-600">
                        @foreach($professor->platforms as $item)
                            <li>{{ $item->title }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="bg-white border rounded-lg p-4 shadow">
                    <h4 class="font-semibold mb-2">لهجه‌ها</h4>
                    <ul class="list-disc list-inside text-gray-600">
                        @foreach($professor->accents as $item)
                            <li>{{ $item->title }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="bg-white border rounded-lg p-4 shadow">
                    <h4 class="font-semibold mb-2">مهارت‌ها</h4>
                    <ul class="list-disc list-inside text-gray-600">
                        @foreach($professor->skills as $item)
                            <li>{{ $item->name }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="bg-white border rounded-lg p-4 shadow">
                    <h4 class="font-semibold mb-2">کتاب‌ها</h4>
                    <div class="flex flex-wrap gap-3">
                        @foreach($professor->books as $item)
                            <div class="flex items-center gap-2 bg-gray-50 p-2 rounded shadow">
                                <img src="{{ asset($item->image) }}" class="w-10 h-10 rounded object-cover" alt="">
                                <span>{{ $item->name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white border rounded-lg p-4 shadow md:col-span-2">
                    <h4 class="font-semibold mb-2">اهداف یادگیری</h4>
                    <ul class="list-disc list-inside text-gray-600">
                        @foreach($professor->learningGoals as $goal)
                            <li>{{ $goal->subgoal->title ?? '' }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- دکمه برگشت -->
        <div class="mt-8">
            <a href="{{ route('admin.professors.index') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded shadow">
                بازگشت به لیست اساتید
            </a>
        </div>
    </div>
@endsection

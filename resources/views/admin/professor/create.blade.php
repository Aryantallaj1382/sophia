@extends('admin.layouts.app')

@section('title', 'ایجاد استاد جدید')

@section('content')
    <h2 class="text-2xl font-bold mb-6">ایجاد استاد جدید</h2>
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pr-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.professors.store') }}" method="POST" enctype="multipart/form-data"
          class="bg-white shadow rounded-lg p-6 space-y-4">
        @csrf

        <div class="border rounded overflow-hidden">
            <button type="button"
                    class="w-full text-left px-4 py-2 bg-gray-100 font-medium flex justify-between items-center accordion-btn">
                اطلاعات پایه
                <span class="accordion-icon">+</span>
            </button>
                <div class="accordion-content px-4 py-4 bg-white">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <input type="text" name="name" placeholder="نام استاد"
                               class="w-full border rounded p-2 text-right placeholder-gray-400">
                        <input type="email" name="email" placeholder="ایمیل"
                               class="w-full border rounded p-2 text-right placeholder-gray-400">
                        <input type="password" name="password" placeholder="رمز عبور"
                               class="w-full border rounded p-2 text-right placeholder-gray-400">
                        <input type="password" name="password_confirmation" placeholder="تکرار رمز عبور"
                               class="w-full border rounded p-2 text-right placeholder-gray-400">
                        <input type="text" name="mobile" placeholder="شماره تلفن"
                               class="w-full border rounded p-2 text-right placeholder-gray-400">
                        <input type="text" name="bio" placeholder="بیو"
                               class="w-full border rounded p-2 text-right placeholder-gray-400">
                        <input type="text" name="years_of_experience" placeholder="سابقه تدریس"
                               class="w-full border rounded p-2 text-right placeholder-gray-400">

                        <input type="hidden" name="is_active" value="0">
                        <label class="flex items-center gap-2 border rounded p-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="w-4 h-4">
                            <span>فعال باشد</span>
                        </label>


                        <label class="flex items-center gap-2 border rounded p-2 cursor-pointer">
                            <input type="checkbox" name="is_verified" class="w-4 h-4">
                            <span>تایید شده</span>
                        </label>
                        <label class="flex items-center gap-2 border rounded p-2 cursor-pointer">
                            <input type="checkbox" name="is_native" class="w-4 h-4">
                            <span>استاد محلی است</span>
                        </label>
                        <div class="col-span-1 md:col-span-1 border rounded p-4">
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="gender" value="male" class="w-4 h-4">
                                    <span>مرد</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="gender" value="female" class="w-4 h-4">
                                    <span>زن</span>
                                </label>
                            </div>
                        </div>
                        <!-- فیلدهای آپلود ویدیو -->

                        <!-- فیلد انتخاب تاریخ -->
                        <div class="col-span-1 md:col-span-1">
                            <label class="block mb-2 font-medium text-gray-700">تاریخ تولد</label>
                            <input type="date" name="birth_date" class="w-full border rounded p-2 text-right"
                                   placeholder="تاریخ تولد">
                        </div>
                        <div class="col-span-1 md:col-span-1">
                            <label for="options" class="block mb-2 font-medium text-gray-700">تعیین سطح</label>
                            <select id="options" name="placement" class="border rounded px-3 py-2 w-full">
                                <option value="">-- انتخاب تعیین سطح --</option>
                                <option value="null">برگزار نمیکند</option>
                                <option value="0">0 درصد تخفیف</option>
                                <option value="50">50 درصد تخفیف</option>
                                <option value="100"> 100 درصد تخفیف</option>
                            </select>
                        </div>
                        <div class="col-span-1 md:col-span-1">
                            <label for="options" class="block mb-2 font-medium text-gray-700">کلاس آزمایشی</label>
                            <select id="options" name="trial" class="border rounded px-3 py-2 w-full">
                                <option value="">-- انتخاب کلاس آزمایشی --</option>
                                <option value="null">برگزار نمیکند</option>

                                <option value="0">0 درصد تخفیف</option>
                                <option value="50">50 درصد تخفیف</option>
                                <option value="100"> 100 درصد تخفیف</option>
                            </select>
                        </div>

                        <label
                            class="flex flex-col items-center justify-center border-2 border-dashed rounded-lg p-4 text-gray-600 cursor-pointer hover:border-green-500 hover:text-green-500">
                            <span>آپلود ویدیو نمونه تدریس</span>
                            <input type="file" name="sample_video" accept="video/*" class="hidden">
                        </label>
                        <label
                            class="flex flex-col items-center justify-center border-2 border-dashed rounded-lg p-4 text-gray-600 cursor-pointer hover:border-green-500 hover:text-green-500">
                            <span>آپلود ویدیو معرفی</span>
                            <input type="file" name="teaching_video" accept="video/*" class="hidden">
                        </label>

                        <!-- فیلدهای آپلود عکس -->
                        <label
                            class="flex flex-col items-center justify-center border-2 border-dashed rounded-lg p-4 text-gray-600 cursor-pointer hover:border-blue-500 hover:text-blue-500">
                            <span>آپلود عکس  نمونه تدریس</span>
                            <input type="file" name="sample_video_cover" accept="image/*" class="hidden">
                        </label>
                        <label
                            class="flex flex-col items-center justify-center border-2 border-dashed rounded-lg p-4 text-gray-600 cursor-pointer hover:border-blue-500 hover:text-blue-500">
                            <span>آپلود عکس  معرفی</span>
                            <input type="file" name="teaching_video_cover" accept="image/*" class="hidden">
                        </label>
                        <label
                            class="flex flex-col items-center justify-center border-2 border-dashed rounded-lg p-4 text-gray-600 cursor-pointer hover:border-blue-500 hover:text-blue-500">
                            <span>آپلود عکس  پروفایل</span>
                            <input type="file" name="profile" accept="image/*" class="hidden">
                        </label>
                        <button type="submit"
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded col-span-1 md:col-span-2">
                            ثبت استاد
                        </button>
                    </div>
                </div>

        </div>
        <div class="border rounded ">
            <button type="button"
                    class="w-full text-left px-4 py-2 bg-gray-100 font-medium flex justify-between items-center accordion-btn">
                اطلاعات بیشتر
                <span class="accordion-icon">+</span>
            </button>
            <div class="accordion-content px-4 py-4 bg-white">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div class="col-span-1 md:col-span-1">
                        <label for="age_groups" class="block mb-2 font-medium text-gray-700">گروه‌های سنی</label>
                        <select id="age_groups" name="age_groups[]" multiple class="w-full border rounded px-3 py-2 text-right">
                            @foreach($age as $item)
                                <option value="{{ $item->id }}">{{ $item->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-1 md:col-span-1">
                        <label for="level" class="block mb-2 font-medium text-gray-700">سطج زبان</label>
                        <select id="level" name="level[]" multiple class="w-full border rounded px-3 py-2 text-right">
                            @foreach($level as $item)
                                <option value="{{ $item->id }}">{{ $item->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-1 md:col-span-1">
                        <label for="platform" class="block mb-2 font-medium text-gray-700">پلتفرم ها</label>
                        <select id="platform" name="platform[]" multiple class="w-full border rounded px-3 py-2 text-right">
                            @foreach($platform as $item)
                                <option value="{{ $item->id }}">{{ $item->title }}</option>
                            @endforeach
                        </select>
                    </div>



                    <div class="col-span-1 md:col-span-1">
                        <label for="accent" class="block mb-2 font-medium text-gray-700">لهجه ها</label>
                        <select id="accent" name="accent[]" multiple class="w-full border rounded px-3 py-2 text-right">
                            @foreach($accent as $item)
                                <option value="{{ $item->id }}">{{ $item->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-1 md:col-span-1">
                        <label for="skill" class="block mb-2 font-medium text-gray-700">مهارت ها</label>
                        <select id="skill" name="skill[]" multiple class="w-full border rounded px-3 py-2 text-right">
                            @foreach($skill as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-1 md:col-span-1">
                        <label for="books" class="block mb-2 font-medium text-gray-700">انتخاب کتاب‌ها</label>
                        <select id="books" name="books[]" multiple class="w-full border rounded px-3 py-2 text-right">
                            @foreach($book as $item)
                                <option value="{{ $item->id }}" data-img="{{ asset($item->image) }}">
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-1 md:col-span-1">
                        <label for="learning_goals" class="block mb-2 font-medium text-gray-700">اهداف یادگیری</label>
                        <select  id="learning_goals" name="learning_goals[]" multiple class="w-full border rounded px-3 py-2 text-right">
                            @foreach($goals as $goal)
                                <optgroup label="{{ $goal->title }}">
                                    @foreach($goal->subgoals as $sub)
                                        <option value="{{ $sub->id }}">{{ $sub->title }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded col-span-1 md:col-span-2">
                        ثبت استاد
                    </button>
                </div>
            </div>

        </div>

    </form>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new TomSelect("#learning_goals", {
                plugins: ['remove_button'],
                placeholder: "یک یا چند هدف یادگیری را انتخاب کنید",
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            new TomSelect("#age_groups", {
                plugins: ['remove_button'], // دکمه حذف برای هر انتخاب
                placeholder: "یک یا چند گروه سنی را انتخاب کنید",
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            new TomSelect("#level", {
                plugins: ['remove_button'], // دکمه حذف برای هر انتخاب
                placeholder: "یک یا چند گروه سنی را انتخاب کنید",
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            new TomSelect("#platform", {
                plugins: ['remove_button'], // دکمه حذف برای هر انتخاب
                placeholder: "یک یا چند گروه سنی را انتخاب کنید",
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            new TomSelect("#accent", {
                plugins: ['remove_button'], // دکمه حذف برای هر انتخاب
                placeholder: "یک یا چند گروه سنی را انتخاب کنید",
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            new TomSelect("#skill", {
                plugins: ['remove_button'], // دکمه حذف برای هر انتخاب
                placeholder: "یک یا چند گروه سنی را انتخاب کنید",
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            new TomSelect("#books", {
                plugins: ['remove_button'],
                placeholder: "یک یا چند کتاب را انتخاب کنید",
                render: {
                    option: function(data, escape) {
                        return `<div class="flex items-center gap-2">
                                <img src="${escape(data.img)}" class="w-8 h-8 rounded object-cover" alt="">
                                <span>${escape(data.text)}</span>
                            </div>`;
                    },
                    item: function(data, escape) {
                        return `<div class="flex items-center gap-2">
                                <img src="${escape(data.img)}" class="w-6 h-6 rounded object-cover" alt="">
                                <span>${escape(data.text)}</span>
                            </div>`;
                    }
                }
            });
        });
    </script>


    <script>
        document.querySelectorAll('.accordion-btn').forEach(button => {
            button.addEventListener('click', () => {
                const content = button.nextElementSibling;
                const icon = button.querySelector('.accordion-icon');

                // بستن همه آکاردئون‌ها
                document.querySelectorAll('.accordion-content').forEach(c => {
                    if (c !== content) {
                        c.classList.add('hidden');
                        c.previousElementSibling.querySelector('.accordion-icon').textContent = '+';
                    }
                });

                // باز/بسته کردن همین یکی
                content.classList.toggle('hidden');
                icon.textContent = content.classList.contains('hidden') ? '+' : '-';
            });
        });
    </script>
@endsection

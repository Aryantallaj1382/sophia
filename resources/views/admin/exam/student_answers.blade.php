@extends('admin.layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto py-10">

        <!-- Header -->
        <div class="bg-white shadow rounded-xl p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">
                پاسخ‌های دانش‌آموز:
                <span class="text-indigo-600">{{ $student->first_name.' '. $student->last_name}}</span>
            </h2>

            <p class="text-lg text-gray-600">
                آزمون: <span class="font-semibold text-gray-800">{{ $exam->name }}</span>
            </p>
        </div>

        @foreach ($questions as $q)
            @php $answer = $q->answers->first(); @endphp

            <div class="bg-white shadow-md rounded-xl mb-6 overflow-hidden">

                <!-- Question Header -->
                <div class="px-6 py-4 bg-gray-100 border-b flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">سؤال {{ $loop->iteration }}</p>
                        <h3 class="text-lg font-bold text-gray-800">{{ $q->title }}</h3>
                    </div>
                </div>

                <!-- Question Body -->
                <div class="px-6 py-5">

                    @if (!$answer)
                        <div class="p-4 bg-red-100 text-red-700 rounded-lg text-sm">
                            این دانش‌آموز پاسخی ثبت نکرده است.
                        </div>

                    @else

                        <!-- متن پاسخ -->
                        @if ($answer->text_answer)
                            <div class="mb-4">
                                <p class="font-semibold text-gray-700 mb-1">پاسخ متنی:</p>
                                <div class="p-3 bg-gray-50 border rounded-lg text-gray-800 leading-relaxed">
                                    {{ $answer->text_answer }}
                                </div>
                            </div>
                        @endif

                        <!-- فایل -->
                        @if ($answer->file)
                            <div class="mb-4">
                                <p class="font-semibold text-gray-700 mb-1">فایل ارسال‌شده:</p>
                                <a href="{{ $answer->file }}"
                                   target="_blank"
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition">
                                    دانلود فایل
                                </a>
                            </div>
                        @endif

                        <!-- گزینه انتخاب‌شده -->
                        @if ($answer->options->count())
                            <div class="mb-4">
                                <p class="font-semibold text-gray-700 mb-2">گزینه انتخاب‌شده:</p>

                                <div class="space-y-2">
                                    @foreach ($answer->options as $op)
                                        <div class="p-3 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-800">
                                            {{ $op->option->text ?? 'گزینه یافت نشد' }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- نوع سوال -->
                        @if ($answer->variant)
                            <div class="mt-4">
                                <p class="font-semibold text-gray-700">
                                    نوع سؤال:
                                    <span class="text-gray-800">{{ $answer->variant->title }}</span>
                                </p>
                            </div>
                        @endif

                    @endif

                </div>
            </div>

        @endforeach
    </div>
@endsection

@extends('admin.layouts.app')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc pr-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">📖 ایجاد کتاب جدید</h1>
            <a href="{{ route('admin.books.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-medium rounded-lg shadow-sm transition transform hover:scale-105">
                بازگشت
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6 max-w-4xl mx-auto">
            <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                {{-- نام کتاب --}}
                <div>
                    <label class="block mb-1 font-semibold text-gray-700">نام کتاب</label>
                    <input type="text" name="name" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block mb-1 font-semibold text-gray-700">فرمت فایل</label>
                    <select name="title_file"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="" disabled {{ old('file_type') ? '' : 'selected' }}>انتخاب کنید</option>
                        <option value="pdf" {{ old('file_type') == 'pdf' ? 'selected' : '' }}>PDF</option>
                        <option value="epub" {{ old('file_type') == 'epub' ? 'selected' : '' }}>EPUB</option>
                        <option value="mobi" {{ old('file_type') == 'mobi' ? 'selected' : '' }}>MOBI</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1 font-semibold text-gray-700">نوع فایل</label>
                    <select name="book_type"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="" selected disabled>نوع کتاب</option>
                        <option value="Students Book">Students Book</option>
                        <option value="Teachers Book">Teachers Book</option>
                        <option value="Workbook">Workbook</option>
                    </select>
                </div>



                {{-- نویسنده --}}
                <div>
                    <label class="block mb-1 font-semibold text-gray-700">نویسنده</label>
                    <input type="text" name="author"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                {{-- ادیشن و حجم --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-semibold text-gray-700">ادیشن</label>
                        <input type="number" name="edition" min="1"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block mb-1 font-semibold text-gray-700">جلد (Volume)</label>
                        <input type="number" name="volume" min="1"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                {{-- موضوعات --}}
                <div>
                    <label class="block mb-1 font-semibold text-gray-700">موضوعات (JSON Array یا کاما جدا)</label>
                    <input type="text" name="topics"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder='مثلاً: ["ماجراجویی","فانتزی"]'>
                </div>

                {{-- توضیحات --}}
                <div>
                    <label class="block mb-1 font-semibold text-gray-700">توضیحات</label>
                    <textarea name="description" rows="4"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
                <div>
                    <label class="block mb-1 font-semibold text-gray-700">انتشارات</label>
                    <input type="text" name="publication"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block mb-1 font-semibold text-gray-700">لینک فایل های ویدیویی</label>
                    <input type="text" name="video_file"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block mb-1 font-semibold text-gray-700">لینک فایل های صوتی</label>
                    <input type="text" name="audio_file"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                {{-- تصویر --}}
                <div>
                    <label class="block mb-1 font-semibold text-gray-700">تصویر جلد</label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                {{-- فایل ویدیویی --}}
                <div>
                    <label class="block mb-1 font-semibold text-gray-700">ویدیو (اختیاری)</label>
                    <input type="file" name="video" accept="video/*"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                {{-- فایل کتاب --}}
                <div>
                    <label class="block mb-1 font-semibold text-gray-700">فایل کتاب</label>
                    <input type="file" name="file"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="mb-4">
                    <label class="block mb-1 font-semibold text-gray-700">صفحات نمونه (چند تصویر)</label>
                    <input type="file" name="sample_pages[]" multiple
                           class="w-full border-gray-300 rounded-lg px-3 py-2 bg-gray-50 focus:ring focus:ring-indigo-200 focus:border-indigo-400">
                    <p class="text-sm text-gray-500 mt-1">می‌توانید چند تصویر انتخاب کنید.</p>
                </div>

                {{-- دکمه ثبت --}}
                <div class="flex justify-end mt-4">
                    <button type="submit"
                            class="px-6 py-2 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg shadow transition transform hover:scale-105">
                        ایجاد کتاب
                    </button>
                </div>


            </form>
        </div>
    </div>
@endsection

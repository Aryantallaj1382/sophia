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
            <h1 class="text-2xl font-bold text-gray-800">✏️ ویرایش کتاب</h1>
            <a href="{{ route('admin.books.index') }}"
               class="px-4 py-2 bg-indigo-500 text-white rounded-lg">
                بازگشت
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6 max-w-4xl mx-auto">
            <form action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                {{-- نام کتاب --}}
                <div>
                    <label class="block mb-1 font-semibold">نام کتاب</label>
                    <input type="text" name="name" value="{{ old('name', $book->name) }}" required class="input">
                </div>

                {{-- فرمت فایل --}}
                <div>
                    <label class="block mb-1 font-semibold">فرمت فایل</label>
                    <select name="title_file" class="input">
                        <option value="">انتخاب کنید</option>
                        @foreach(['pdf','epub','mobi'] as $type)
                            <option value="{{ $type }}" @selected(old('title_file', $book->title_file) == $type)>
                                {{ strtoupper($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- نوع کتاب --}}
                <div>
                    <label class="block mb-1 font-semibold">نوع کتاب</label>
                    <select name="book_type" class="input">
                        @foreach(['Students Book','Teachers Book','Workbook'] as $type)
                            <option value="{{ $type }}" @selected(old('book_type', $book->book_type) == $type)>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- نویسنده --}}
                <div>
                    <label class="block mb-1 font-semibold">نویسنده</label>
                    <input type="text" name="author" value="{{ old('author', $book->author) }}" class="input">
                </div>

                {{-- ادیشن و جلد --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-semibold">ادیشن</label>
                        <input type="number" name="edition" value="{{ old('edition', $book->edition) }}" class="input">
                    </div>
                    <div>
                        <label class="block mb-1 font-semibold">جلد</label>
                        <input type="number" name="volume" value="{{ old('volume', $book->volume) }}" class="input">
                    </div>
                </div>

                {{-- موضوعات --}}
                <div>
                    <label class="block mb-1 font-semibold">موضوعات</label>
                    <input type="text" name="topics"
                           value="{{ old('topics', implode(',', $book->topics ?? [])) }}"
                           class="input">
                </div>

                {{-- توضیحات --}}
                <div>
                    <label class="block mb-1 font-semibold">توضیحات</label>
                    <textarea name="description" rows="4" class="input">{{ old('description', $book->description) }}</textarea>
                </div>
                <div>
                <label class="block mb-1 font-semibold text-gray-700">انتشارات</label>
                <input type="text" name="publication" value="{{ old('publication', $book->publication) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
                <div>
                    <label class="block mb-1 font-semibold text-gray-700">لینک فایل های ویدیویی</label>
                    <input type="text" name="video_file"
                           value="{{ old('video_file', $book->video_file) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block mb-1 font-semibold text-gray-700">لینک فایل های صوتی</label>
                    <input type="text" name="audio_file" value="{{ old('audio_file', $book->audio_file) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                {{-- تصویر --}}
                <div>
                    <label class="block mb-1 font-semibold">تصویر جلد</label>
                    <input type="file" name="image" class="input">
                    @if($book->image)
                        <img src="{{ asset($book->image) }}" class="h-24 mt-2 rounded">
                    @endif
                </div>

                {{-- ویدیو --}}
                <div>
                    <label class="block mb-1 font-semibold">ویدیو</label>
                    <input type="file" name="video" class="input">
                </div>

                {{-- فایل کتاب --}}
                <div>
                    <label class="block mb-1 font-semibold">فایل کتاب</label>
                    <input type="file" name="file" class="input">
                </div>

                {{-- صفحات نمونه --}}
                <div>
                    <label class="block mb-1 font-semibold">افزودن صفحات نمونه</label>
                    <input type="file" name="sample_pages[]" multiple class="input">
                </div>

                <div class="flex justify-end">
                    <button class="px-6 py-2 bg-green-500 text-white rounded-lg">
                        بروزرسانی کتاب
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection

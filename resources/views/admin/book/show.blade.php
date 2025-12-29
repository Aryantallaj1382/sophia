@extends('admin.layouts.app')

@section('content')
    <div class="p-6 space-y-6 bg-gray-50">

        {{-- اطلاعات کتاب --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-300 bg-white rounded-t-lg shadow">
            <h1 class="text-lg font-bold text-gray-800">
                <span class="text-indigo-600">{{ $book->name }}</span>
            </h1>
            <div class="flex items-center gap-2">
                {{-- حذف --}}
                <form action="{{ route('admin.books.destroy', $book->id) }}" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-2xl shadow-md transition transform hover:scale-105">
                        حذف
                    </button>
                </form>

                {{-- ویرایش --}}
                <a href="{{ route('admin.books.edit',$book) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-2xl shadow-md transition transform hover:scale-105">
                    ویرایش
                </a>

                <a href="{{ route('admin.books.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-medium rounded-2xl shadow-md transition transform hover:scale-105">
                    بازگشت
                </a>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-6 bg-white rounded-b-lg shadow p-6">
            {{-- تصویر کتاب --}}
            <div class="w-full lg:w-1/3 flex justify-center items-center bg-gray-100 rounded-lg overflow-hidden">
                <img src="{{ $book->image }}" alt="{{ $book->name }}" class="max-w-full max-h-full object-contain">
            </div>

            {{-- اطلاعات جزئیات کتاب --}}
            <div class="flex-1 space-y-4 text-gray-800">
                <h1 class="text-2xl font-bold">{{ $book->name }}</h1>

                @if($book->description)
                    <p class="text-gray-600">{{ $book->description }}</p>
                @endif

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <p><span class="font-semibold">عنوان فایل:</span> {{ $book->title_file ?? '-' }}</p>
                    <p><span class="font-semibold">نویسنده:</span> {{ $book->author ?? '-' }}</p>
                    <p><span class="font-semibold">ادیشن:</span> {{ $book->edition ?? '-' }}</p>
                    <p><span class="font-semibold">حجم (Volume):</span> {{ $book->volume ?? '-' }}</p>
                    <p><span class="font-semibold">تعداد بازدید:</span> {{ $book->view_count ?? 0 }}</p>
                    <p><span class="font-semibold">فایل ویدیویی:</span>
                        @if($book->video)
                            <a href="{{ url('public/'.$book->video) }}" target="_blank" class="text-blue-600 hover:underline">دانلود/مشاهده</a>
                        @else
                            -
                        @endif
                    </p>
                    <p><span class="font-semibold">فایل کتاب:</span>
                        @if($book->file)
                            <a href="{{ url('public/'.$book->file) }}" target="_blank" class="text-blue-600 hover:underline">دانلود</a>
                        @else
                            -
                        @endif
                    </p>
                    <p><span class="font-semibold">موضوعات:</span>
                        @if($book->topics)
                            {{ implode(', ', $book->topics) }}
                        @else
                            -
                        @endif
                    </p>
                </div>

                {{-- نمونه صفحات --}}
                @if($book->samplePages->count())
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2">نمونه صفحات:</h3>
                        <div class="flex gap-2 overflow-x-auto">
                            @foreach($book->samplePages as $page)
                                <img src="{{ $page->image }}" class="h-32 rounded border" alt="Sample Page">
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection

@extends('admin.layouts.app')

@section('title', 'افزودن استوری جدید')

@section('content')
    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow p-6 mt-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">📤 افزودن استوری جدید</h1>

        @if ($errors->any())
            <div class="mb-4 bg-red-100 text-red-700 p-3 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.stories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">عکس کاور</label>
                <input type="file" name="cover_image"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ویدیو</label>
                <input type="file" name="video"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="main_page" id="main_page" value="1" checked
                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="main_page" class="text-sm text-gray-700">نمایش در صفحه اصلی</label>
            </div>

            <div class="pt-3">
                <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                    ذخیره استوری
                </button>
            </div>
        </form>
    </div>
@endsection

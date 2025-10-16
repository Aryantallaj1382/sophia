@extends('admin.layouts.app')

@section('content')
    <div class="container mx-auto p-6">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif
        <div class="flex justify-start md:justify-end">
            <a href="{{ route('admin.blogs.create') }}"
               class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg shadow">
                ایجاد بلاگ جدید
            </a>
        </div>

        <h2 class="text-2xl font-bold text-gray-700 mb-6">لیست بلاگ‌ها</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($blogs as $blog)
                <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden flex flex-col">
                    <!-- عکس بلاگ -->
                    <img src="{{ $blog->image ?? asset('images/default-blog.jpg') }}"
                         alt="{{ $blog->title }}"
                         class="w-full h-40 object-cover">

                    <!-- اطلاعات -->
                    <div class="p-4 flex flex-col flex-1">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">
                            {{ $blog->title }}
                        </h3>

                        <p class="text-sm text-gray-600 mb-3 line-clamp-3">
                            {{ Str::limit(strip_tags($blog->content), 100) }}
                        </p>

                        <div class="mt-auto">
                            <div class="flex justify-between items-center text-xs text-gray-500 mb-3">
                <span class="bg-blue-100 text-blue-600 px-2 py-1 rounded">
                    بازدید: {{ $blog->views }}
                </span>
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded">
                    زمان: {{ $blog->reading_time }} دقیقه
                </span>
                            </div>

                            <div class="flex justify-between items-center">
                                <a href="{{ route('admin.blogs.edit', $blog->id) }}"
                                   class="px-3 py-1 bg-yellow-500 text-white text-sm rounded hover:bg-yellow-600 transition">
                                    ویرایش
                                </a>
                                <form action="{{ route('admin.blogs.destroy', $blog->id) }}" method="POST"
                                      onsubmit="return confirm('آیا مطمئن هستید؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1 bg-red-500 text-white text-sm rounded hover:bg-red-600 transition">
                                        حذف
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <p class="col-span-4 text-center text-gray-500">هیچ بلاگی موجود نیست.</p>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $blogs->links() }}
        </div>
    </div>
@endsection

@extends('admin.layouts.app')

@section('content')
    <div class="container mx-auto p-6">
        @if(session('success'))
            <div
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 3000)"
                class="mb-4 p-4 rounded-lg bg-green-100 border border-green-400 text-green-700 flex justify-between items-center"
            >
                <span>{{ session('success') }}</span>
                <button @click="show = false" class="text-green-700 hover:text-green-900 font-bold">&times;</button>
            </div>
        @endif
        <div class="flex justify-start md:justify-end">
            <a href="{{ route('admin.webinar.create') }}"
               class="bg-green-500 hover:bg-green-600 text-white p-2 rounded">
                ایجاد کلاس جدید
            </a>
        </div>
        <h2 class="text-2xl font-bold text-gray-700 mb-6">لیست کلاس‌ها</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($classes as $class)
                <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">
                    <!-- عکس کلاس -->
                    <img src="{{ $class->image ?? asset('images/default-class.jpg') }}"
                         alt="{{ $class->name }}"
                         class="w-full h-40 object-cover">

                    <!-- اطلاعات -->
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">
                            {{ $class->name }}
                        </h3>

                        <p class="text-sm text-gray-500 mb-3">
                            استاد: {{ $class->professor->user->name ?? '---' }}
                        </p>

                        <div class="flex justify-between items-center gap-2">
                            <a href="{{ route('admin.webinar.show', $class->id) }}"
                               class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition">
                                مشاهده
                            </a>

                            <a href="{{ route('admin.webinar.edit', $class) }}"
                               class="px-3 py-1 bg-yellow-500 text-white text-sm rounded hover:bg-yellow-600 transition">
                                ویرایش
                            </a>

                            <form action="{{ route('admin.webinar.destroy', $class) }}" method="POST"
                                  onsubmit="return confirm('آیا از حذف این کلاس مطمئن هستید؟')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition">
                                    حذف
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="col-span-4 text-center text-gray-500">هیچ کلاسی موجود نیست.</p>
            @endforelse
        </div>

        <div class="mt-6">

            {{ $classes->links() }}
        </div>

    </div>
@endsection

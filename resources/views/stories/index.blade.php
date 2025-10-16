@extends('admin.layouts.app')

@section('content')
    <div class="container mx-auto p-6" x-data="{ showAddModal: false, selectedVideo: null }">

        <h2 class="text-2xl font-bold text-gray-700 mb-6">استوری‌های استاد: {{ $professor->user->name }}</h2>

        <button @click="showAddModal = true"
                class="px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition mb-4">
            + افزودن استوری جدید
        </button>

        <div class="overflow-x-auto bg-white shadow rounded-lg">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border">کاور</th>
                    <th class="px-4 py-2 border">ویدیو</th>
                    <th class="px-4 py-2 border">عملیات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($stories as $story)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border text-center">
                            <img src="{{ $story->cover_image }}" class="w-24 h-16 object-cover rounded shadow">
                        </td>
                        <td class="px-4 py-2 border text-center">
                            <button @click="selectedVideo = '{{ $story->video }}'"
                                    class="px-3 py-1 bg-blue-600 text-white rounded shadow hover:bg-blue-700 transition">
                                مشاهده ویدیو
                            </button>
                        </td>
                        <td class="px-4 py-2 border text-center">
                            <form action="{{ route('admin.professorsStory.destroy', [$professor, $story]) }}" method="POST"
                                  onsubmit="return confirm('آیا مطمئن هستید؟')" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button class="px-3 py-1 bg-red-600 text-white rounded shadow hover:bg-red-700 transition">
                                    حذف
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-6 text-gray-500">
                            هیچ استوری‌ای موجود نیست.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $stories->links() }}
        </div>

        <!-- مدال افزودن استوری -->
        <div x-show="showAddModal"
             x-transition.opacity
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div @click.away="showAddModal = false"
                 class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6 relative">

                <button @click="showAddModal = false"
                        class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>

                <h3 class="text-xl font-semibold mb-4 text-gray-700">افزودن استوری جدید</h3>

                <form action="{{ route('admin.professorsStory.store', $professor) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block mb-2 font-medium text-gray-600">کاور استوری</label>
                        <input type="file" name="cover_image" accept="image/*"
                               class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-green-300">
                    </div>

                    <div>
                        <label class="block mb-2 font-medium text-gray-600">ویدیو استوری</label>
                        <input type="file" name="video" accept="video/*"
                               class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-green-300">
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showAddModal = false"
                                class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">
                            انصراف
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition">
                            ذخیره
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- مدال نمایش ویدیو -->
        <div x-show="selectedVideo"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div @click.away="selectedVideo = null" class="bg-white rounded-xl shadow-lg w-11/12 md:w-2/3 lg:w-1/2 p-6 relative">
                <button @click="selectedVideo = null"
                        class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
                <video class="w-full rounded" controls autoplay>
                    <source :src="selectedVideo" type="video/mp4">
                </video>
            </div>
        </div>

    </div>
@endsection

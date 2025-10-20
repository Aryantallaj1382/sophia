@extends('admin.layouts.app')

@section('title', 'استوری‌های صفحه اصلی')

@section('content')
    <div class="p-6">
        <a href="{{ route('admin.stories.create') }}"
           class="inline-block mb-6 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
            ➕ افزودن استوری جدید
        </a>

        <h1 class="text-2xl font-bold mb-6 text-gray-800">🎥 استوری‌های صفحه اصلی</h1>

        @if($stories->isEmpty())
            <div class="text-center text-gray-500">
                هیچ استوری‌ای برای صفحه اصلی وجود ندارد.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($stories as $story)
                    <div class="bg-white shadow rounded-xl overflow-hidden hover:shadow-lg transition cursor-pointer"
                         onclick="openVideoModal('{{ $story->video }}')">

                        <div class="relative">
                            @if($story->cover_image)
                                <img src="{{ $story->cover_image }}" alt="Story Cover"
                                     class="w-full h-40 object-cover">
                            @else
                                <div class="w-full h-40 flex items-center justify-center bg-gray-100 text-gray-400">
                                    بدون تصویر
                                </div>
                            @endif

                            @if($story->video)
                                <div class="absolute top-2 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded">
                                    🎬 ویدیو دارد
                                </div>
                            @endif
                        </div>

                        <div class="p-4">
                            <form action="{{ route('admin.stories.destroy', $story->id) }}" method="POST" onsubmit="return confirm('آیا مطمئن هستید که می‌خواهید این استوری را حذف کنید؟')" class="mt-2 text-center">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700 transition">
                                    🗑 حذف
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ✅ مودال نمایش ویدیو --}}
    <div id="videoModal" class="fixed inset-0 bg-black/80 flex items-center justify-center hidden z-50">
        <div class="relative w-full max-w-2xl px-4">
            <video id="storyVideo" controls class="w-full rounded-xl shadow-lg"></video>
            <button onclick="closeVideoModal()"
                    class="absolute -top-10 right-2 text-white text-3xl font-bold hover:text-red-400">
                &times;
            </button>
        </div>
    </div>

    {{-- ✅ اسکریپت نمایش مودال --}}
    <script>
        function openVideoModal(videoUrl) {
            if (!videoUrl) return;
            const modal = document.getElementById('videoModal');
            const video = document.getElementById('storyVideo');
            video.src = videoUrl;
            modal.classList.remove('hidden');
            video.play();
        }

        function closeVideoModal() {
            const modal = document.getElementById('videoModal');
            const video = document.getElementById('storyVideo');
            video.pause();
            video.src = '';
            modal.classList.add('hidden');
        }

        // بستن مودال با کلیک روی پس‌زمینه
        document.getElementById('videoModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeVideoModal();
            }
        });
    </script>
@endsection

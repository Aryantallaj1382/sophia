@extends('admin.layouts.app')

@section('title', 'مدیریت اسلایدرها')

@section('content')
    <div class="p-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">مدیریت اسلایدرها</h2>
            <button onclick="openModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                + افزودن اسلایدر جدید
            </button>
        </div>

        <!-- Success Alert -->
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <!-- Errors -->
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-md">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Slider List -->
        @if($sliders->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                @foreach($sliders as $slider)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden relative hover:shadow-lg transition">
                        <img src="{{ $slider->image }}" class="w-full h-44 object-cover">

                        <!-- Delete button -->
                        <form action="{{ route('admin.sliders.destroy', $slider->id) }}" method="POST"
                              onsubmit="return confirm('آیا از حذف این اسلایدر مطمئن هستید؟')"
                              class="absolute top-2 left-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white p-1.5 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </form>

                        <div class="p-3 text-center">
                            @if($slider->link)
                                <a href="{{ $slider->link }}" target="_blank"
                                   class="block text-blue-600 hover:underline text-sm font-medium">
                                    مشاهده لینک
                                </a>
                            @else
                                <span class="text-gray-400 text-sm">بدون لینک</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center text-gray-500 mt-10">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 16.5V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v9a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 16.5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5l9 6 9-6"/>
                </svg>
                هیچ اسلایدری ثبت نشده است.
            </div>
        @endif

    </div>

    <!-- Modal -->
    <div id="addModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6 relative animate-fadeIn">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">افزودن اسلایدر جدید</h3>
            <form action="{{ route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">تصویر اسلایدر <span class="text-red-500">*</span></label>
                    <input type="file" name="image" accept="image/*" required
                           class="block w-full text-sm border rounded-md p-2 focus:ring focus:ring-blue-200">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">لینک (اختیاری)</label>
                    <input type="text" name="link" placeholder="https://example.com"
                           class="block w-full text-sm border rounded-md p-2 focus:ring focus:ring-blue-200">
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal()"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">انصراف</button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">افزودن</button>
                </div>
            </form>
            <button onclick="closeModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600">
                ✕
            </button>
        </div>
    </div>

    <!-- Tailwind Modal Animation -->
    <style>
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95);} to { opacity: 1; transform: scale(1);} }
        .animate-fadeIn { animation: fadeIn 0.2s ease-out; }
    </style>

    <!-- JS -->
    <script>
        function openModal() {
            document.getElementById('addModal').classList.remove('hidden');
        }
        function closeModal() {
            document.getElementById('addModal').classList.add('hidden');
        }
    </script>
@endsection

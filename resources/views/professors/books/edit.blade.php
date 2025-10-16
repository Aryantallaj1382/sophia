@extends('admin.layouts.app')

@section('content')
    <div class="container mx-auto p-6" x-data="{ showBooksModal: false }">

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-700">کتاب‌های استاد</h2>
            <button @click="showBooksModal = true"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                📚 انتخاب کتاب‌ها
            </button>
        </div>

        <!-- لیست کتاب‌های استاد -->
        <div class="overflow-x-auto bg-white shadow rounded-lg">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border">تصویر</th>
                    <th class="px-4 py-2 border">نام کتاب</th>
                </tr>
                </thead>
                <tbody>
                @forelse($professor->books as $book)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border text-center">
                            <img src="{{ asset($book->image) }}" class="w-16 h-16 object-cover rounded shadow">
                        </td>
                        <td class="px-4 py-2 border text-center text-gray-700 font-medium">
                            {{ $book->name }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center py-6 text-gray-500">
                            این استاد کتابی ندارد.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- مدال انتخاب کتاب‌ها -->
        <div x-show="showBooksModal"
             x-transition.opacity
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div @click.away="showBooksModal = false"
                 class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6 relative">

                <!-- دکمه بستن -->
                <button @click="showBooksModal = false"
                        class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>

                <h3 class="text-xl font-semibold mb-4 text-gray-700">انتخاب کتاب‌های استاد</h3>

                <form action="{{ route('admin.professorsBook.update', $professor->id) }}" method="POST" class="space-y-4">
                    @csrf


                    <!-- لیست کتاب‌ها -->
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-72 overflow-y-auto pr-1">
                        @foreach($books as $book)
                            <label class="flex items-center gap-2 p-2 border rounded-lg hover:shadow cursor-pointer bg-gray-50"
                                   >
                                <input type="checkbox" name="books[]" value="{{ $book->id }}"
                                       class="w-4 h-4 text-blue-600 rounded"
                                    {{ in_array($book->id, $professor->books->pluck('id')->toArray()) ? 'checked' : '' }}>
                                <img src="{{ asset($book->image) }}" class="w-10 h-10 rounded object-cover border" alt="">
                                <span class="text-sm font-medium text-gray-700">{{ $book->name }}</span>
                            </label>
                        @endforeach
                    </div>

                    <!-- دکمه‌ها -->
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" @click="showBooksModal = false"
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

    </div>
@endsection

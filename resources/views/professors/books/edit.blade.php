@extends('admin.layouts.app')

@section('content')
    <div class="container mx-auto p-6" x-data="{ showBooksModal: false }">

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-700">کتاب‌های استاد</h2>
            <button id="openBooksModalBtn"
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


        <dialog id="bookModal" class="p-6 rounded-xl shadow-2xl w-full max-w-lg bg-white">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">انتخاب کتاب‌ها</h2>
                <!-- دکمه بستن -->
                <button id="closeBooksModalBtn" class="text-2xl font-bold">&times;</button>
            </div>

            <!-- دکمه انتخاب همه -->
            <button id="selectAllBooksBtn"
                    class="mb-4 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition text-sm font-medium">
                انتخاب همه
            </button>

            <!-- لیست کتاب‌ها -->
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @foreach($books as $book)
                    <label class="flex items-center space-x-3 space-x-reverse cursor-pointer">
                        <!-- چک‌باکس -->
                        <input type="checkbox" name="books[]" value="{{ $book->id }}"
                               class="book-checkbox w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500"
                            {{ in_array($book->id, $professor->books->pluck('id')->toArray()) ? 'checked' : '' }}>

                        <!-- عکس کوچک کتاب -->
                        <img src="{{ $book->image}}"
                             alt="{{ $book->name }}"
                             class="w-10 h-10 rounded object-cover border border-gray-300">

                        <!-- نام کتاب -->
                        <span class="text-gray-700">{{ $book->name }}</span>
                    </label>
                @endforeach
            </div>

            <div class="mt-6 flex justify-end space-x-2 space-x-reverse">
                <button id="cancelBooksBtn" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                    انصراف
                </button>
                <button onclick="document.getElementById('bookModal').close()"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                    تأیید
                </button>
            </div>
        </dialog>

        <script>
            const openModalBtn = document.getElementById('openBooksModalBtn');
            const closeModalBtn = document.getElementById('closeBooksModalBtn');
            const cancelBtn = document.getElementById('cancelBooksBtn');
            const bookModal = document.getElementById('bookModal');

            openModalBtn.addEventListener('click', () => bookModal.showModal());
            closeModalBtn.addEventListener('click', () => bookModal.close());
            cancelBtn.addEventListener('click', () => bookModal.close());

            const selectAllBtn = document.getElementById('selectAllBooksBtn');
            const checkboxes = document.querySelectorAll('.book-checkbox');
            let allSelected = false;

            selectAllBtn.addEventListener('click', () => {
                allSelected = !allSelected;
                checkboxes.forEach(cb => cb.checked = allSelected);

                selectAllBtn.textContent = allSelected ? 'حذف انتخاب همه' : 'انتخاب همه';
                selectAllBtn.classList.toggle('bg-red-600', allSelected);
                selectAllBtn.classList.toggle('hover:bg-red-700', allSelected);
                selectAllBtn.classList.toggle('bg-indigo-600', !allSelected);
                selectAllBtn.classList.toggle('hover:bg-indigo-700', !allSelected);
            });
        </script>
    </div>
@endsection

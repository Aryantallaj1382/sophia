@extends('admin.layouts.app')

@section('content')
    <div class="container mx-auto p-6">

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-700">کتاب‌های استاد</h2>
            <button type="button" onclick="openBooksModal()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                انتخاب کتاب‌ها
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

        <!-- مودال (div ساده) - بدون dialog و بدون Alpine -->
        <div id="booksModalOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden flex items-center justify-center">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 p-6 max-h-screen overflow-y-auto">

                <form id="booksForm" action="{{ route('admin.professorsBook.update', $professor->id) }}" method="POST">
                    @csrf

                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800">انتخاب کتاب‌ها</h2>
                        <button type="button" onclick="closeBooksModal()" class="text-2xl font-bold text-gray-600 hover:text-gray-900">
                            ×
                        </button>
                    </div>

                    <!-- دکمه انتخاب همه -->
                    <button type="button" id="selectAllBooksBtn"
                            class="mb-4 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition text-sm font-medium">
                        انتخاب همه
                    </button>

                    <!-- لیست کتاب‌ها -->
                    <div class="space-y-3 max-h-96 overflow-y-auto border-t border-b py-4">
                        @foreach($books as $book)
                            <label class="flex items-center space-x-3 space-x-reverse cursor-pointer hover:bg-gray-50 p-2 rounded">
                                <input type="checkbox" name="books[]" value="{{ $book->id }}"
                                       class="book-checkbox w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500"
                                    {{ in_array($book->id, $professor->books->pluck('id')->toArray()) ? 'checked' : '' }}>

                                <img src="{{ asset($book->image) }}" class="w-10 h-10 rounded object-cover border border-gray-300">
                                <span class="text-gray-700">{{ $book->name }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeBooksModal()"
                                class="px-5 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                            انصراف
                        </button>

                        <!-- این دکمه حالا ۱۰۰٪ فرم را ارسال می‌کند -->
                        <button type="submit"
                                class="px-5 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition font-medium">
                            تأیید و ذخیره
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- جاوااسکریپت خالص (بدون Alpine) -->
        <script>
            function openBooksModal() {
                document.getElementById('booksModalOverlay').classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // جلوگیری از اسکرول صفحه
            }

            function closeBooksModal() {
                document.getElementById('booksModalOverlay').classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            // بستن مودال با کلیک روی پس‌زمینه
            document.getElementById('booksModalOverlay').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeBooksModal();
                }
            });

            // انتخاب/حذف همه
            document.getElementById('selectAllBooksBtn').addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('.book-checkbox');
                const allChecked = this.textContent.includes('انتخاب همه');

                checkboxes.forEach(cb => cb.checked = allChecked);

                this.textContent = allChecked ? 'حذف انتخاب همه' : 'انتخاب همه';
                this.classList.toggle('bg-red-600', allChecked);
                this.classList.toggle('hover:bg-red-700', allChecked);
                this.classList.toggle('bg-indigo-600', !allChecked);
                this.classList.toggle('hover:bg-indigo-700', !allChecked);
            });
        </script>
    </div>
@endsection

@extends('admin.layouts.app')

@section('title', 'آزمون‌ها')
@section('content')
    <div class="container mx-auto py-6" dir="rtl">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-800">📑 مدیریت آزمون‌ها</h3>
            <a href="{{ route('admin.exams.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                ➕ آزمون جدید
            </a>
        </div>

        <div class="overflow-x-auto bg-white shadow rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-right text-sm font-medium text-gray-600">نام</th>
                    <th class="px-4 py-2 text-right text-sm font-medium text-gray-600">نوع</th>
                    <th class="px-4 py-2 text-right text-sm font-medium text-gray-600">توضیح</th>
                    <th class="px-4 py-2 text-right text-sm font-medium text-gray-600">تاریخ انقضا</th>
                    <th class="px-4 py-2 text-right text-sm font-medium text-gray-600">دفعات مجاز</th>
                    <th class="px-4 py-2 text-right text-sm font-medium text-gray-600">تعداد بخش‌ها</th>
                    <th class="px-4 py-2 text-right text-sm font-medium text-gray-600">مدت زمان</th>
                    <th class="px-4 py-2 text-right text-sm font-medium text-gray-600">ویو</th>
                    <th class="px-4 py-2 text-right text-sm font-medium text-gray-600">عملیات</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                @forelse($exams as $exam)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-gray-700 font-medium">{{ $exam->name }}</td>
                        <td class="px-4 py-2 text-gray-700">{{ $exam->type }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ \Illuminate\Support\Str::words($exam->description, 5, '...') }}</td>
                        <td class="px-4 py-2 text-gray-700">{{ $exam->expiration }}</td>
                        <td class="px-4 py-2 text-gray-700">{{ $exam->number_of_attempts }}</td>
                        <td class="px-4 py-2 text-gray-700">{{ $exam->number_of_sections }}</td>
                        <td class="px-4 py-2 text-gray-700">{{ $exam?->duration?->format('H:i') }} دقیقه</td>
                        <td class="px-4 py-2 text-gray-700">{{ $exam->view }}</td>
                        <td class="px-4 py-2">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.exams.show', $exam->id) }}" class="text-blue-600 hover:underline">مشاهده</a>
                                <a href="{{ route('admin.exams.edit', $exam->id) }}" class="text-gray-600 hover:underline">ویرایش</a>
                                <form action="{{ route('admin.exams.destroy', $exam->id) }}" method="POST" onsubmit="return confirm('آیا مطمئن هستید؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">حذف</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-6 text-center text-gray-400">🚫 هیچ آزمونی یافت نشد.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $exams->links('pagination::tailwind') }}
        </div>
    </div>
@endsection

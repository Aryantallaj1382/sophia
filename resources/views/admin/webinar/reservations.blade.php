@extends('admin.layouts.app')

@section('title', 'لیست دانش‌آموزان کلاس گروهی')

@section('content')
    <div class="max-w-6xl mx-auto p-6 bg-white shadow-md rounded-lg">
        <h2 class="text-2xl font-bold mb-4">لیست دانش‌آموزان کلاس گروهی</h2>

        @if($reservations->isNotEmpty())
            <table class="w-full table-auto border-collapse border border-gray-200">
                <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2">#</th>
                    <th class="border px-4 py-2">نام دانش‌آموز</th>
                    <th class="border px-4 py-2">کد تخفیف</th>
                    <th class="border px-4 py-2">توضیحات</th>
                    <th class="border px-4 py-2">وضعیت</th>
                    <th class="border px-4 py-2">تاریخ رزرو</th>
                </tr>
                </thead>
                <tbody>
                @foreach($reservations as $index => $res)
                    <tr class="hover:bg-gray-50">
                        <td class="border px-4  text-center py-2">{{ $index + 1 }}</td>
                        <td class="border px-4  text-center py-2">{{ $res->user->name ?? 'نامشخص' }}</td>
                        <td class="border px-4 text-center py-2">{{ $res->discount_code ?? '-' }}</td>
                        <td class="border px-4 text-center py-2">{{ $res->description ?? '-' }}</td>
                        <td class="border px-4 text-center py-2 capitalize">
                            <span class="px-2 py-1 rounded
                                @if($res->status == 'approved') bg-green-200 text-green-800
                                @elseif($res->status == 'pending') bg-yellow-200 text-yellow-800
                                @elseif($res->status == 'rejected') bg-red-200 text-red-800
                                @elseif($res->status == 'canceled') bg-gray-200 text-gray-800
                                @endif">
                                {{ $res->status }}
                            </span>
                        </td>
                        <td class="border text-center px-4 py-2">{{ $res->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <p class="text-gray-500">هیچ دانش‌آموزی برای این کلاس رزرو نکرده است.</p>
        @endif
    </div>
@endsection

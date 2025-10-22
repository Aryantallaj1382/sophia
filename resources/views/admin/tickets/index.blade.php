@extends('admin.layouts.app')

@section('content')
    <div class="p-6 bg-gray-100 min-h-screen">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">📩 لیست تیکت‌ها</h1>

        <div class="overflow-x-auto bg-white rounded-xl shadow-lg">
            <div class="mb-4 px-4 py-3">
                <form method="GET" action="{{ route('admin.tickets.index') }}">
                    <select name="status"
                            onchange="this.form.submit()"
                            class="px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">همه وضعیت‌ها</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>باز</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>بسته</option>
                        <option value="answered" {{ request('status') == 'answered' ? 'selected' : '' }}>پاسخ داده شده</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>در انتظار پاسخ</option>
                    </select>
                </form>
            </div>

            <table class="w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-100 text-gray-800">
                <tr>
                    <th class="px-4 py-3 text-center">شماره تیکت</th>
                    <th class="px-4 py-3 text-center">کاربر</th>
                    <th class="px-4 py-3 text-center">موضوع</th>
                    <th class="px-4 py-3 text-center">وضعیت</th>
                    <th class="px-4 py-3 text-center">عملیات</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($tickets as $ticket)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-medium text-center text-gray-800">{{ $ticket->ticket_number }}</td>
                        <td class="px-4 py-3 font-medium text-center text-gray-800">{{ $ticket->user->name }}</td>
                        <td class="px-4 py-3 font-medium text-center text-gray-800">{{ $ticket->subject }}</td>
                        <td class="px-4 py-3 text-center">
                            @if ($ticket->status === 'open')
                                <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-lg">باز</span>
                            @elseif($ticket->status === 'pending')
                                <span class="px-2 py-1 text-xs font-semibold text-yellow-700 bg-yellow-200 rounded-lg">در انتظار پاسخ</span>
                            @elseif($ticket->status === 'answered')
                                <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-lg">پاسخ داده شده</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-lg">بسته</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.tickets.show', $ticket->id) }}"
                               class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-indigo-500 hover:bg-indigo-600 rounded-lg shadow transition">
                                مشاهده
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $tickets->appends(request()->query())->links() }}
        </div>
    </div>
@endsection

@extends('admin.layouts.app')

@section('content')
    <div class="container mx-auto p-4">

        <h2 class="text-2xl font-bold mb-4">لیست پلن‌های کاربران</h2>

        <!-- دکمه ثبت پلن جدید -->
        <a href="{{ route('admin.user-plans.create') }}"
           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded mb-4 inline-block">
            ثبت پلن جدید
        </a>

        <!-- جدول -->
        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">کاربر</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">پلن</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">تاریخ شروع</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">تاریخ پایان</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">کلاس‌ها</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">عملیات</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($userPlans as $plan)
                    <tr>
                        <td class="px-6 text-center py-4">{{ $plan->id }}</td>
                        <td class="px-6 text-center py-4">{{ $plan->user->name }}</td>
                        <td class="px-6 text-center py-4">{{ $plan->plan->name }}</td>
                        <td class="px-6 text-center py-4">{{ \Carbon\Carbon::parse($plan->started_at)->format('Y-m-d') }}</td>
                        <td class="px-6 text-center py-4">{{ \Carbon\Carbon::parse($plan->expires_at)->format('Y-m-d') }}</td>
                        <td class="px-6 text-center py-4">{{ $plan->class_count }}</td>
                        <td class="px-6 text-center py-4 space-x-2">

                            <!-- دکمه ویرایش -->
                            <a href="{{ route('admin.user-plans.edit', $plan->id) }}"
                               class="bg-yellow-400 hover:bg-yellow-500 text-white px-2 py-1 rounded text-xs">ویرایش</a>

                            <!-- دکمه حذف -->
                            <form action="{{ route('admin.user-plans.destroy', $plan->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('آیا مطمئن هستید؟')"
                                        class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">
                                    حذف
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $userPlans->links() }}
        </div>
    </div>
@endsection

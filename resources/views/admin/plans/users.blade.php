@extends('admin.layouts.app')

@section('content')
    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">کاربران خریدار پلن "{{ $plan->name }}"</h1>
            <a href="{{ route('admin.plans.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                بازگشت
            </a>
        </div>

        @if($users->count())
            <div class="overflow-x-auto bg-white shadow-md rounded-lg">
                <table class="min-w-full border border-gray-200 text-right">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border-b">#</th>
                        <th class="px-4 py-2 border-b">نام کاربر</th>
                        <th class="px-4 py-2 border-b">ایمیل</th>
                        <th class="px-4 py-2 border-b">تاریخ شروع</th>
                        <th class="px-4 py-2 border-b">تاریخ پایان</th>
                        <th class="px-4 py-2 border-b">وضعیت</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $index => $userPlan)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border-b">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 border-b">{{ $userPlan->user->name ?? '—' }}</td>
                            <td class="px-4 py-2 border-b">{{ $userPlan->user->email ?? '—' }}</td>
                            <td class="px-4 py-2 border-b">{{ $userPlan->started_at?->format('Y-m-d') ?? '—' }}</td>
                            <td class="px-4 py-2 border-b">{{ $userPlan->expires_at?->format('Y-m-d') ?? '—' }}</td>
                            <td class="px-4 py-2 border-b">
                                @if($userPlan->is_active)
                                    <span class="text-green-600 font-semibold">فعال</span>
                                @else
                                    <span class="text-red-600 font-semibold">غیرفعال</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-600 mt-4">هیچ کاربری این پلن را خریداری نکرده است.</p>
        @endif
    </div>
@endsection

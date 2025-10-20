@extends('admin.layouts.app')

@section('title', 'مدیریت کاربران')

@section('content')
    <div class="container py-4" dir="rtl">
        <h3 class="text-2xl font-bold mb-4">👥 لیست کاربران</h3>

        <div class="bg-white rounded-xl shadow p-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 table-auto">
                <thead class="bg-gray-100">
                <tr>
                    <th class="text-center px-4 py-2  text-sm font-medium text-gray-700">#</th>
                    <th class="text-center px-4 py-2  text-sm font-medium text-gray-700">نام</th>
                    <th class="text-center px-4 py-2  text-sm font-medium text-gray-700">ایمیل</th>
                    <th class="text-center px-4 py-2  text-sm font-medium text-gray-700">تاریخ ثبت‌نام</th>
                    <th class="text-center px-4 py-2  text-sm font-medium text-gray-700">عملیات</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                @foreach($users as $user)
                    <tr class="h-12">
                        <td class="text-center px-4 py-2">{{ $user->id }}</td>
                        <td class="text-center px-4 py-2">{{ $user->name }}</td>
                        <td class="text-center px-4 py-2">{{ $user->email }}</td>
                        <td class="text-center px-4 py-2">{{ $user->created_at?->format('Y/m/d') }}</td>
                        <td class="text-center px-4 py-2">
                            <div class="flex items-center justify-center gap-2">
                                <!-- دکمه مشاهده -->
                                <a href="{{ route('admin.users.show', $user->id) }}"
                                   class="inline-flex items-center gap-1 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200
                  px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200">
                                    👁 مشاهده
                                </a>

                                <!-- دکمه حذف -->
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                      onsubmit="return confirm('آیا از حذف این کاربر مطمئن هستید؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 text-red-600 bg-red-50 hover:bg-red-100 border border-red-200
                           px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200">
                                        🗑 حذف
                                    </button>
                                </form>
                            </div>
                        </td>


                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection

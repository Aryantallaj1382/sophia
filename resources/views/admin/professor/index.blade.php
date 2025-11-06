@extends('admin.layouts.app')

@section('title', 'لیست اساتید')

@section('content')
    <h2 class="text-2xl font-bold mb-6">لیست اساتید</h2>

    <!-- فرم جستجو -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center mb-4">
        <!-- فرم جستجو -->
        <form method="GET" action="{{ route('admin.professors.index') }}" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder=" کد , نام , ایمیل..."
                   class="p-2 border rounded">
            <button type="submit" class="bg-blue-500 text-white p-2 rounded">جستجو</button>
        </form>

        <!-- دکمه ایجاد استاد -->
        <div class="flex justify-start md:justify-end">
            <a href="{{ route('admin.professors.create') }}"
               class="bg-green-500 hover:bg-green-600 text-white p-2 rounded">
                ایجاد استاد جدید
            </a>
        </div>
    </div>


    <!-- جدول اساتید -->
    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full table-auto border-collapse border border-gray-200">
            <thead>
            <tr class="bg-gray-100 text-gray-700">
                <th class="px-4 py-2 border text-center">
                        #
                </th>
                <th class="px-4 py-2 border">
                        نام استاد
                </th>
                <th class="px-4 py-2 border">
                        کد استاد
                </th>
                <th class="px-4 py-2 border">
                        ایمیل
                </th>
                <th class="px-4 py-2 border text-center">
                    <a href="{{ route('admin.professors.index', ['sort' => 'created_at', 'direction' => request('direction') == 'desc' ? 'asc' : 'desc']) }}">
                        تاریخ ایجاد
                    </a>
                </th>
                <th class="px-4 py-2 border text-center">عملیات</th>
            </tr>
            </thead>
            <tbody>
            @forelse($professors as $index => $professor)
                <tr class="text-gray-600 hover:bg-gray-50 transition">
                    <td class="px-4 te py-2 border text-center">{{ $professors->firstItem() + $index }}</td>

                    <td class="px-4 py-2 border flex items-center gap-2">
                        <img src="{{ $professor->user->profile ? asset($professor->user->profile) : 'https://ui-avatars.com/api/?name='.urlencode($professor->user->name).'&background=0D8ABC&color=fff&size=40' }}"
                             alt="{{ $professor->user->name }}"
                             class="w-10 h-10 rounded-full border">
                        <span>{{ $professor->user->name }}</span>
                    </td>
                    <td class="px-4 py-2 text-center border">{{ $professor->id }}</td>
                    <td class="px-4 py-2 text-center border">{{ $professor->user->email }}</td>
                    <td class="px-4 py-2 border  text-center">{{ $professor?->created_at?->format('d M Y') }}</td>
                    <td class="px-4 py-2 border text-center">
                        <a href="{{route('admin.professors.show' , $professor->id)}}" class="text-blue-500 hover:text-blue-700">نمایش</a>
                        <a href="{{route('admin.professors.edit' , $professor->id)}}" class="text-gray-50-500 hover:text-blue-700">ویرایش</a>
                        <form action="# " method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700">حذف</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-2 text-center text-gray-500">استادی پیدا نشد.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- پیجینیشن -->
    <div class="mt-4">
        {{ $professors->appends(request()->except('page'))->links() }}
    </div>
@endsection


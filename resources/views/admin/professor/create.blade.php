@extends('admin.layouts.app')

@section('title', 'ایجاد استاد جدید')

@section('content')
    <h2 class="text-2xl font-bold mb-6">ایجاد استاد جدید</h2>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pr-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.professors.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow rounded-lg p-6 space-y-4">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="text" name="first_name" placeholder="نام استاد" class="w-full border rounded p-2 text-right placeholder-gray-400">
            <input type="text" name="last_name" placeholder="نام خانوادگی استاد" class="w-full border rounded p-2 text-right placeholder-gray-400">
            <input type="email" name="email" placeholder="ایمیل" class="w-full border rounded p-2 text-right placeholder-gray-400">
            <input type="password" name="password" placeholder="رمز عبور" class="w-full border rounded p-2 text-right placeholder-gray-400">
            <input type="password" name="password_confirmation" placeholder="تکرار رمز عبور" class="w-full border rounded p-2 text-right placeholder-gray-400">
            <input type="text" name="mobile" placeholder="شماره تلفن" class="w-full border rounded p-2 text-right placeholder-gray-400">
            <input type="text" name="bio" placeholder="بیو" class="w-full border rounded p-2 text-right placeholder-gray-400">
            <input type="text" name="years_of_experience" placeholder="سابقه تدریس" class="w-full border rounded p-2 text-right placeholder-gray-400">

            <div class="col-span-1 md:col-span-2 flex gap-4 items-center">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1">
                    فعال باشد
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_verified" value="1">
                    تایید شده
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_native" value="1">
                    استاد محلی
                </label>
            </div>

            <div class="col-span-1 md:col-span-2 flex gap-4 items-center">
                <label class="flex items-center gap-2">
                    <input type="radio" name="gender" value="male">
                    مرد
                </label>
                <label class="flex items-center gap-2">
                    <input type="radio" name="gender" value="female">
                    زن
                </label>
            </div>

            <input type="date" name="birth_date" class="w-full border rounded p-2 text-right">

            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded col-span-1 md:col-span-2">
                ثبت استاد
            </button>
        </div>
    </form>
@endsection

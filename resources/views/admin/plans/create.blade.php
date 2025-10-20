@extends('admin.layouts.app')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">ایجاد پلن جدید</h1>

        {{-- نمایش خطاها --}}
        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                <ul class="list-disc ms-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.plans.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-5">
            @csrf

            {{-- ردیف ۱: نام پلن + نوع پلن --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-semibold mb-2">نام پلن</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full border rounded-lg px-3 py-2" required>
                </div>

                <div>
                    <label class="block font-semibold mb-2">نوع پلن</label>
                    <select name="plan_type" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="">انتخاب کنید...</option>
                        @foreach ($planTypes as $key => $label)
                            <option value="{{ $key }}" {{ old('plan_type') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- ردیف ۲: رنگ پلن + مدت اعتبار --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-semibold mb-2">کد رنگ پلن</label>
                    <input type="color" name="color" value="{{ old('color', '#000000') }}"
                           class="w-24 h-10 p-0 border rounded-lg cursor-pointer">
                </div>

                <div>
                    <label class="block font-semibold mb-2">مدت اعتبار (روز)</label>
                    <input type="number" name="days" value="{{ old('days') }}"
                           class="w-full border rounded-lg px-3 py-2" required>
                </div>
            </div>

            {{-- ردیف ۳: قیمت + قیمت اصلی --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-semibold mb-2">قیمت (بعد از تخفیف)</label>
                    <input type="number" name="price" value="{{ old('price') }}"
                           class="w-full border rounded-lg px-3 py-2" required>
                </div>

                <div>
                    <label class="block font-semibold mb-2">قیمت اصلی (قبل از تخفیف)</label>
                    <input type="number" name="original_price" value="{{ old('original_price') }}"
                           class="w-full border rounded-lg px-3 py-2">
                </div>
            </div>

            {{-- ردیف ۴: میزان تخفیف + تعداد کلاس --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-semibold mb-2">میزان تخفیف</label>
                    <input type="number" name="discount_amount" value="{{ old('discount_amount') }}"
                           class="w-full border rounded-lg px-3 py-2">
                </div>

                <div>
                    <label class="block font-semibold mb-2">تعداد کلاس‌ها</label>
                    <input type="number" name="class_count" value="{{ old('class_count') }}"
                           class="w-full border rounded-lg px-3 py-2">
                </div>
            </div>

            {{-- دکمه ثبت --}}
            <div class="flex justify-end pt-4">
                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    ایجاد پلن
                </button>
            </div>
        </form>
    </div>
@endsection

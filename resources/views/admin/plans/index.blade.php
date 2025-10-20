@extends('admin.layouts.app')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">لیست پلن‌ها</h1>
            <a href="{{ route('admin.plans.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                + ایجاد پلن جدید
            </a>
        </div>

        {{-- فیلتر بر اساس نوع پلن --}}
        <div class="flex flex-wrap gap-3 mb-6">
            <a href="{{ route('admin.plans.index') }}"
               class="px-4 py-2 rounded-lg text-white {{ !$type ? 'bg-blue-600' : 'bg-gray-500 hover:bg-gray-600' }}">
                همه
            </a>
            @foreach($planTypes as $key => $label)
                <a href="{{ route('admin.plans.index', ['type' => $key]) }}"
                   class="px-4 py-2 rounded-lg text-white {{ $type === $key ? 'bg-blue-600' : 'bg-gray-500 hover:bg-gray-600' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- نمایش کارت‌ها --}}
        {{-- نمایش کارت‌ها --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($plans as $plan)
                <div class="border-2 rounded-xl shadow-md flex flex-col justify-between"
                     style="border-color: {{ $plan->color }};">

                    {{-- محتوای اصلی کارت --}}
                    <div class="p-5 flex-1">
                        <h2 class="text-xl font-bold mb-2" style="color: {{ $plan->color }};">
                            {{ $plan->name }}
                        </h2>

                        {{-- نوار رنگی بین عنوان و نوع پلن --}}
                        <div class="h-2 mb-3" style="background-color: {{ $plan->color }};"></div>

                        <p class="text-gray-600 mb-2">نوع پلن: {{ $planTypes[$plan->plan_type] ?? $plan->plan_type }}</p>
                        <p class="text-gray-600 mb-2">تعداد کلاس: {{ $plan->class_count }}</p>
                        <p class="text-gray-600 mb-2">مدت (روز): {{ $plan->days }}</p>

                        <div class="mt-3">
                            <p class="text-gray-800 font-semibold">
                                قیمت: <span class="text-black">{{ number_format($plan->price) }} تومان</span>
                            </p>
                            @if($plan->discount_amount)
                                <p class="text-sm text-green-600">
                                    تخفیف: {{ number_format($plan->discount_amount) }}
                                </p>
                                <p class="text-sm text-gray-500 line-through">
                                    قیمت اصلی: {{ number_format($plan->original_price) }}
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- دکمه‌ها --}}
                    <div class="flex justify-between items-center border-t px-5 py-4 bg-gray-50 rounded-b-xl">
                        <a href="{{ route('admin.plans.users', $plan->id) }}"
                           class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                            کاربران خریدار
                        </a>

                        <form action="{{ route('admin.plans.destroy', $plan->id) }}" method="POST"
                              onsubmit="return confirm('آیا از حذف این پلن مطمئن هستید؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                                حذف
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">هیچ پلنی یافت نشد.</p>
            @endforelse
        </div>
    </div>
@endsection

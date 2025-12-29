@extends('admin.layouts.app')

@section('content')
    <div class="container,container mx-auto p-6">
        <h2 class="text-3xl font-bold mb-6 text-gray-800">افزودن پلن جدید برای کاربر</h2>

        <!-- نمایش خطاها -->
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- پیام موفقیت -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.user-plans.store') }}" method="POST" class="bg-white shadow-lg rounded-lg p-8">
            @csrf

            <!-- انتخاب کاربر -->
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">کاربر <span class="text-red-500">*</span></label>
                <select name="user_id" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none">
                    <option value="">-- انتخاب کاربر --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- انتخاب پلن -->
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">پلن <span class="text-red-500">*</span></label>
                <select name="plan_id" id="plan-select" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none">
                    <option value="">-- انتخاب پلن --</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}"
                                data-days="{{ $plan->days }}"
                                data-classes="{{ $plan->class_count }}"
                            {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                            {{ $plan->name }} ({{ $plan->days }} روز - {{ $plan->class_count }} کلاس)
                        </option>
                    @endforeach
                </select>
                @error('plan_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- تاریخ شروع -->
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">تاریخ شروع</label>
                <input type="date" name="started_at" id="started_at"
                       value="{{ old('started_at', date('Y-m-d')) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none">
                @error('started_at')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- تاریخ پایان -->
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">تاریخ پایان (قابل ویرایش)</label>
                <input type="date" name="expires_at" id="expires_at"
                       value="{{ old('expires_at') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none">
                @error('expires_at')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- تعداد کلاس -->
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">تعداد کلاس (قابل ویرایش)</label>
                <input type="number" name="class_count" id="class_count"
                       value="{{ old('class_count') }}"
                       min="0"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none">
                @error('class_count')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition">
                    ثبت پلن
                </button>
                         <a href="{{ route('admin.user-plans.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-8 rounded-lg transition">
                    بازگشت
                </a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('plan-select').addEventListener('change', function () {
            const selected = this.options[this.selectedIndex];
            const days = selected.getAttribute('data-days');
            const classes = selected.getAttribute('data-classes');

            if (days && classes) {
                // تاریخ شروع = امروز
                const today = new Date();
                const yyyy = today.getFullYear();
                const mm = String(today.getMonth() + 1).padStart(2, '0');
                const dd = String(today.getDate()).padStart(2, '0');
                document.getElementById('started_at').value = `${yyyy}-${mm}-${dd}`;

                // تاریخ پایان = امروز + تعداد روزها
                const expires = new Date();
                expires.setDate(today.getDate() + parseInt(days));
                const exp_yyyy = expires.getFullYear();
                const exp_mm = String(expires.getMonth() + 1).padStart(2, '0');
                const exp_dd = String(expires.getDate()).padStart(2, '0');
                document.getElementById('expires_at').value = `${exp_yyyy}-${exp_mm}-${exp_dd}`;

                // تعداد کلاس
                document.getElementById('class_count').value = classes;
            } else {
                document.getElementById('expires_at').value = '';
                document.getElementById('class_count').value = '';
            }
        });

        // اگر از قبل پلنی انتخاب شده بود (مثلاً خطا در فرم)
        window.addEventListener('load', function () {
            const select = document.getElementById('plan-select');
            if (select.value) {
                select.dispatchEvent(new Event('change'));
            }
        });
    </script>
@endsection

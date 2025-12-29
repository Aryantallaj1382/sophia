@extends('admin.layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-bold mb-4">ویرایش پلن کاربر</h2>

        <form action="{{ route('admin.user-plans.update', $userPlan->id) }}" method="POST" class="bg-white p-6 rounded shadow">
            @csrf
            @method('PUT')

            <!-- انتخاب کاربر -->
            <div class="mb-4">
                <label class="block mb-1 font-medium">کاربر:</label>
                <select name="user_id" class="w-full border-gray-300 rounded px-3 py-2">
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" @selected($u->id == $userPlan->user_id)>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- انتخاب پلن -->
            <div class="mb-4">
                <label class="block mb-1 font-medium">پلن:</label>
                <select name="plan_id" id="plan-select" class="w-full border-gray-300 rounded px-3 py-2">
                    <option value="">انتخاب پلن</option>
                    @foreach($plans as $p)
                        <option value="{{ $p->id }}"
                                data-days="{{ $p->days }}"
                                data-classes="{{ $p->class_count }}"
                            @selected($p->id == $userPlan->plan_id)>
                            {{ $p->name }} ({{ $p->days }} روز)
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- تاریخ شروع -->
            <div class="mb-4">
                <label class="block mb-1 font-medium">تاریخ شروع:</label>
                <input type="date" name="started_at" id="started_at"
                       value="{{ \Carbon\Carbon::parse($userPlan->started_at)->format('Y-m-d') }}"
                       class="w-full border-gray-300 rounded px-3 py-2">
            </div>

            <!-- تاریخ پایان -->
            <div class="mb-4">
                <label class="block mb-1 font-medium">تاریخ پایان:</label>
                <input type="date" name="expires_at" id="expires_at"
                       value="{{ \Carbon\Carbon::parse($userPlan->expires_at)->format('Y-m-d') }}"
                       class="w-full border-gray-300 rounded px-3 py-2">
            </div>

            <!-- تعداد کلاس -->
            <div class="mb-4">
                <label class="block mb-1 font-medium">تعداد کلاس:</label>
                <input type="number" name="class_count" id="class_count"
                       value="{{ $userPlan->class_count }}"
                       class="w-full border-gray-300 rounded px-3 py-2">
            </div>

            <button class="bg-yellow-400 hover:bg-yellow-500 text-white px-4 py-2 rounded">ذخیره تغییرات</button>
        </form>
    </div>

    <script>
        document.getElementById('plan-select').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const days = selected.getAttribute('data-days');
            const classes = selected.getAttribute('data-classes');

            if(days) {
                // تاریخ شروع = امروز
                const today = new Date();
                const yyyy = today.getFullYear();
                const mm = String(today.getMonth() + 1).padStart(2, '0');
                const dd = String(today.getDate()).padStart(2, '0');
                document.getElementById('started_at').value = `${yyyy}-${mm}-${dd}`;

                // تاریخ پایان = امروز + days
                const expires = new Date();
                expires.setDate(today.getDate() + parseInt(days));
                const exp_yyyy = expires.getFullYear();
                const exp_mm = String(expires.getMonth() + 1).padStart(2, '0');
                const exp_dd = String(expires.getDate()).padStart(2, '0');
                document.getElementById('expires_at').value = `${exp_yyyy}-${exp_mm}-${exp_dd}`;

                // تعداد کلاس
                document.getElementById('class_count').value = classes;
            }
        });
    </script>
@endsection

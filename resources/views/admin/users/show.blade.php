@extends('admin.layouts.app')

@section('content')
    <div class="p-6 bg-gray-100 min-h-screen">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div
            class="flex items-center justify-between px-6 py-4 border-b border-gray-300 bg-white mb-6 rounded-lg shadow">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">👤 جزئیات کاربر: {{ $user->name }}</h1>
            </div>
            <button id="openWalletModal"
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                باز کردن کیف پول
            </button>
            <a href="{{ route('admin.users.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg shadow transition transform hover:scale-105">
                ← بازگشت
            </a>
        </div>
            <div
                class="grid grid-cols-1 md:grid-cols-4 gap-4 px-6 py-4 border-b border-gray-300 bg-white mb-6 rounded-lg shadow">

                <div>
                    <span class="block text-sm text-gray-500">Nickname</span>
                    <span class="text-lg font-semibold text-gray-800">
            {{ $user->student->nickname }}
        </span>
                </div>

                <div>
                    <span class="block text-sm text-gray-500">First Name</span>
                    <span class="text-lg font-semibold text-gray-800">
            {{ $user->student->first_name }}
        </span>
                </div>

                <div>
                    <span class="block text-sm text-gray-500">Last Name</span>
                    <span class="text-lg font-semibold text-gray-800">
            {{ $user->student->last_name }}
        </span>
                </div>

                <div>
                    <span class="block text-sm text-gray-500">Birth Date</span>
                    <span class="text-lg font-semibold text-gray-800">
            {{ $user->student?->birth_date?->format('d-Y-M') }}
        </span>
                </div>
                <div>
                    <span class="block text-sm text-gray-500"> we chat</span>
                    <span class="text-lg font-semibold text-gray-800">
            {{ $user->student?->we_chat}}
        </span>
                </div>
                <div>
                    <span class="block text-sm text-gray-500">phone</span>
                    <span class="text-lg font-semibold text-gray-800">
            {{ $user->student?->phone}}
        </span>
                </div>
            </div>






        <div class="mb-6 p-4 bg-white rounded-lg shadow text-center">
            <h2 class="text-xl font-semibold text-gray-800 mb-2">💰 کیف پول</h2>
            <p class="text-gray-600">موجودی: {{ number_format($user->wallet->balance ?? 0) }} تومان</p>
        </div>

        <div class="overflow-x-auto">
            <h2 class="text-xl font-semibold text-gray-800 mb-2">📄 تراکنش‌ها</h2>
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-200">
                <tr>
                    <th class="px-4 py-2 text-gray-800">#</th>
                    <th class="px-4 py-2 text-gray-800">نوع تراکنش</th>
                    <th class="px-4 py-2 text-gray-800">مبلغ</th>
                    <th class="px-4 py-2 text-gray-800">شماره کارت</th>
                    <th class="px-4 py-2 text-gray-800">وضعیت</th>
                    <th class="px-4 py-2 text-gray-800">تاریخ و ساعت</th>
                    <th class="px-4 py-2 text-gray-800">تغییر وضعیت</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-300">
                @forelse($user->wallet->transactions ?? [] as $index => $transaction)
                    <tr class="hover:bg-gray-100 transition">
                        <td class="px-4 py-2">{{ $index + 1 }}</td>
                        <td class="px-4 py-2">
                            @if ($transaction->type === 'deposit')
                                <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-lg">واریز</span>
                            @else
                                <span
                                    class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-lg">برداشت</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ number_format($transaction->amount) }} تومان</td>
                        <td class="px-4 py-2">{{ '---' }}</td>
                        <td class="px-4 py-2">
                            @if ($transaction->status === 'completed')
                                <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-lg">موفق</span>
                            @elseif($transaction->status === 'pending')
                                <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-lg">در انتظار ادمین</span>
                            @else
                                <span
                                    class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-lg">ناموفق</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-gray-600">
                            {{ $transaction?->created_at?->format('H:i - Y/m/d') }}
                        </td>
                        <td class="px-4 py-2">
                            <form method="POST" action="{{route('admin.transactions.updateStatus' , $transaction)}}">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()"
                                        class="text-sm rounded-lg px-2 py-1 border border-gray-300 bg-white text-gray-800 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <option
                                        value="completed" {{ $transaction->status === 'completed' ? 'selected' : '' }}>✅
                                        موفق
                                    </option>
                                    <option value="pending" {{ $transaction->status === 'pending' ? 'selected' : '' }}>⏳
                                        در انتظار
                                    </option>
                                    <option value="failed" {{ $transaction->status === 'failed' ? 'selected' : '' }}>❌
                                        ناموفق
                                    </option>
                                </select>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-2 text-center text-gray-500">تراکنشی وجود ندارد.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

    </div>
    <!-- دکمه برای باز کردن مدال -->
    <button id="openWalletModal" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
        باز کردن کیف پول
    </button>

    <!-- مدال -->
    <!-- مدال -->
    <div id="walletModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50">
        <div
            class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md relative transform transition-all scale-95 opacity-0">
            <button id="closeWalletModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-2xl">
                &times;
            </button>
            <h2 class="text-xl font-bold mb-4">💳 مدیریت کیف پول</h2>

            <form method="POST" action="{{ route('admin.users.wallet.update', $user) }}">
                @csrf
                <div class="mb-4">
                    <label class="block mb-1 font-semibold">نوع تراکنش</label>
                    <select name="operation_type" class="w-full border-gray-300 rounded-lg px-3 py-2">
                        <option value="deposit">واریز</option>
                        <option value="withdraw">برداشت</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-semibold">مقدار (تومان)</label>
                    <input type="number" name="amount" min="1" required
                           class="w-full border-gray-300 rounded-lg px-3 py-2" placeholder="مثلاً 100000">
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" id="cancelWalletModal"
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">
                        لغو
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                        ثبت
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('walletModal');
        const modalContent = modal.querySelector('.bg-white');
        const openBtn = document.getElementById('openWalletModal');
        const closeBtn = document.getElementById('closeWalletModal');
        const cancelBtn = document.getElementById('cancelWalletModal');

        // تابع باز کردن مدال با انیمیشن
        openBtn.onclick = () => {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        // تابع بستن مدال با انیمیشن
        function closeModal() {
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }

        closeBtn.onclick = closeModal;
        cancelBtn.onclick = closeModal;

        // بستن با کلیک روی زمینه
        modal.onclick = (e) => {
            if (e.target === modal) closeModal();
        }
    </script>
@endsection

@extends('layouts.admin')

@section('content')
    <div class="space-y-6">

        {{-- کارت‌های اطلاعات --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-4 flex flex-col justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">کل کاربران</p>
                    <h3 class="text-2xl font-bold mt-1">1,245</h3>
                </div>
                <div class="text-green-500 mt-2">+5% نسبت به هفته گذشته</div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-4 flex flex-col justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">سفارشات</p>
                    <h3 class="text-2xl font-bold mt-1">342</h3>
                </div>
                <div class="text-green-500 mt-2">+12% نسبت به هفته گذشته</div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-4 flex flex-col justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">درآمد ماهانه</p>
                    <h3 class="text-2xl font-bold mt-1">45,600 تومان</h3>
                </div>
                <div class="text-green-500 mt-2">+8% نسبت به ماه گذشته</div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-4 flex flex-col justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">بازدید روزانه</p>
                    <h3 class="text-2xl font-bold mt-1">1,532</h3>
                </div>
                <div class="text-green-500 mt-2">+3% نسبت به دیروز</div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-6">
            <h3 class="text-lg font-bold mb-4">سفارشات اخیر</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                    <tr class="bg-gray-100 dark:bg-gray-800">
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-300">نام مشتری</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-300">محصول</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-300">مقدار</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-300">وضعیت</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-300">تاریخ</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr>
                        <td class="px-4 py-2">آریان</td>
                        <td class="px-4 py-2">کلاس خصوصی</td>
                        <td class="px-4 py-2">1</td>
                        <td class="px-4 py-2 text-green-500 font-semibold">تکمیل شده</td>
                        <td class="px-4 py-2">1402/05/10</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2">بهناز</td>
                        <td class="px-4 py-2">وبینار</td>
                        <td class="px-4 py-2">3</td>
                        <td class="px-4 py-2 text-yellow-500 font-semibold">در انتظار</td>
                        <td class="px-4 py-2">1402/05/12</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2">محمد</td>
                        <td class="px-4 py-2">کلاس گروهی</td>
                        <td class="px-4 py-2">2</td>
                        <td class="px-4 py-2 text-red-500 font-semibold">کنسل شده</td>
                        <td class="px-4 py-2">1402/05/15</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور'],
                datasets: [{
                    label: 'درآمد',
                    data: [1200, 1900, 3000, 2500, 3200, 4000],
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
@endsection

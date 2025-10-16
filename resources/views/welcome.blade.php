@extends('admin.layouts.app')

@section('title', 'داشبورد مدیریت')

@section('content')
    <div class="space-y-6">

        {{-- کارت‌های اطلاعات --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow p-4 flex flex-col justify-between">
                <div>
                    <p class="text-sm text-gray-500">کل کاربران</p>
                    <h3 class="text-2xl font-bold mt-1">1,245</h3>
                </div>
                <div class="text-green-500 mt-2">+5% نسبت به هفته گذشته</div>
            </div>

            <div class="bg-white rounded-xl shadow p-4 flex flex-col justify-between">
                <div>
                    <p class="text-sm text-gray-500">سفارشات</p>
                    <h3 class="text-2xl font-bold mt-1">342</h3>
                </div>
                <div class="text-green-500 mt-2">+12% نسبت به هفته گذشته</div>
            </div>

            <div class="bg-white rounded-xl shadow p-4 flex flex-col justify-between">
                <div>
                    <p class="text-sm text-gray-500">درآمد ماهانه</p>
                    <h3 class="text-2xl font-bold mt-1">45,600 تومان</h3>
                </div>
                <div class="text-green-500 mt-2">+8% نسبت به ماه گذشته</div>
            </div>

            <div class="bg-white rounded-xl shadow p-4 flex flex-col justify-between">
                <div>
                    <p class="text-sm text-gray-500">بازدید روزانه</p>
                    <h3 class="text-2xl font-bold mt-1">1,532</h3>
                </div>
                <div class="text-green-500 mt-2">+3% نسبت به دیروز</div>
            </div>
        </div>

        {{-- جدول سفارشات اخیر --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold mb-4">کلاس های اخیر</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                    <tr class="bg-gray-100">
                        <th class="text-center px-4 py-2text-sm font-medium text-gray-700">نام مشتری</th>
                        <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">محصول</th>
                        <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">مقدار</th>
                        <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">وضعیت</th>
                        <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">تاریخ</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="text-center px-4 py-2">آریان</td>
                        <td class="text-center px-4 py-2">کلاس خصوصی</td>
                        <td class="text-center px-4 py-2">1</td>
                        <td class="text-center px-4 py-2 text-green-500 font-semibold">تکمیل شده</td>
                        <td class="text-center px-4 py-2">1402/05/10</td>
                    </tr>
                    <tr>
                        <td class="text-center px-4 py-2">علی</td>
                        <td class="text-center px-4 py-2">وبینار</td>
                        <td class="text-center px-4 py-2">3</td>
                        <td class="text-center px-4 py-2 text-yellow-500 font-semibold">در انتظار</td>
                        <td class="text-center px-4 py-2">1402/05/12</td>
                    </tr>
                    <tr>
                        <td class="text-center px-4 py-2">محمد</td>
                        <td class="text-center px-4 py-2">کلاس گروهی</td>
                        <td class="text-center px-4 py-2">2</td>
                        <td class="text-center px-4 py-2 text-red-500 font-semibold">کنسل شده</td>
                        <td class="text-center px-4 py-2">1402/05/15</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- نمودار درآمد --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold mb-4">نمودار درآمد ماهانه</h3>
            <canvas id="salesChart" height="150"></canvas>
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

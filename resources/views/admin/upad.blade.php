<!DOCTYPE html>
<html lang="en" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <!-- فقط Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Tailwind CSS -->

    <script src="https://cdn.tailwindcss.com"></script>


    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        customGray: '#E1E5EB',
                        customblue: '#0073E6',
                        colors_text_main: '#2E384D',
                        colors_text_help: '#68768A',
                        colors_error_600_base: '#DC3545',
                        primery_100: '#D6F3FF',
                        colors_success_600_base: '#28A745',
                        secondary_600main: '#FF8A00',
                    },
                    fontFamily: {
                        'iransans': ['IranianSans', 'system-ui']
                    }
                }
            }
        }
    </script>

    <!-- Custom Styles -->
    <link rel="stylesheet" href="{{ asset('admin/style.css') }}">

    <style>
        @media (max-width: 1024px) {
            #mobile-block {
                display: flex !important;
            }
        }
    </style>
</head>

<body class="font-iransans bg-[#F5F7FA]">

    <div class="flex w-full min-h-screen">
        <!-- Sidebar -->
        <div
            class="sidebar flex flex-col items-center p-4 gap-10 py-4 bg-white border border-customGray rounded-xl mt-5 ml-3">
            <div class="w-36 flex justify-center items-center">
                <i class="bi bi-bootstrap text-4xl text-blue-600"></i>
            </div>
            @include('admin.sitebar')
        </div>

        <!-- Content Area -->
        <div class="flex flex-col flex-grow gap-6 p-6">
            <!-- Top Bar -->
            <div class="flex py-3.5 px-6 items-start gap-2.5 border rounded-2xl border-customGray bg-white">
                <div class="flex items-center justify-between w-full">
                    <div class="flex w-[363px] gap-2 border rounded-md border-customGray bg-white p-1">
                        <div class="flex items-center justify-center w-6 h-6 pt-2">
                            <i class="bi bi-search text-gray-500"></i>
                        </div>
                        <input class="w-full border-0 focus:outline-none" type="text" placeholder="جستجو">
                    </div>
                    <div class="flex items-center gap-6">
                        <button class="flex items-center justify-center w-6 h-6">
                            <i class="bi bi-calendar3 "></i>
                        </button>
                        <button class="flex items-center justify-center w-8 h-8">
                            <i class="bi bi-bell-fill "></i>
                        </button>
                        <button class="flex items-center justify-center py-2 pl-3 pr-2 gap-2.5 bg-customblue rounded-md">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-6 h-6">
                                    <i class="bi bi-person-circle text-white text-2xl"></i>
                                </div>
                                <p class="text-xs font-bold text-center text-white">پروفایل</p>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

    <!-- بلاک موبایل -->
    <div id="mobile-block" class="hidden fixed inset-0 bg-white flex-col items-center justify-center z-50">
        <h1 class="text-2xl font-bold mb-4">این وب‌سایت فقط روی کامپیوتر در دسترس است</h1>
        <p class="text-lg">لطفاً با یک دستگاه دسکتاپ وارد شوید.</p>
    </div>

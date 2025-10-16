<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'پنل مدیریت')</title>

    <!-- Tailwind CSS -->

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.default.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

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

    <style>
        @media (max-width: 1024px) {
            #mobile-block {
                display: flex !important;
            }
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-800 font-sans">

{{-- هدر --}}
@include('admin.layouts.header')

<div class="flex">
    {{-- سایدبار --}}
    @include('admin.layouts.sidebar')

    {{-- محتوای اصلی --}}
    <main class="flex-1 p-6">
        @yield('content')
    </main>
</div>

{{-- فوتر --}}
@include('admin.layouts.footer')

@vite('resources/js/app.js')
@stack('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

</body>
</html>

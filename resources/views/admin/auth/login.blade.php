
<!DOCTYPE html>
<html lang="fa" dir="ltr">
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


<div class="flex">


    <main class="flex-1 p-6">
            <div class="flex items-center justify-center min-h-screen bg-gray-100">
                <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
                    <h1 class="text-2xl font-bold mb-6 text-center">admin</h1>

                    <form action="{{ route('admin.login.submit') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 mb-2">email</label>
                            <input type="email" name="email" id="email"
                                   class="w-full border px-3 py-2 rounded"
                                   required autofocus>
                            @error('email')
                            <p class="text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="block text-gray-700 mb-2">password</label>
                            <input type="password" name="password" id="password"
                                   class="w-full border px-3 py-2 rounded" required>
                            @error('password')
                            <p class="text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>



                        <button type="submit"
                                class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                            ورود
                        </button>
                    </form>
                </div>
            </div>
    </main>
</div>


@vite('resources/js/app.js')
@stack('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

</body>
</html>

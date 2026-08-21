<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4f46e5',
                        secondary: '#6366f1'
                    },
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased bg-gradient-to-tr from-indigo-100 via-white to-indigo-200 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md px-8 py-10 bg-white/70 backdrop-blur-md rounded-2xl shadow-xl">
        <div class="flex justify-center mb-4">
            <a href="/">
                <x-application-logo class="w-16 h-16 text-indigo-600" />
            </a>
        </div>
        {{ $slot }}
    </div>
</body>
</html>

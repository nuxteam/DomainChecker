<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-100 flex items-center justify-center font-sans">

    <div class="w-full max-w-md flex items-center flex-col ">

        <!-- logo -->
        <div class="flex justify-center mb-6">
            <a href="/">
                <x-application-logo class="w-10 h-10 text-gray-700" />
            </a>
        </div>

        <!-- CARD -->
        <div class="flex justify-center bg-white p-6 rounded-lg max-w-xl">

            {{ $slot }}

        </div>

    </div>

</body>
</html>
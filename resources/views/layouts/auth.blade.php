<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body>
    <div class="flex min-h-screen items-center justify-center py-12 sm:px-6 lg:px-8">
        {{ $slot }}
    </div>
    @livewireScripts
</body>

</html>

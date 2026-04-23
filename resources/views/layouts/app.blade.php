<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles

</head>

<body class="min-h-screen bg-white dark:bg-zinc-800 antialiased">

    <livewire:components.sidebar>
        <livewire:components.toast/>
            {{ $slot }}
    </livewire:components.sidebar>


    @livewireScripts
    @fluxScripts
</body>

</html>

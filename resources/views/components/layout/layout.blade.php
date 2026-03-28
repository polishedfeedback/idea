<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Code+Pro:ital,wght@0,200..900;1,200..900&family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
    <title>Idea</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-foreground">
    <x-layout.nav />
    <main class="max-w-7xl mx-auto px-6 pb-10">
        {{ $slot }}
    </main>

    @session('success')
        <div
            x-data="{show: true}"
            x-init="setTimeout(()=>show=false, 3000)"
            x-show="show"
            x-transition:enter.duration.500ms
            x-transition:leave.duration.400ms
            class="bg-primary px-4 py-3 absolute right-4 bottom-4 rounded-lg"
        >
        {{$value}}
        </div>
    @endsession
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Luan Erazo Website</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body style="height: 100vh" class="antialiased bg-black py-8">
    <x-three-canvas />
    <nav class="relative z-50">
        <ul class="list-none flex justify-center flex-row">
            <li class="px-6 font-bold hover:text-rose-700 transition-all hover:underline"><a href="{{route('portfolio')}}">Portfolio</a>
            </li>
            <li class="px-6 font-bold hover:text-rose-700 transition-all hover:underline"><a target="_blank" href="https://data.skyrocket.com.co/index.php/s/rTTKPFFmtLcEEJg">CV - Curriculum Vitae - español</a>
            </li>
            <li class="px-6 font-bold hover:text-rose-700 transition-all hover:underline"><a target="_blank" href="{{route('blog')}}">Blog</a>
            </li>
        </ul>
    </nav>
    <section id="le-info-container" class="mx-4 lg:w-10/12 lg:flex justify-end lg:mx-auto relative z-10">
        <div class="p-2 my-auto absolute top-0 translate-y-1/2 flex flex-col justify-end text-right">
            <span class="text-4xl">David
            </span>
            <H2 class="pt-0">Luan Erazo</H2>
            <div class="m-2 mr-0 flex flex-col text-2xl">
                <span>Interactive Designer</span>
                <span>Web Development</span>
            </div>
        </div>
    </section>
</body>

</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Blog</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-black">
    <section class="p-1">
        <figure class="overflow-hidden lg:max-h-96">
            <picture>
                <img class="-translate-y-1/4" src="{{ $cover }}" alt="">
            </picture>
        </figure>
        <div class="p-1">
            <div>

                <h2 class="text-white">
                    {{ $title }}
                </h2>
                @php
                  // dd($notionInfo);
                @endphp
                @foreach ($notionInfo as $item)
                    <article class="">
                        <div class="p-4 my-4 bg-gray-700 rounded-sm">
                            <h5 class="font-bold text-xl">
                                {{ $item->getTitle() }}
                            </h5>
                            <p class="p-4">
                                {{ $firstChild}}
                            </p>
                            <a class="p-4 flex text-right" href={{$item->getUrl()}} target="_blank" rel="noopener noreferrer">Leer en notion</a>
                        </div>  
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    <section></section>
</body>

</html>

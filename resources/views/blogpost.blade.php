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
    <section class="p-10">
        <figure class="overflow-hidden lg:max-h-96">
            <picture>
                <img class="-translate-y-1/4" src="{{ $cover }}" alt="">
            </picture>
        </figure>
        <div class="grid grid-cols-2">
            <div>

                <h2 class="text-white">
                    {{ $title }}
                </h2>
                @php
                  // dd($notionInfo);
                @endphp
                @foreach ($notionInfo as $item)
                    <article class="py-8">
                        <div>
                            <h5 class="font-bold text-xl">
                                {{ $title }}
                            </h5>
                            <p class="p-4">
                                {{ $item['child']}}
                            </p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    <section></section>
</body>

</html>

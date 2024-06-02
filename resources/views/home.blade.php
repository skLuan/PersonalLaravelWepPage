<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Luan Erazo Portfolio</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-black">
    <section class="p-10">
        <figure class="overflow-hidden lg:max-h-96">
            <picture>
                <img class="-translate-y-1/4" src="{{$cover}}" alt="">
            </picture>
        </figure>
        <article class="grid grid-cols-2">
            <div>

                <h2 class="text-white">
                    {{ $title }}
                </h2>
                @php
                    // dd($cover);
                @endphp
                <div>
                    @foreach ($notionInfo as $block)
                        @php
                            if ($block->getType() === 'divider') {
                                return;
                            }
                        @endphp

                        @switch($block->getType())
                            @case('heading_2')
                                <h3 class=""> {{ $block->asText() }} </h3>
                            @break

                            @case('paragraph')
                                <p class="text-white p-2">  {{ $block->asText() }} </p>
                            @break

                            @default   <p class="text-white">  {{ $block->asText() }} </p>
                            @break
                        @endswitch
                    @endforeach
                </div>
            </div>
            <div>

            </div>
        </article>
    </section>
    <div>
        @php
            // dd($title);
            // dd($childrenInfo);
            // dd($notionInfo);
        @endphp
    </div>
</body>

</html>

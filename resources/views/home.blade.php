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
        <section class="px-6">
            <article>
                <h2 class="text-white">
                    {{$title}}
                </h2>
            </article>
        </section>
        <div>
            @php
                // dd($title);
                // dd($childrenInfo);
            @endphp
        </div>
    </body>
</html>



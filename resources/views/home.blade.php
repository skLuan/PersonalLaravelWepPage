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

<body class="antialiased bg-black pb-12">
    <x-three-canvas />
    <section class="m-2 mx-4 lg:w-10/12 lg:mx-auto">
        <div class="p-2 flex flex-col justify-end text-right">
            <span>David
            </span>
            <H2 class="pt-2">Luan Erazo</H2>
            <div class="m-4 flex flex-col ">
                <span>Interactive Designer</span>
                <span>Web Development</span>
            </div>
        </div>

        <div class="mt-40">
            <h4>Creador digital, “tech savvy”</h4>
            <p>Ciencia, viajar, conocer, ser curioso, soñando despierto con creatividad. soy un diseñador de medios
                interactivos y un entusiasta de la cien..</p>
        </div>
        {{-- <article class="p-4 rounded-sm bg-gray-950 bg-opacity-90">            
            <div>
                <h2 class="text-white">
                    {{ $title }}
                </h2>
                <div>
                    @foreach ($notionInfo as $block)
                        @php
                            if ($block->getType() === 'divider') {
                                continue;
                            }
                        @endphp

                        @switch($block->getType())
                            @case('heading_2')
                                <h3 class=""> {{ $block->asText() }} </h3>
                            @break

                            @case('paragraph')
                                <p class="p-2 text-white"> {{ $block->asText() }} </p>
                            @break

                            @default
                                <p class="text-white"> {{ $block->asText() }} </p>
                            @break
                        @endswitch
                    @endforeach
                </div>
            </div>
        </article> --}}
    </section>
    <section class="m-1">
        {{-- <article class="p-4 rounded-sm bg-gray-950 bg-opacity-90">
            <div>
                <h2 class="text-white">
                    About Me
                </h2>
                <h3 class="font-bold">Creador digital, “tech savvy”</h3>
                <div>
                    <p>
                        Ciencia, viajar, conocer, ser curioso, soñando despierto con creatividad. soy un diseñador de
                        medios interactivos y un entusiasta de la ciencia, me gusta saber por qué las cosas son como
                        son, inspirándome en la naturaleza de las cosas. Me gusta ver las cosas con una perspectiva
                        diferente, crear y diseñar son una de las formas en la que me expreso.

                        Ser empático, respetuoso, objetivo y calido son grandes ingredientes para trabajar con otros.
                        Permanecer curioso y escéptico crea soluciones por fuera de la caja.
                        Enfocarse en el usuario final, mientras se tiene el negocio en la mente son claves para
                        desarrollar soluciones practicas y usables en un mundo de crecimiento rápido, Tener un ojo en el
                        futuro es critico para buscar mejores soluciones para los problemas de hoy, siempre buscando en
                        mejorar la experiencia del usuario
                    </p>
                </div>
            </div>
        </article> --}}
    </section>
    <x-tabbar3d />
</body>

</html>

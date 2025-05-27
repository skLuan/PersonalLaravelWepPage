@extends('layouts.app')
@section('content')
    <x-three-canvas />
    <section class="m-2 mx-4 lg:w-10/12 lg:mx-auto">
        <div class="p-2 flex flex-col justify-end text-right">
            <span>David
            </span>
            <H2 class="pt-2">Luan Erazo</H2>
        </div>

        <div class="mt-20">
            <h4>My space in the web of interconected machines, and sometimes, humans</h4>
            <p>Hobbies sometimes blends with passion, with work. Creativity is the tendency
                of try to do it yourself, using a guide with diferent materials. Here I am, experimenting with my human
                capacity
            </p>
        </div>
    </section>
    <section>
        <ul class="text-center my-14">
            <li class="p-1"><a class="p-2 border block font-bold text-lg border-skl-pink rounded-lg bg-skl-black-90" href="/blog">Blog</a></li>
            <li class="p-1"><a class="p-2 border block font-bold text-lg border-skl-pink rounded-lg bg-skl-black-90" href="/portfolio">Portfolio</a></li>
            {{-- <li><a href="/blog">Experiments</a></li> --}}
        </ul>
    </section>
    <section class="m-1">
        <article class="p-4 rounded-sm bg-gray-950 bg-opacity-90">
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
        </article>
    </section>
@endsection
        {{-- Notion section --}}
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
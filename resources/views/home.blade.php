@extends('layouts.app')
@section('content')
    <x-three-canvas />
    <section class="m-2 mx-4 lg:w-10/12 lg:mx-auto min-h-[100dvh] lg:min-h-[unset] flex flex-col">
        <div class="p-2 mt-8 flex flex-col justify-end text-right">
            <span class="text-3xl w-fit shadow-md shadow-skl-grey py-1 px-4 bg-skl-black-50">David
            </span>
            <H2 class="pt-0 pb-2 w-fit bg-skl-black-50 shadow-lg rounded shadow-skl-purple p-1">Luan Erazo</H2>
        </div>
        <ul class="flex-row justify-evenly items-center my-auto lg:my-24 relative hidden lg:flex lg:w-1/2">
            <li class=" items-end flex">
                <a class="text-center p-4 !min-w-40 bg-skl-black-90 border-2 border-y-transparent border-x-skl-yellow rounded-full px-8 text-skl-yellow no-underline font-skl-titles text-xl font-extrabold"
                    href="{{ url('/blog') }}">Blog</a>
            </li>
            <li class=" items-end flex">
                <a class="text-center p-4 !min-w-40 bg-skl-yellow rounded-full px-8 text-skl-grey no-underline font-skl-titles text-xl font-extrabold"
                    href="{{ url('/portfolio') }}">Portfolio</a>
            </li>
        </ul>
        <div class="mt-auto lg:mt-6 lg:w-8/12 bg-skl-black-90 rounded-sm shadow-sm shadow-skl-grey p-2">
            <h4>My space in the web of interconected machines, and sometimes, humans</h4>
            <p>Hobbies sometimes blends with passion, with work. Creativity is the tendency
                of try to do it yourself, using a guide with diferent materials. Here I am, experimenting with my human
                capacity
            </p>
        </div>
        <ul class="flex flex-col justify-center items-center my-auto relative lg:hidden">
            <li class=" items-end flex  mb-10">
                <a class="text-center p-4 !min-w-40 bg-skl-black-90 border-2 border-y-transparent border-x-skl-yellow rounded-full px-8 text-skl-yellow no-underline font-skl-titles text-xl font-extrabold"
                    href="{{ url('/blog') }}">Blog</a>
            </li>
            <li class=" items-end flex">
                <a class="text-center p-4 !min-w-40 bg-skl-yellow rounded-full px-8 text-skl-grey no-underline font-skl-titles text-xl font-extrabold"
                    href="{{ url('/portfolio') }}">Portfolio</a>
            </li>
        </ul>
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

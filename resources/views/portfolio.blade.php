@php
    $title = __('portfolio.title');
@endphp
@extends('layouts.app', ['title' => $title])

@section('content')
    <x-three-canvas />
    <section id="hero">

    </section>
    <section id="aboutMe" class="pt-6 lg:w-8/12 mx-auto">
        <article id="le-info-container" class="relative z-10  min-h-[83dvh] flex flex-col">
            <div class="p-2 flex flex-col justify-start">
                <figure
                    class="to-path mb-4 rounded-full overflow-hidden w-2/3 lg:w-2/5 hover:w-3/6 transition-all border-skl-purple border-2">
                    <picture><img class="" src="/imgs/Luan_square.png" alt=""></picture>
                </figure>
                <span class="shadow-xl px-2 shadow-skl-purple w-fit">
                    <span class="text-4xl font-skl-titles bg-skl-black-50 w-fit">{{ __('portfolio.hero.name') }}
                    </span>
                    <H2 class="pt-0 pb-2 w-fit bg-skl-black-50 shadow p-1">{{ __('portfolio.hero.name') }}</H2>
                </span>
                <div class="m-1 mr-0 flex flex-col text-2xl mt-2">
                    <span class="bg-skl-black-90 w-fit shadow-md shadow-skl-grey py-1 px-4">{{ __('portfolio.hero.title1') }}</span>
                    <span class="bg-skl-black-90 w-fit shadow-md shadow-skl-grey py-1 px-4">{{ __('portfolio.hero.title2') }}</span>
                </div>
            </div>
            <ul class="flex flex-row justify-around my-auto bottom-0 relative">
                <li class=" items-end flex">
                    <a class="text-right p-4 bg-skl-black-90 border-2 border-y-transparent border-x-skl-yellow rounded-full px-8 text-skl-yellow no-underline font-skl-titles text-xl font-extrabold"
                        href="#whatIDo">{{ __('portfolio.hero.link_what_i_do') }}</a>
                </li>
                <li class=" items-end flex">
                    <a class="text-right p-4 bg-skl-yellow rounded-full px-8 text-skl-grey no-underline font-skl-titles text-xl font-extrabold"
                        href="#Projects">{{ __('portfolio.hero.link_projects') }}</a>
                </li>
            </ul>
        </article>
        <article>
            <div class="bg-skl-black-50 relative">
                <x-title-web id="title-about-me"
                    class="justify-end sticky top-0 bg-gradient-to-b from-[#0A090B] to-[#0A090B00] mb-0">{{ __('portfolio.about_me.title') }}</x-title-web>
                <p>
                    {{ __('portfolio.about_me.paragraph1') }}
                </p>
                <p>
                    {{ __('portfolio.about_me.paragraph2') }}
                </p>
            </div>
        </article>
    </section>
    <section id="whatIDo" class="pt-6 lg:w-10/12 mx-auto relative">
        <x-title-web class=" sticky top-0 z-20 bg-gradient-to-b from-[#0A090B] to-[#0A090B00] mb-0">{{ __('portfolio.what_i_do.title') }}</x-title-web>
        <div class="relative">
            <h4 class="text-right sticky z-20 top-6 p-3 shadow-md text-skl-yellow">{{ __('portfolio.what_i_do.design') }}</h4>
            <article class=" md:grid grid-cols-3 gap-4 z-10">
                <x-card.simple iconName="ri:mind-map">
                    <x-slot:title>{{ __('portfolio.what_i_do.services.navigation_optimization.title') }}</x-slot>
                    {{ __('portfolio.what_i_do.services.navigation_optimization.description') }}
                </x-card.simple>
                {{-- ----------------- --}}
                <x-card.simple iconName="devicon:figma">
                    <x-slot:title>{{ __('portfolio.what_i_do.services.custom_interface_design.title') }}</x-slot>
                    {{ __('portfolio.what_i_do.services.custom_interface_design.description') }}
                </x-card.simple>
                {{-- ----------------- --}}
                <x-card.simple iconName="material-symbols-light:design-services-outline-rounded">
                    <x-slot:title>{{ __('portfolio.what_i_do.services.digital_product_creation.title') }}</x-slot>
                    {{ __('portfolio.what_i_do.services.digital_product_creation.description') }}
                </x-card.simple>
                {{-- ----------------- --}}
            </article>
        </div>
        <div class="relative">
            <h4 class="text-right sticky z-20 top-6 p-3 shadow-md text-skl-yellow">{{ __('portfolio.what_i_do.development') }}</h4>
            <article class=" md:grid grid-cols-3 gap-4">
                <x-card.simple iconName="system-uicons:code">
                    <x-slot:title>{{ __('portfolio.what_i_do.dev_services.custom_web_experience.title') }}</x-slot>
                    {{ __('portfolio.what_i_do.dev_services.custom_web_experience.description') }}
                </x-card.simple>
                {{-- ----------------- --}}
                <x-card.simple iconName="eos-icons:performance">
                    <x-slot:title>{{ __('portfolio.what_i_do.dev_services.web_performance_optimization.title') }}</x-slot>
                    {{ __('portfolio.what_i_do.dev_services.web_performance_optimization.description') }}
                </x-card.simple>
                {{-- ----------------- --}}
                <x-card.simple iconName="mdi:cloud-refresh-outline">
                    <x-slot:title>{{ __('portfolio.what_i_do.dev_services.technological_optimization.title') }}</x-slot>
                    {{ __('portfolio.what_i_do.dev_services.technological_optimization.description') }}
                </x-card.simple>
                {{-- ----------------- --}}
            </article>
        </div>
    </section>
    <section id="Projects" class="pt-6 mx-auto lg:w-11/12">
        <x-title-web id="title-about-me"
            class="justify-end sticky top-0 bg-gradient-to-b from-[#0A090B] to-[#0A090B00] mb-0">{{ __('portfolio.projects.title') }}</x-title-web>
        <ul>
            <li><button>{{ __('portfolio.projects.filter_web_product') }}</button></li>
            <li><button>{{ __('portfolio.projects.filter_graphic_design') }}</button></li>
        </ul>
        <ul class="md:grid md:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse ($projects as $project)
                <li class="my-4">
                    <x-card.project :$project />
                </li>
            @empty
                <p class="text-gray-400">{{ __('portfolio.projects.no_projects') }}</p>
            @endforelse
        </ul>
    </section>
    <section id="Skills" class="pt-6 lg:w-8/12 mx-auto">

    </section>
    <section id="letsWork" class="pt-6 lg:w-8/12 mx-auto">

    </section>
@endsection

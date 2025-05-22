@php
    $title = 'Portfolio';
@endphp
@extends('layouts.app', ['title' => $title])

@section('content')
    <x-three-canvas />
    <section id="hero">

    </section>
    <section id="aboutMe" class="pt-6 lg:w-8/12 mx-auto" >
        <article id="le-info-container" class="relative z-10">
            <div class="p-2 my-auto flex flex-col justify-start">
                <figure class="rounded-full overflow-hidden w-2/3 lg:w-2/5 hover:w-3/6 transition-all border-skl-purple border-2">
                    <picture><img class="" src="/imgs/Luan_square.png" alt=""></picture>
                </figure>
                <span class="text-4xl font-skl-titles">David
                </span>
                <H2 class="pt-0 pb-2">Luan Erazo</H2>
                <div class="m-1 mr-0 flex flex-col text-2xl">
                    <span>Interactive Designer</span>
                    <span>Web Development</span>
                </div>
            </div>
        </article>
        <article class="">
            <ul>
                <li class=" items-end flex">
                    <a class="text-right py-0 ml-auto" href="#whatIDo">What I do</a>
                </li>
                <li class=" items-end flex">
                    <a class="text-right py-0 ml-auto" href="#Projects">Projects</a>
                </li>
                <li>
                    <x-title-web class="justify-end">About Me</x-title-web>
                </li>
            </ul>
            <p>
                I am a self-taught digital creator with over 4 years of experience in web design and development, passionate
                about continuous learning and innovation.

                My multidisciplinary approach allows me to tackle projects from different perspectives, integrating UX
                Design, Frontend Development, and knowledge of digital marketing to provide complete, user-centered
                solutions.

                I draw inspiration from science, curiosity, and creativity, always striving to understand the nature of
                things and explore new ways to solve problems. With skills in PHP, HTML, CSS, JS, and their frameworks, I
                combine technical thinking with design to create intuitive and efficient user experiences.
            </p>
            <p>
                I value empathy and critical thinking, which enables me to connect with user needs while also considering
                business objectives.

                I always keep an eye on the future, exploring new technologies and methods to improve digital experiences. I
                seek to work in dynamic environments that foster creativity and innovation, where I can continue creating
                solutions that make a difference.

                Currently based in Cali, Colombia, I am fluent in Spanish and English, which allows me to collaborate with
                global teams without communication barriers.
            </p>
        </article>
    </section>
    <section id="whatIDo" class="pt-6 lg:w-10/12 mx-auto">
        <x-title-web>What I Do</x-title-web>
        <div class="relative">
            <h4 class="text-right sticky top-0 p-3 shadow-md text-skl-yellow">Design</h4>
            <article class=" md:grid grid-cols-3 gap-4">
                <x-card.simple iconName="ri:mind-map">
                    <x-slot:title>Navigation Optimization</x-slot>
                    Design of clear, logical structures to improve the user experience in web navigation. I analyze
                    information
                    architecture to ensure that users can easily find what they are looking for.
                </x-card.simple>
                {{-- ----------------- --}}
                <x-card.simple iconName="devicon:figma">
                    <x-slot:title>Custom Interface Design</x-slot>
                    Creation of visually appealing and functional interfaces tailored to the client’s needs, focused on user
                    experience. I strive to balance aesthetics and functionality to guarantee a smooth and memorable
                    experience.
                </x-card.simple>
                {{-- ----------------- --}}
                <x-card.simple iconName="material-symbols-light:design-services-outline-rounded">
                    <x-slot:title>Digital Product Creation</x-slot>
                    Design and development of innovative digital solutions, from applications to interactive experiences,
                    always
                    centered on improving the end-user experience.
                </x-card.simple>
                {{-- ----------------- --}}
            </article>
        </div>
        <div class="relative">
            <h4 class="text-right sticky top-0 p-3 shadow-md text-skl-yellow">Development</h4>
            <article class=" md:grid grid-cols-3 gap-4">
                <x-card.simple iconName="system-uicons:code">
                    <x-slot:title>Custom <br> Web Experience</x-slot>
                    Development of tailor-made websites designed to provide a smooth and intuitive user experience, adapted
                    to the specific needs of your project.
                </x-card.simple>
                {{-- ----------------- --}}
                <x-card.simple iconName="eos-icons:performance">
                    <x-slot:title>Web Performance Optimization</x-slot>
                    Implementation of improvements to increase website loading speed and efficiency, optimizing both
                    performance and SEO rankings.
                </x-card.simple>
                {{-- ----------------- --}}
                <x-card.simple iconName="mdi:cloud-refresh-outline">
                    <x-slot:title>Technological Optimization</x-slot>
                    Evaluation and improvement of the technological tools used in your project, ensuring their effective
                    integration into your business infrastructure to maximize efficiency and scalability.
                </x-card.simple>
                {{-- ----------------- --}}
            </article>
        </div>
    </section>
    <section id="Projects" class="pt-6 mx-auto lg:w-11/12">
        <x-title-web class="justify-end w-8/12 mx-auto">Projects</x-title-web>
        <ul>
            <li><button>Web & Product</button></li>
            <li><button>Graphic Design</button></li>
        </ul>
        <ul class="md:grid md:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse ($projects as $project)
                <li>
                    <x-card.project :$project />
                </li>
            @empty
                <p class="text-gray-400">No hay proyectos disponibles.</p>
            @endforelse
        </ul>
    </section>
    <section id="Skills" class="pt-6 lg:w-8/12 mx-auto">

    </section>
    <section id="letsWork" class="pt-6 lg:w-8/12 mx-auto">

    </section>
@endsection

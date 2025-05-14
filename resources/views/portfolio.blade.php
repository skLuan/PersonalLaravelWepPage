@php
    $title = 'Portfolio';
@endphp
@extends('layouts.app', ['title' => $title])

@section('content')
    <section id="hero">

    </section>
    <section id="aboutMe" class="pt-6">
        <article id="le-info-container" class="lg:w-10/12 lg:flex justify-end lg:mx-auto relative z-10">
            <div class="p-2 my-auto flex flex-col justify-start">
                <figure class="rounded-full overflow-hidden w-4/6 border-skl-purple border-2">
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
        <article>
            <x-title-web class="justify-end">About Me</x-title-web>
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
    <section id="whatIDo">
        <x-title-web>What I Do</x-title-web>

    </section>
    <section id="Projects">

    </section>
    <section id="Skills">

    </section>
    <section id="letsWork">

    </section>
@endsection

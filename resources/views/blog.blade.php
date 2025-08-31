<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Blog</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ env('GOOGLE_TAG_ID') }}"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', '{{ env('GOOGLE_TAG_ID') }}');
</script>

<body class="antialiased bg-skl-black font-skl-nunito">
    <div class="container max-w-screen-2xl mx-auto px-4">
        <section class="" id="heroOne">
            <article class="w-1/2 lg:w-1/3 mx-auto p-12">
                <h3>Reflexiones sobre existir como humano.</h3>
                <p>Humanos arraigados al tiempo y no a la materia.</p>
            </article>
            <figure class="w-full relative">
                <picture>
                    <img class="" width="100%" src="/imgs/hero1.jpg" alt="">
                </picture>
                <h2 class="text-skl-white-true bg-skl-black px-20 py-10 my-12 absolute top-0">
                    Blog
                </h2>
            </figure>
        </section>
        <section class="p-1 grid grid-cols-3 gap-6" id="blogCards">
            @php
                // dd($notionInfo);
            @endphp
            @foreach ($notionInfo as $item)
                <article class="">
                    <figure class="w-full max-h-32 overflow-hidden">
                        <picture>
                            <img class="relative -translate-y-1/2" src={{ $item->getCover() }} alt="">
                        </picture>
                    </figure>
                    <h5 class="font-bold text-xl pt-4 pb-2 px-2 text-skl-white-pink">
                        {{ $item->getTitle() }}
                    </h5>
                    <div class="p-2 rounded-sm">
                        <p class="">
                            @php
                                // $blocks = $this->notionInstance
                                //     ->block($id)
                                //     ->limit($amount)
                                //     ->children()
                                //     ->withUnsupported()
                                //     ->asTextCollection();

                                $infoCard = Notion::block($item->getID())->limit(1)->children()->asTextCollection();

                            @endphp
                            {{ $infoCard }}
                        </p>
                        <a class="p-4 flex text-right" href={{ $item->getUrl() }} target="_blank"
                            rel="noopener noreferrer">Leer en notion</a>
                    </div>
                </article>
            @endforeach
        </section>
    </div>
</body>

</html>

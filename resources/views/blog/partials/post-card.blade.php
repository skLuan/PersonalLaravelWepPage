{{-- One post in a list. $headingLevel: 2 on the index (the page h1 is "Blog"). --}}
@php($tag = 'h' . ($headingLevel ?? 2))
<article class="px-2 lg:my-12 bg-skl-black-50">
    <div class="border-l border-skl-grey rounded-sm shadow-lg shadow-skl-grey">
        <div class="flex flex-col-reverse lg:flex-row justify-between items-center lg:mr-6">
            <header class="text-left lg:px-6 w-full">
                <{{ $tag }} class="bg-skl-black-50 w-fit text-2xl">
                    <a href="{{ $post->url() }}" class="no-underline hover:underline text-white hover:text-skl-pink">{{ $post->title }}</a>
                </{{ $tag }}>
                @if ($post->subtitle)
                    <p class="font-skl-nunito pl-4 leading-6 lg:w-11/12 pt-4 text-white bg-skl-black-50 w-fit">{{ $post->subtitle }}</p>
                @endif
            </header>
            @if ($post->coverUrl())
                <figure class="block p-0 lg:w-1/4 w-full overflow-hidden rounded-sm h-28 flex justify-center items-center bg-gray-300">
                    <img src="{{ $post->coverUrl() }}" alt="{{ $post->cover_alt ?? '' }}" loading="lazy" class="w-full h-full object-cover">
                </figure>
            @endif
        </div>
        <div class="p-3 pt-0 flex flex-col h-full mb-10">
            <p class="text-skl-white w-fit bg-skl-black-90 lg:w-3/4">{{ $post->summary() }}</p>
            <p class="text-sm opacity-75">
                <time datetime="{{ $post->published_at?->toAtomString() }}">{{ $post->published_at?->translatedFormat('j F Y') }}</time>
                @foreach ($post->categories as $category)
                    · <a href="{{ $category->url() }}" class="text-skl-yellow">{{ $category->name }}</a>
                @endforeach
            </p>
        </div>
    </div>
</article>

@extends('layouts.app', ['title' => $post->seo?->title ?: $post->title])

@section('content')
    <div class="pt-24 w-full px-4">
        <x-darkmode-button />

        @unless ($post->isPublished())
            <p role="status" class="lg:w-7/12 lg:mx-auto border border-skl-yellow text-skl-yellow p-2 my-4">{{ __('blog.draft_notice') }}</p>
        @endunless

        <nav aria-label="{{ __('blog.breadcrumb') }}" class="lg:w-7/12 lg:mx-auto text-sm mt-8">
            <a href="{{ route('blog.index') }}" class="text-skl-yellow">{{ __('blog.title') }}</a>
            @if ($category = $post->categories->first())
                / <a href="{{ $category->url() }}" class="text-skl-yellow">{{ $category->name }}</a>
            @endif
        </nav>

        <article class="lg:w-7/12 lg:mx-auto">
            <header class="blog_post_header relative mb-12">
                <h1 class="blog_title lg:mt-16 text-shadow-md dark:bg-skl-black-50 w-full">{{ $post->title }}</h1>
                <div class="flex flex-col lg:flex-row items-center">
                    @if ($post->subtitle)
                        <p class="blog_subtitle text-xl font-skl-titles text-shadow-md dark:bg-skl-black-50 w-full mb-12 lg:mb-0">{{ $post->subtitle }}</p>
                    @endif
                    @if ($post->coverUrl())
                        <figure class="overflow-hidden md:h-80 lg:h-auto lg:ml-auto relative rounded-sm -z-10 lg:w-7/12 right-0 bg-gray-200">
                            <img src="{{ $post->coverUrl() }}" alt="{{ $post->cover_alt ?? '' }}" class="w-full md:-translate-y-1/2 lg:translate-y-0">
                        </figure>
                    @endif
                </div>
                <p class="text-sm opacity-75 mt-4">
                    {{ __('blog.published') }}
                    <time datetime="{{ $post->published_at?->toAtomString() }}">{{ $post->published_at?->translatedFormat('j F Y') }}</time>
                    @if ($post->author)
                        · {{ __('blog.by') }} {{ $post->author->name }}
                    @endif
                </p>
            </header>

            {{-- Body is HTML written in the admin editor (trusted, admin-only). --}}
            <div class="blog_body_content">
                {!! $post->body !!}
            </div>

            <footer class="border-t border-skl-purple p-2 mt-8">
                @if ($post->categories->isNotEmpty())
                    <p>{{ __('blog.categories') }}:
                        @foreach ($post->categories as $category)
                            <a href="{{ $category->url() }}" class="text-skl-yellow">{{ $category->name }}</a>@if (! $loop->last), @endif
                        @endforeach
                    </p>
                @endif
                @if ($translations->isNotEmpty())
                    <p>{{ __('blog.also_in') }}:
                        @foreach ($translations as $translation)
                            <a href="{{ $translation->url() }}" hreflang="{{ $translation->locale }}" lang="{{ $translation->locale }}" class="text-skl-yellow">{{ $translation->title }}</a>
                            ({{ __('blog.locale.' . $translation->locale) }})@if (! $loop->last), @endif
                        @endforeach
                    </p>
                @endif
                @if (auth()->user()?->is_admin)
                    <p><a href="{{ route('filament.admin.resources.posts.edit', $post) }}" class="text-skl-pink">{{ __('blog.edit') }}</a></p>
                @endif
                <p><a href="{{ route('blog.index') }}" class="text-skl-yellow">{{ __('blog.back') }}</a></p>
            </footer>
        </article>
    </div>
@endsection

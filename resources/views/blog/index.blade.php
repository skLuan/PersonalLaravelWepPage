@extends('layouts.app', ['title' => $category ? $category->name . ' · ' . __('blog.title') : __('blog.title')])

@section('content')
    <x-three-canvas />
    <div class="relative z-10 px-4 lg:px-16">
        <header class="mb-12 lg:min-h-[40dvh] mt-36">
            @if ($category)
                <nav aria-label="{{ __('blog.breadcrumb') }}" class="mb-4 text-sm">
                    <a href="{{ route('blog.index') }}" class="text-skl-yellow">{{ __('blog.title') }}</a> / {{ $category->name }}
                </nav>
                <h1 class="text-skl-white-true rounded-sm bg-skl-purple px-6 py-6 my-12 shadow-xl w-fit shadow-skl-purple">{{ $category->name }}</h1>
                @if ($category->description)
                    <p class="bg-skl-black-50 inline-block">{{ $category->description }}</p>
                @endif
            @else
                <h1 class="text-skl-white-true rounded-sm bg-skl-purple px-6 py-6 my-12 shadow-xl w-fit shadow-skl-purple">{{ __('blog.title') }}</h1>
                <div class="px-2 shadow-xl shadow-skl-grey rounded-sm lg:w-1/2">
                    <p class="bg-skl-black-50 inline text-xl font-skl-titles">{{ __('blog.tagline') }}</p>
                    <p class="bg-skl-black-50 inline-block">{{ __('blog.intro') }}</p>
                </div>
            @endif
            @include('blog.partials.search-form')
        </header>

        <section class="mx-auto lg:max-w-[80%]" aria-label="{{ __('blog.title') }}">
            @if ($posts->isEmpty())
                <p>{{ __('blog.empty') }}</p>
            @else
                <ol class="flex flex-col list-none p-0">
                    @foreach ($posts as $post)
                        <li>@include('blog.partials.post-card', ['headingLevel' => 2])</li>
                    @endforeach
                </ol>
            @endif

            @if ($posts->hasPages())
                <nav aria-label="{{ __('blog.pagination') }}" class="my-8">
                    {{ $posts->links() }}
                </nav>
            @endif

            <p class="text-sm my-6"><a href="{{ route('blog.feed') }}" class="text-skl-yellow">{{ __('blog.feed.link') }}</a></p>
        </section>
    </div>
@endsection

@extends('layouts.app', ['title' => __('blog.search.title') . ' · ' . __('blog.title')])

@section('content')
    <x-three-canvas />
    <div class="relative z-10 px-4 lg:px-16 mt-36">
        <nav aria-label="{{ __('blog.breadcrumb') }}" class="mb-4 text-sm">
            <a href="{{ route('blog.index') }}" class="text-skl-yellow">{{ __('blog.title') }}</a> / {{ __('blog.search.title') }}
        </nav>
        <h1 class="text-skl-white-true rounded-sm bg-skl-purple px-6 py-6 my-6 shadow-xl w-fit shadow-skl-purple">
            {{ $term !== '' ? __('blog.search.results_for', ['term' => $term]) : __('blog.search.title') }}
        </h1>
        @include('blog.partials.search-form')

        <section class="mx-auto lg:max-w-[80%]">
            @if ($posts->isEmpty())
                <p>{{ __('blog.search.none') }}</p>
            @else
                <ol class="flex flex-col list-none p-0">
                    @foreach ($posts as $post)
                        <li>@include('blog.partials.post-card', ['headingLevel' => 2])</li>
                    @endforeach
                </ol>
                @if ($posts->hasPages())
                    <nav aria-label="{{ __('blog.pagination') }}" class="my-8">{{ $posts->links() }}</nav>
                @endif
            @endif
        </section>
    </div>
@endsection

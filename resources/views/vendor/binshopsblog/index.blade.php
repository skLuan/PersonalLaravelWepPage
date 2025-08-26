@extends('layouts.app', ['title' => $title])

@section('blog-custom-css')
    <link type="text/css" href="{{ asset('binshops-blog.css') }}" rel="stylesheet">
@endsection

@section('content')
    <x-three-canvas />
    <div class='col-sm-12 BinshopsBlog_container relative z-10'>
        <section class="row  mb-12" id="heroOne">
            <article class="px-24 pr-8 py-4">
                <h3>Reflexiones sobre existir como humano.</h3>
                <p>Humanos arraigados al tiempo y no a la materia.</p>
            </article>
            {{-- <figure class="w-full relative">
                <picture>
                    <source media="(min-width: 920px)" srcset="/imgs/hero1.jpg">
                    <img class="" width="100%" src="/blog_images/banner_mobile.png" alt="">
                </picture>
            </figure> --}}
            <h2 class="text-skl-white-true px-10 py-6 my-12">
                Blog
            </h2>
        </section>
        <div class="row" id="">
            <div class="col-md-9">

                @if ($category_chain)
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                @forelse($category_chain as $cat)
                                    / <a href="{{ $cat->url() }}">
                                        <span class="cat1">{{ $cat->category_name }}</span>
                                    </a>
                                @empty
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endif

                @if (isset($BinshopsBlog_category) && $BinshopsBlog_category)
                    <h2 class='text-center'> {{ $BinshopsBlog_category->category_name }}</h2>

                    @if ($BinshopsBlog_category->category_description)
                        <p class='text-center'>{{ $BinshopsBlog_category->category_description }}</p>
                    @endif
                @endif
                {{-- Inicio Loop --}}
                <div class="container cards mx-auto lg:max-w-[80%] items-center">
                    <div class="row lg:px-3 flex flex-col mx-auto" id="partial-container">
                        @forelse($posts as $post)
                            @include('binshopsblog::partials.index_loop')
                        @empty
                            <div class="col-md-12">
                                <div class='alert alert-danger'>No posts!</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <h6>Blog Categories</h6>
                @forelse($categories as $category)
                    <a href="{{ $category->url() }}">
                        <h6>{{ $category->category_name }}</h6>
                    </a>
                @empty
                    <a href="#">
                        <h6>No Categories</h6>
                    </a>
                @endforelse
            </div>
        </div>

        <div class='text-center  col-sm-4 mx-auto'>
            {{ $posts->appends([])->links() }}
        </div>
        @if (config('binshopsblog.search.search_enabled'))
            @include('binshopsblog::sitewide.search_form')
        @endif
    </div>

@endsection

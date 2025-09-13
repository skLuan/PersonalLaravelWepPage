@extends('layouts.app', ['title' => $post->gen_seo_title()])

@section('blog-custom-css')
    {{-- <link type="text/css" href="{{ asset('binshops-blog.css') }}" rel="stylesheet"> --}}
@endsection

@section('content')
    <div class='pt-24 w-full'>
        @if (config('binshopsblog.reading_progress_bar'))
            <div id="scrollbar">
                <div id="scrollbar-bg"></div>
            </div>
        @endif

        <div class='row'>
            <div class=''>
                <x-darkmode-button />

                @include('binshopsblog::partials.show_errors')
                @include('binshopsblog::partials.full_post_details')


                @if (config('binshopsblog.comments.type_of_comments_to_show', 'built_in') !== 'disabled')
                    <div class="" id='maincommentscontainer'>
                        <h2 class='text-center' id='BinshopsBlogcomments'>Comments</h2>
                        @include('binshopsblog::partials.show_comments')
                    </div>
                @else
                    {{-- Comments are disabled --}}
                @endif


            </div>
        </div>
    </div>
@endsection

@section('blog-custom-js')
    <script src="{{ asset('binshops-blog.js') }}"></script>
@endsection

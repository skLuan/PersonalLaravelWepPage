@if (\Auth::check() && \Auth::user()->canManageBinshopsBlogPosts())
    <a href="{{ $post->edit_url() }}" class="btn btn-outline-secondary btn-sm pull-right float-right">Edit
        Post</a>
@endif
<section class="blog_post_header relative mb-12 lg:w-7/12 lg:mx-auto">
    <h1 class='blog_title lg:mt-16 text-shadow-md dark:bg-skl-black-50 w-full'>{{ $post->title }}</h1>
    <div class="flex flex-col lg:flex-row items-center">
        <h5 class='blog_subtitle text-shadow-md dark:bg-skl-black-50 w-full mb-12 lg:mb-0'>{{ $post->subtitle }}</h5>
        <figure class="overflow-hidden md:h-80 lg:h-auto lg:ml-auto relative rounded-sm -z-10 lg:w-7/12 right-0 bg-gray-200">
            <picture>
                <?= $post->image_tag('large', false, 'w-full md:-translate-y-1/2 lg:translate-y-0') ?>
            </picture>
        </figure>
    </div>
</section>

<section>
    <article class="lg:w-7/12 lg:mx-auto">
        <p class="blog_body_content">
            {!! $post->post_body_output() !!}

            {{-- @if (config('binshopsblog.use_custom_view_files') && $post->use_view_file) --}}
            {{--                                // use a custom blade file for the output of those blog post --}}
            {{--   @include("binshopsblog::partials.use_view_file") --}}
            {{-- @else --}}
            {{--   {!! $post->post_body !!}        // unsafe, echoing the plain html/js --}}
            {{--   {{ $post->post_body }}          // for safe escaping --}}
            {{-- @endif --}}
        </p>
        <div class="border-t border-skl-purple p-2">
            Posted <strong>{{ $post->posted_at->diffForHumans() }}</strong>

            @includeWhen($post->author, 'binshopsblog::partials.author', ['post' => $post])
            @includeWhen($post->categories, 'binshopsblog::partials.categories', ['post' => $post])
        </div>
    </article>
</section>

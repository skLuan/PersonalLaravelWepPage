@if (\Auth::check() && \Auth::user()->canManageBinshopsBlogPosts())
    <a href="{{ $post->edit_url() }}" class="btn btn-outline-secondary btn-sm pull-right float-right">Edit
        Post</a>
@endif
<section class="blog_post_header relative flex flex-col-reverse lg:flex-row items-end gap-4 mb-12">
    <div class="lg:w-6/12 top-0 lg:mt-16 text-shadow-md bg-skl-black-50">
        <h1 class='blog_title'>{{ $post->title }}</h1>
        <h5 class='blog_subtitle'>{{ $post->subtitle }}</h5>
    </div>
    <figure class="overflow-hidden md:h-80 lg:h-auto lg:ml-auto relative rounded-sm -z-10 lg:w-7/12 right-0">
        <picture>
            <?= $post->image_tag('large', false, 'w-full md:-translate-y-1/2 lg:translate-y-0') ?>
        </picture>
    </figure>
</section>

<section>
    <article class="lg:w-7/12 lg:ml-6">
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

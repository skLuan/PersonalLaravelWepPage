<div class="px-1">
    <div class="blog-item border-l border-skl-grey bg-skl-black-90">
        <div class='text-center blog-image flex flex-col-reverse lg:flex-row justify-between items-center'>
            <a href='{{ $post->url() }}' class="text-left lg:px-6 no-underline hover:underline text-skl-white hover:text-skl-pink">
                <h4 class='pb-2'>{{ $post->title }}</h4>
                <h5 class='font-skl-nunito'>{{ $post->subtitle }}</h5>
            </a>
            <a href="{{ $post->url() }}" class="block p-0 lg:w-1/4">
                <figure class="overflow-hidden rounded-sm h-28 flex justify-center items-center">
                    <picture>
                        <?= $post->image_tag('medium', false, '') ?>
                    </picture>
                </figure>
            </a>
        </div>
        <div class="blog-inner-item p-3 flex flex-col lg:pt-0 h-full">

            @if (config('binshopsblog.show_full_text_at_list'))
                <p>{!! $post->post_body_output() !!}</p>
            @else
                <p class="mt-auto">{!! mb_strimwidth($post->post_body_output(), 0, 200, '...') !!}</p>
            @endif
            {{-- 
            <div class="post-details-bottom">
                <span class="light-text">Authored by: </span> {{ $post->author->name }} <span class="light-text">Posted
                    at: </span> {{ date('d M Y ', strtotime($post->posted_at)) }}
            </div> --}}
            <div class='text-right'>
                <a href="{{ $post->url() }}"
                    class="btn btn-primary  border border-skl-purple rounded-ms mt-4 block w-fit ml-auto px-6">View
                    Post</a>
            </div>
        </div>
    </div>

</div>

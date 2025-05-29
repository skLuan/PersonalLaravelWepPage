<div class="col-md-6">
    <div class="blog-item border-l border-skl-grey">
        <div class='text-center blog-image'>
            <a href="{{ $post->url() }}" class="block p-0">
                <figure class="overflow-hidden rounded-sm h-40 flex justify-center items-center">
                    <picture>
                        <?= $post->image_tag('medium', false, '') ?>
                    </picture>
                </figure>
            
            </a>
        </div>
        <div class="blog-inner-item p-3 flex flex-col pt-0 h-full">
            <h3 class=''><a href='{{ $post->url() }}'>{{ $post->title }}</a></h3>
            <h5 class=''>{{ $post->subtitle }}</h5>

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
                <a href="{{ $post->url() }}" class="btn btn-primary  border border-skl-purple rounded-ms mt-4 block w-fit ml-auto px-6">View Post</a>
            </div>
        </div>
    </div>

</div>

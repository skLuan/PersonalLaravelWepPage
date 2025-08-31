<div class="px-2 lg:my-12 bg-skl-black-50">
    <div class="blog-item border-l border-skl-grey rounded-sm shadow-lg shadow-skl-grey">
        <div class='text-center blog-image flex flex-col-reverse lg:flex-row justify-between items-center lg:mr-6'>
            <a href='{{ $post->url() }}' class="text-left lg:px-6 no-underline hover:underline text-white hover:text-skl-pink w-full">
                <h4 class='bg-skl-black-50 w-fit'>{{ $post->title }}</h4>
              @if ($post->subtitle && $post->subtitle !== '') <h5 class='font-skl-nunito pl-4 leading-6 lg:w-11/12 pt-4 text-white bg-skl-black-50 w-fit'>{{ $post->subtitle }}</h5> @endif
            </a>
            <a href="{{ $post->url() }}" class="block p-0 lg:w-1/4 w-full">
                <figure class="overflow-hidden rounded-sm h-28 flex justify-center items-center bg-gray-300 w-full">
                    <picture>
                        <?= $post->image_tag('medium', false, '') ?>
                    </picture>
                </figure>
            </a>
        </div>
        <div class="blog-inner-item p-3 pt-0 flex flex-col lg:pt-0 h-full mb-10">

            @if (config('binshopsblog.show_full_text_at_list'))
                <p><a class="p-0" href="{{ $post->url() }}">{!! $post->post_body_output() !!} </a></p>
            @else
                <a class="p-0 text-skl-white no-underline w-fit bg-skl-black-90 lg:w-3/4" href="{{ $post->url() }}"><p class="">{!! mb_strimwidth($post->post_body_output(), 0, 200, '...') !!}</p></a>
            @endif
            {{-- 
            <div class="post-details-bottom">
                <span class="light-text">Authored by: </span> {{ $post->author->name }} <span class="light-text">Posted
                    at: </span> {{ date('d M Y ', strtotime($post->posted_at)) }}
            </div> --}}
        </div>
    </div>

</div>

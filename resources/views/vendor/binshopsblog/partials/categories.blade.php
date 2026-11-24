<div class=''>
    @foreach($post->categories as $category)
    @php
        $url = 'categories/' . $category->slug;
    @endphp
    @php
    //dd($category);
    @endphp
        <a class='btn btn-outline-secondary btn-sm m-1' href='{{$url}}'>
            {{$category->category_name}}
        </a>
    @endforeach
</div>
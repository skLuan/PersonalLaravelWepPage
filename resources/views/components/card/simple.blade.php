<div class="whatIdoCard my-4">
    <div>
        <h5>{{ $title}}</h5>
        <figure class=" w-fit  flex py-3"><iconify-icon
                class="text-skl-white m-auto p-2 rounded-full border-skl-white border" icon="{{$iconName}}" width="32"
                height="32"></iconify-icon></figure>
        <p class="pl-12 pt-0 pr-0">
            {{$slot}}
        </p>
    </div>
</div>

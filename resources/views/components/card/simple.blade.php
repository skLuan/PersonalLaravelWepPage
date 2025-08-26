<div class="whatIdoCard my-4">
    <div>
        <h5 class="bg-skl-black-50">{{ $title}}</h5>
        <figure class=" w-fit  flex py-3"><iconify-icon
                class="text-skl-white m-auto p-2 rounded-full border-skl-white border" icon="{{$iconName}}" width="32"
                height="32"></iconify-icon></figure>
        <p class="pl-12 md:pl-6 pt-0 pr-0 bg-skl-black-90">
            {{$slot}}
        </p>
    </div>
</div>

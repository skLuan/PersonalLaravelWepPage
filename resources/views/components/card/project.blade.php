<div class="!border-skl-purple border p-2 rounded-lg bg-skl-black-90">
    <figure class="bg-slate-400 h-44 w-full max-h-44 overflow-hidden rounded-md">
        <picture>
            <source media="(min-width: )" srcset=""><img src="{{ $project->image_path }}" alt="{{ $project->title }}">
        </picture>
    </figure>
    <p class="flex flex-row items-center"> <span class="ml-auto">{{ $project->title }}</span> <iconify-icon icon="mingcute:right-fill" width="24" height="24"></iconify-icon></p>
    <div class="w-full flex justify-between items-center"><h5 class="opacity-50 font-skl-titles">Url</h5><a
           target="_blank" href="{{ $project->site_url }}">{{ $project->site_url }}</a></div>
    <div class="flex justify-between py-2 items-center"><h5 class="opacity-50 font-skl-titles">Skills</h5>
        <div class="flex flex-row justify-end w-full">
            @foreach ($project->skills as $skill)
            <span class="pillskl !border-skl-purple border rounded-full px-3 py-1 mx-1">{{$skill->name}}</span>
            @endforeach
        </div>
    </div>
    <p class="border-t !border-t-skl-purple">
        {{ $project->short_description }}
    </p>
</div>

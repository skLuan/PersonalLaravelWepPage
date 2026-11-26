<form role="search" method="GET" action="{{ route('blog.search') }}" class="flex gap-2 items-center my-6">
    <label for="blog-search" class="sr-only">{{ __('blog.search.label') }}</label>
    <input id="blog-search" type="search" name="s" value="{{ $term ?? '' }}" placeholder="{{ __('blog.search.placeholder') }}"
        class="bg-skl-black-90 border border-skl-grey rounded-sm px-3 py-2 text-skl-white w-full max-w-md">
    <button type="submit" class="border border-skl-pink text-skl-pink px-4 py-2 rounded-sm hover:bg-skl-pink hover:text-skl-white transition-all">{{ __('blog.search.submit') }}</button>
</form>

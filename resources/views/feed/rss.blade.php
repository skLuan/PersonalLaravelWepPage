{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}{{-- split: a literal closing tag would end Blade's compiled PHP --}}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ $title }}</title>
        <link>{{ route('binshopsblog.index') }}</link>
        <description>{{ $description }}</description>
        <language>{{ $language }}</language>
        <atom:link href="{{ url()->current() }}" rel="self" type="application/rss+xml" />
        @if ($posts->isNotEmpty())
            <lastBuildDate>{{ $posts->first()->posted_at->toRssString() }}</lastBuildDate>
        @endif
        @foreach ($posts as $post)
            <item>
                <title>{{ $post->title }}</title>
                <link>{{ $post->url() }}</link>
                <guid isPermaLink="true">{{ $post->url() }}</guid>
                <pubDate>{{ $post->posted_at->toRssString() }}</pubDate>
                <description>{{ \Illuminate\Support\Str::limit(strip_tags($post->short_description ?: $post->generate_introduction()), $textLimit) }}</description>
            </item>
        @endforeach
    </channel>
</rss>

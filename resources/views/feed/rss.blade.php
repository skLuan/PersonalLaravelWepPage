{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}{{-- split: a literal closing tag would end Blade's compiled PHP --}}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ $title }}</title>
        <link>{{ route('blog.index') }}</link>
        <description>{{ $description }}</description>
        <language>{{ $language }}</language>
        <atom:link href="{{ url()->current() }}" rel="self" type="application/rss+xml" />
        @if ($posts->isNotEmpty())
            <lastBuildDate>{{ $posts->first()->published_at->toRssString() }}</lastBuildDate>
        @endif
        @foreach ($posts as $post)
            <item>
                <title>{{ $post->title }}</title>
                <link>{{ $post->url() }}</link>
                <guid isPermaLink="true">{{ $post->url() }}</guid>
                <pubDate>{{ $post->published_at->toRssString() }}</pubDate>
                <description>{{ $post->summary(300) }}</description>
            </item>
        @endforeach
    </channel>
</rss>

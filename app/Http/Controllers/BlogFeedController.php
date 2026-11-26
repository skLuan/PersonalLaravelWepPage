<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;

/** RSS 2.0 feed of the current locale's posts. */
class BlogFeedController extends Controller
{
    public const POSTS = 20;

    public function __invoke(): Response
    {
        $posts = Post::query()
            ->locale()
            ->published()
            ->latest('published_at')
            ->limit(self::POSTS)
            ->get();

        return response()
            ->view('feed.rss', [
                'posts' => $posts,
                'title' => __('blog.feed.title'),
                'description' => __('blog.feed.description'),
                'language' => app()->getLocale(),
            ])
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }
}

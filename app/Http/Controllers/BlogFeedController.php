<?php

namespace App\Http\Controllers;

use BinshopsBlog\Models\BinshopsBlogPost;
use Carbon\Carbon;
use Illuminate\Http\Response;

/**
 * RSS 2.0 feed for the blog.
 *
 * Replaces binshops' BinshopsBlogRssFeedController, which needs the abandoned
 * laravelium/feed package.
 */
class BlogFeedController extends Controller
{
    public function __invoke(): Response
    {
        $posts = BinshopsBlogPost::query()
            ->where('is_published', true)
            ->where('posted_at', '<', Carbon::now())
            ->orderByDesc('posted_at')
            ->limit(config('binshopsblog.rssfeed.posts_to_show_in_rss_feed', 10))
            ->get();

        return response()
            ->view('feed.rss', [
                'posts' => $posts,
                'title' => config('app.name').' Blog',
                'description' => config('binshopsblog.rssfeed.description', 'Blog RSS feed'),
                'language' => app()->getLocale(),
                'textLimit' => config('binshopsblog.rssfeed.text_limit', 100),
            ])
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }
}

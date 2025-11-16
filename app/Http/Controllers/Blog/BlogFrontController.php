<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Inertia\Inertia;
use Illuminate\Support\Str;

class BlogFrontController extends Controller
{
    public function index()
    {
        $blogs = Blog::where('status', 'published')
        ->with('category')
            ->latest()
            ->paginate(6)
            ->through(fn ($blog) => [
                'id' => $blog->id,
                'title' => $blog->title,
                'slug' => $blog->slug,
                'excerpt' => $blog->excerpt,
                'image' => Str::startsWith($blog->image, 'http')
                    ? $blog->image
                    : asset('storage/' . $blog->image),
                'category' => $blog->category,
                'created_at' => $blog->created_at->format('M d, Y'),
            ]);

        return Inertia::render('Blog', [
            'blogs' => $blogs,
        ]);
    }

    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)
            ->where('status', 'published')
            ->with('user')
            ->firstOrFail();

        return Inertia::render('BlogDetail', [
            'blog' => [
                'title' => $blog->title,
                'content' => $blog->content,
                'image' => Str::startsWith($blog->image, 'http')
                    ? $blog->image
                    : asset('storage/' . $blog->image),
                'author' => $blog->user->name ?? 'Admin',
                'tags' => $blog->tags ? (is_array($blog->tags) ? $blog->tags : explode(',', $blog->tags)) : [],
                'created_at' => $blog->created_at->format('M d, Y'),
            ],
        ]);
    }
}

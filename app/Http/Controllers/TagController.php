<?php

namespace App\Http\Controllers;

use App\Models\Hashtag;
use Inertia\Inertia;

class TagController extends Controller
{
    /**
     * Exibe os posts publicados de uma hashtag.
     */
    public function show(string $slug)
    {
        $hashtag = Hashtag::where('slug', $slug)->firstOrFail();

        $posts = $hashtag->posts()
            ->publicado()
            ->with('category', 'hashtags')
            ->orderByRaw('created_at DESC')
            ->get();

        return Inertia::render('Tags/Show', [
            'hashtag' => $hashtag,
            'posts' => $posts,
        ]);
    }
}

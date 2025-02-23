<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Spatie\LaravelMarkdown\MarkdownRenderer;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($slug) // Recebe o slug como parâmetro
    {
        // $post = Post::where('slug', $slug)->firstOrFail(); // Busca o post
        $posts = json_decode(Storage::get('data\posts.json'), true);

        foreach ($posts as $value) {
            if ($value['slug'] == $slug) {
                $post = $value;
                //$post['content'] = app(MarkdownRenderer::class)->toHtml($post['content']);
                break;
            }
        }


        return Inertia::render('Post', [ // Renderiza o componente Vue 'Post'
            'post' => $post,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }
}

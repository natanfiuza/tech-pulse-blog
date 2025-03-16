<?php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::all();
        return Inertia::render('Admin/Posts/PostsIndex', ['posts' => $posts]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Posts/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([ // Validação!

            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'required|string',

        ]);

        Post::create($request->all());
        return redirect()->route('posts.index')->with('success', 'Post criado!'); // Mensagens!

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
    public function edit(string $uuid)
    {
        $post = Post::whereRaw(" uuid = '{$uuid}' ")->first();
        return Inertia::render('Admin/Posts/PostsEdit', ['post' => $post]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'except' => 'required|string',
        ]);

        $post->update($request->all());
        return redirect()->route('posts.index')->with('success', 'Post atualizado com sucesso!');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('posts.index')->with('success', 'Post excluído!');

    }
}

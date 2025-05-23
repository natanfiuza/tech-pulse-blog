<?php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::whereRaw("user_id = '" . Auth::user()->id . "' ")
            ->orderByRaw('created_at DESC')
            ->get();
        return Inertia::render('Admin/Posts/PostsIndex', ['posts' => $posts]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Posts/PostsCreate');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $mensagens = [
            'title.required'   => __('Título não foi informado.'),
            'content.required' => __('Conteúdo não foi informado.'),
            'excerpt.required' => __('Resumo não foi informado.'),

        ];

        $request->validate([

            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'required|string',

        ], $mensagens);

        $uuid = Str::uuid()->toString();

        $imagePath = ''; // Inicializa o caminho da imagem como vazio
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            try {
                $image     = $request->file('image');
                $filename  = $uuid;
                $base_path = storage_path('app/public/images');
                if (! file_exists($base_path)) {
                    mkdir($base_path, 0777, true);
                }

                $path = $image->storeAs('public/images', $filename);

                // Define o caminho relativo que será salvo no banco de dados
                $imagePath = '/storage/images/' . $filename;

            } catch (\Exception $e) {
                // Loga o erro e retorna com uma mensagem amigável
                Log::error('Erro no upload da imagem do post: ' . $e->getMessage());
                return back()->withErrors(['image' => 'Ocorreu um erro ao fazer upload da imagem.'])->withInput();
            }
        }

        $post          = new Post();
        $post->user_id = Auth::user()->id;
        $post->uuid    = $uuid;
        $post->title   = $request->title;
        $post->image   = $imagePath;
        $post->content = base64_decode($request->content);
        $post->excerpt = $request->excerpt;
        $post->save();

        return redirect()->route('posts.index')->with('success', 'Post criado com sucesso!'); // A MELHOR OPÇÃO

    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request) // Recebe o slug como parâmetro
    {
        if (isset($request->slug)) {
            $post = Post::where('slug', $request->slug)->firstOrFail(); // Busca o post
        }
        if (isset($request->uuid)) {
            $post = Post::where('uuid', $request->uuid)->firstOrFail(); // Busca o post
        }

        return Inertia::render('PostMermaid', [ // Renderiza o componente Vue 'Post'
            'post' => $post,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $uuid)
    {
        $post = Post::whereRaw(" uuid = '$uuid' ")->first();
        if (! $post) {
            return abort(404);
        }

        return Inertia::render('Admin/Posts/PostsEdit', ['post' => $post]);

    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request)
    public function update(Request $request)
    {
        $mensagens = [
            'title.required'   => __('Título não foi informado.'),
            'content.required' => __('Conteúdo não foi informado.'),
            'excerpt.required' => __('Resumo não foi informado.'),

        ];

        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
        ], $mensagens);

        $post = Post::whereRaw(" uuid = '{$request->uuid}' ")->first();

        if (! $post) {
            return abort(404);
        }

        $post->title   = $request->title;
        $post->content = base64_decode($request->content);
        $post->excerpt = $request->excerpt;
        $post->update();

        $imagePath = ''; // Inicializa o caminho da imagem como vazio
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            try {
                $image     = $request->file('image');
                $filename  = $post->uuid;
                $base_path = storage_path('app/public/images');
                if (! file_exists($base_path)) {
                    mkdir($base_path, 0777, true);
                }

                $image->storeAs('public/images', $filename);

            } catch (\Exception $e) {
                // Loga o erro e retorna com uma mensagem amigável
                Log::error('PostUpdate: Erro no upload da imagem do post: ' . $e->getMessage());
                return back()->withErrors(['image' => 'Ocorreu um erro ao fazer upload da imagem.'])->withInput();
            }
        }

        return redirect()->route('posts.index')->with('success', 'Post atualizado com sucesso!'); // A MELHOR OPÇÃO

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $post = Post::whereRaw("uuid = '{$request->uuid}' and user_id='" . Auth::user()->id . "'");
        $post->delete();
        return redirect()->route('posts.index')->with('success', 'Post excluído!');

    }
    public function list_rss(Request $request)
    {
        $per_page = $request->input('per_page', 5);
        $posts = Post::orderByRaw('created_at DESC')
        ->take($per_page)
            ->get();

        return $posts->map(function ($post) {
           return [
                "id" => $post->uuid,
                "date_gmt" => $post->created_at, // Usado para 'publishedDate'
                "title" => [
                    "rendered" => $post->title // Usado para 'title'
                ],
                "content" => [
                    "rendered" => $post->content // Usado para 'content'
                ],
                "excerpt" => [
                    "rendered" => $post->excerpt
                ],
                "jetpack_featured_media_url" => "https://tech-pulse.natanfiuza.dev.br/post/image/".$post->uuid,
                "_embedded" => [
                    "wp:featuredmedia" => [

                            "source_url" => "https://tech-pulse.natanfiuza.dev.br/post/image/".$post->uuid // Usado para 'imageUrl'

                    ]
                ],
            ];
        });
    }

}

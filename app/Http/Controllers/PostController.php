<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Hashtag;
use App\Models\Post;
use App\Models\PostView;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Post::query();

        // Autor vê apenas os próprios posts; admin vê todos
        if (Auth::user()->possui_papel(User::ROLE_AUTOR)) {
            $query->where('user_id', Auth::user()->id);
        }

        $posts = $query->orderByRaw('created_at DESC')
            ->with('category', 'hashtags', 'user')
            ->get();

        return Inertia::render('Admin/Posts/PostsIndex', ['posts' => $posts]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Posts/PostsCreate', [
            'categorias' => $this->categorias_para_select(),
            'hashtags_existentes' => Hashtag::orderBy('name')->get(['id', 'name', 'slug']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $mensagens = [
            'title.required' => __('Título não foi informado.'),
            'content.required' => __('Conteúdo não foi informado.'),
            'excerpt.required' => __('Resumo não foi informado.'),
            'category_id.exists' => __('Categoria inválida.'),
            'status.in' => __('Status inválido.'),
            'published_at.date' => __('Data de publicação inválida.'),
            'image.mimes' => __('A imagem deve ser PNG, JPG ou WebP.'),
            'image.max' => __('A imagem deve ter no máximo 5MB.'),

        ];

        $request->validate([

            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'required|string',
            'category_id' => 'nullable|integer|exists:categories,id',
            'status' => 'nullable|in:rascunho,publicado,agendado',
            'published_at' => 'nullable|date',
            'hashtags' => 'nullable|array',
            'image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:5120',

        ], $mensagens);

        $uuid = Str::uuid()->toString();

        $imagePath = ''; // Inicializa o caminho da imagem como vazio
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            try {
                $image = $request->file('image');
                $filename = $uuid;
                $base_path = storage_path('app/public/images');
                if (! file_exists($base_path)) {
                    mkdir($base_path, 0777, true);
                }

                $image->storeAs('public/images', $filename);

                // Define o caminho relativo que será salvo no banco de dados
                $imagePath = '/storage/images/'.$filename;

            } catch (\Exception $e) {
                // Loga o erro e retorna com uma mensagem amigável
                Log::error('Erro no upload da imagem do post: '.$e->getMessage());

                return back()->withErrors(['image' => 'Ocorreu um erro ao fazer upload da imagem.'])->withInput();
            }
        }

        $status = $this->normalizar_status($request->input('status', 'publicado'), $request->input('published_at'));
        $published_at = $request->input('published_at') ? \Carbon\Carbon::parse($request->input('published_at')) : null;

        $post = new Post;
        $post->user_id = Auth::user()->id;
        $post->uuid = $uuid;
        $post->title = $request->title;
        $post->image = $imagePath;
        $post->content = base64_decode($request->content);
        $post->excerpt = $request->excerpt;
        $post->category_id = $request->input('category_id');
        $post->status = $status;
        $post->published_at = $published_at;
        $post->save();

        $this->sincronizar_hashtags($post, $request->input('hashtags', []));

        return redirect()->route('posts.index')->with('success', 'Post criado com sucesso!'); // A MELHOR OPÇÃO

    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request) // Recebe o slug como parâmetro
    {
        $query = Post::publicado()
            ->with(['category', 'hashtags', 'comments' => function ($query_comments) {
                $query_comments->with([
                    'user',
                    'children' => function ($query_children) {
                        $query_children->with('user')
                            ->withCount('votes')
                            ->withExists(['votes as has_upvoted' => function ($query_votes) {
                                $query_votes->where('user_id', Auth::id());
                            }]);
                    },
                ])
                    ->withCount('votes')
                    ->withExists(['votes as has_upvoted' => function ($query_votes) {
                        $query_votes->where('user_id', Auth::id());
                    }]);
            }]);

        if (isset($request->slug)) {
            $post = $query->where('slug', $request->slug)->firstOrFail(); // Busca o post
        }
        if (isset($request->uuid)) {
            $post = $query->where('uuid', $request->uuid)->firstOrFail(); // Busca o post
        }

        // Registra a visualização do usuário logado (histórico do dashboard do leitor)
        if (Auth::check()) {
            PostView::updateOrCreate(
                ['user_id' => Auth::id(), 'post_id' => $post->id],
                ['viewed_at' => now()]
            );
        }

        return Inertia::render('Post', [ // Renderiza o componente Vue 'Post' (pipeline markdown-it + mermaid unificado)
            'post' => $post,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $uuid)
    {
        $post = Post::where('uuid', $uuid)->with('category', 'hashtags')->first();
        if (! $post) {
            return abort(404);
        }

        $this->autorizar_post($post);

        return Inertia::render('Admin/Posts/PostsEdit', [
            'post' => $post,
            'categorias' => $this->categorias_para_select(),
            'hashtags_existentes' => Hashtag::orderBy('name')->get(['id', 'name', 'slug']),
        ]);

    }

    /**
     * Árvore de categorias (raízes com filhos até 2 níveis) para o select do form.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Category>
     */
    private function categorias_para_select()
    {
        return Category::whereNull('parent_id')
            ->with('children.children')
            ->orderBy('name')
            ->get();
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request)
    public function update(Request $request)
    {
        $mensagens = [
            'title.required' => __('Título não foi informado.'),
            'content.required' => __('Conteúdo não foi informado.'),
            'excerpt.required' => __('Resumo não foi informado.'),
            'category_id.exists' => __('Categoria inválida.'),
            'status.in' => __('Status inválido.'),
            'published_at.date' => __('Data de publicação inválida.'),
            'image.mimes' => __('A imagem deve ser PNG, JPG ou WebP.'),
            'image.max' => __('A imagem deve ter no máximo 5MB.'),

        ];

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'category_id' => 'nullable|integer|exists:categories,id',
            'status' => 'nullable|in:rascunho,publicado,agendado',
            'published_at' => 'nullable|date',
            'hashtags' => 'nullable|array',
            'image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:5120',
        ], $mensagens);

        $post = Post::where('uuid', $request->uuid)->first();

        if (! $post) {
            return abort(404);
        }

        $this->autorizar_post($post);

        $status = $this->normalizar_status($request->input('status', 'publicado'), $request->input('published_at'));
        $published_at = $request->input('published_at') ? \Carbon\Carbon::parse($request->input('published_at')) : null;

        $post->title = $request->title;
        $post->content = base64_decode($request->content);
        $post->excerpt = $request->excerpt;
        $post->category_id = $request->input('category_id');
        $post->status = $status;
        $post->published_at = $published_at;
        $post->update();

        $this->sincronizar_hashtags($post, $request->input('hashtags', []));

        $imagePath = ''; // Inicializa o caminho da imagem como vazio
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            try {
                $image = $request->file('image');
                $filename = $post->uuid;
                $base_path = storage_path('app/public/images');
                if (! file_exists($base_path)) {
                    mkdir($base_path, 0777, true);
                }

                $image->storeAs('public/images', $filename);

                // Define o caminho relativo que será salvo no banco de dados
                $imagePath = '/storage/images/'.$filename;
                $post->image = $imagePath;
                $post->save();

            } catch (\Exception $e) {
                // Loga o erro e retorna com uma mensagem amigável
                Log::error('PostUpdate: Erro no upload da imagem do post: '.$e->getMessage());

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
        $post = Post::where('uuid', $request->uuid)->first();

        if (! $post) {
            return abort(404);
        }

        $this->autorizar_post($post);

        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post excluído!');

    }

    /**
     * Garante que o usuário pode gerenciar o post (dono ou admin).
     */
    private function autorizar_post(Post $post): void
    {
        if (! Auth::user()->possui_papel(User::ROLE_ADMIN) && $post->user_id !== Auth::user()->id) {
            abort(403);
        }
    }

    /**
     * Normaliza o status do post conforme a data de publicação informada.
     *
     * Publicado com data futura vira agendado; agendado sem data vira publicado.
     *
     * @param  string|null  $status
     * @param  string|null  $published_at
     */
    private function normalizar_status($status, $published_at): string
    {
        $status = $status ?: 'publicado';

        if ($status === 'publicado' && $published_at && \Carbon\Carbon::parse($published_at)->isFuture()) {
            return 'agendado';
        }

        if ($status === 'agendado' && ! $published_at) {
            return 'publicado';
        }

        return $status;
    }

    /**
     * Cria (se preciso) e vincula as hashtags do post (array de nomes).
     */
    private function sincronizar_hashtags(Post $post, array $hashtags): void
    {
        $ids = collect($hashtags)
            ->filter(fn ($nome) => is_string($nome) && trim($nome) !== '')
            ->map(function ($nome) {
                $slug = criar_slug($nome);

                return Hashtag::firstOrCreate(['slug' => $slug], ['name' => trim($nome)])->id;
            })
            ->all();

        $post->hashtags()->sync($ids);
    }

    /**
     * Lista RSS com os posts
     *
     *
     * @return JSON
     */
    public function list_rss(Request $request)
    {
        $per_page = $request->input('per_page', 5);
        $posts = Post::publicado()
            ->with('category', 'hashtags')
            ->orderByRaw('created_at DESC')
            ->take($per_page)
            ->get();

        return $posts->map(function ($post) {
            return [
                'id' => $post->uuid,
                'date_gmt' => $post->created_at, // Usado para 'publishedDate'
                'title' => [
                    'rendered' => $post->title, // Usado para 'title'
                ],
                'content' => [
                    'rendered' => $post->content, // Usado para 'content'
                ],
                'excerpt' => [
                    'rendered' => $post->excerpt,
                ],
                'image_front_url' => 'https://tech-pulse.natanfiuza.dev.br/post/image/'.$post->uuid,
                'web_url' => 'https://tech-pulse.natanfiuza.dev.br/post/show/'.$post->slug,
                'web_fix_url' => 'https://tech-pulse.natanfiuza.dev.br/post/show/fix/'.$post->uuid,
                'slug' => $post->slug,
                'uuid' => $post->uuid,
                'user_id' => $post->user_id,
                'category' => $post->category ? ['slug' => $post->category->slug, 'name' => $post->category->name] : null,
                'hashtags' => $post->hashtags->map(fn ($hashtag) => ['slug' => $hashtag->slug, 'name' => $hashtag->name])->values(),
                'published_at' => $post->published_at,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
            ];
        });
    }

    /**
     * Lista todos os posts de forma paginada.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function list_published_posts(Request $request)
    {
        $page = $request->input('page', 1);
        $per_page = $request->input('per_page', 15); // Um valor padrão, o Flutter pode requisitar outro

        $paginated_posts = Post::publicado()
            ->with('category', 'hashtags')
            ->orderBy('created_at', 'desc')
            ->paginate($per_page, ['*'], 'page', $page);

        // Transforma a coleção de posts dentro do objeto paginador
        $transformed_posts = $paginated_posts->getCollection()->map(function ($post) {
            return [
                'id' => $post->uuid,
                'date_gmt' => $post->created_at,
                'title' => [
                    'rendered' => $post->title,
                ],
                'content' => [
                    'rendered' => $post->content,
                ],
                'excerpt' => [
                    'rendered' => $post->excerpt,
                ],
                'image_front_url' => 'https://tech-pulse.natanfiuza.dev.br/post/image/'.$post->uuid,
                'web_url' => 'https://tech-pulse.natanfiuza.dev.br/post/show/'.$post->slug,
                'web_fix_url' => 'https://tech-pulse.natanfiuza.dev.br/post/show/fix/'.$post->uuid,
                'slug' => $post->slug,
                'uuid' => $post->uuid,
                'user_id' => $post->user_id,
                'category' => $post->category ? ['slug' => $post->category->slug, 'name' => $post->category->name] : null,
                'hashtags' => $post->hashtags->map(fn ($hashtag) => ['slug' => $hashtag->slug, 'name' => $hashtag->name])->values(),
                'published_at' => $post->published_at,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
            ];
        });

        $paginated_posts->setCollection($transformed_posts);

        return response()->json($paginated_posts);
    }
}

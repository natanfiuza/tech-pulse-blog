<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categoria_slug = $request->query('categoria');
        $categoria_ativa = null;

        $query = Post::publicado()
            ->with('category', 'hashtags', 'user')
            ->orderByRaw('created_at DESC');

        if ($categoria_slug) {
            $categoria = Category::where('slug', $categoria_slug)->first();
            if ($categoria) {
                $categoria_ativa = $categoria->slug;
                // Posts da categoria + todas as subcategorias (árvore recursiva)
                $query->whereIn('category_id', $this->coletar_ids_descendentes($categoria));
            }
        }

        $posts = $query->get();

        // Destaque automático: o post mais recente
        $featured_post = true;
        foreach ($posts as &$value) {
            $value->featured_post = $featured_post;
            $featured_post = false;
        }

        // Categorias raiz (com filhos) para os chips de filtro
        $categorias = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();

        return Inertia::render('Home', [
            'posts' => $posts,
            'categorias' => $categorias,
            'categoria_ativa' => $categoria_ativa,
        ]);
    }

    /**
     * Coleta os ids da categoria e de todos os descendentes (BFS sem N+1).
     *
     * @return array<int>
     */
    private function coletar_ids_descendentes(Category $categoria): array
    {
        $todos = Category::select('id', 'parent_id')->get();
        $mapa = $todos->groupBy('parent_id');

        $ids = [$categoria->id];
        $pilha = [$categoria->id];

        while (! empty($pilha)) {
            $pai = array_pop($pilha);
            foreach ($mapa->get($pai, collect()) as $filha) {
                $ids[] = $filha->id;
                $pilha[] = $filha->id;
            }
        }

        return $ids;
    }
}

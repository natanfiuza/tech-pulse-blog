<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostView;
use App\Models\SolicitacaoAutor;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    /**
     * Dashboard administrativo: estatísticas de posts, categorias e histórico de visualizações.
     */
    public function index(): Response
    {
        /** @var User $user */
        $user = Auth::user();
        $is_admin = $user->possui_papel(User::ROLE_ADMIN);

        $posts_query = Post::query();
        if (! $is_admin) {
            $posts_query->where('user_id', $user->id);
        }

        $posts_publicados = (clone $posts_query)
            ->where('status', 'publicado')
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->count();

        $rascunhos = (clone $posts_query)
            ->where('status', 'rascunho')
            ->count();

        $agendados = (clone $posts_query)
            ->where(function ($q) {
                $q->where('status', 'agendado')
                    ->orWhere(function ($q2) {
                        $q2->where('status', 'publicado')
                            ->whereNotNull('published_at')
                            ->where('published_at', '>', now());
                    });
            })
            ->count();

        $categorias_count = $is_admin ? Category::count() : 0;

        // Visualizações dos posts (globais para admin, ou apenas dos posts do autor)
        $views_query = PostView::query()
            ->join('posts', 'post_views.post_id', '=', 'posts.id')
            ->where('post_views.viewed_at', '>=', now()->subDays(29)->startOfDay());

        if (! $is_admin) {
            $views_query->where('posts.user_id', $user->id);
        }

        $views = $views_query->select(['post_views.viewed_at'])->get();

        $total_views_query = PostView::query()
            ->join('posts', 'post_views.post_id', '=', 'posts.id');
        if (! $is_admin) {
            $total_views_query->where('posts.user_id', $user->id);
        }
        $total_visualizacoes = $total_views_query->count();

        // Agrupamento dos últimos 30 dias para o gráfico
        $dias = [];
        $hoje = now()->startOfDay();
        for ($i = 29; $i >= 0; $i--) {
            $data = (clone $hoje)->subDays($i);
            $chave = $data->format('Y-m-d');
            $dias[$chave] = [
                'data' => $chave,
                'label' => $data->format('d/m'),
                'total' => 0,
            ];
        }

        foreach ($views as $view) {
            if ($view->viewed_at) {
                $chave = $view->viewed_at->format('Y-m-d');
                if (isset($dias[$chave])) {
                    $dias[$chave]['total']++;
                }
            }
        }

        $posts_mais_vistos = (clone $posts_query)
            ->withCount('views')
            ->orderByDesc('views_count')
            ->take(5)
            ->get(['id', 'uuid', 'title', 'slug', 'status']);

        return Inertia::render('Admin/AdminHome', [
            'metricas' => [
                'posts_publicados'       => $posts_publicados,
                'rascunhos'              => $rascunhos,
                'agendados'              => $agendados,
                'categorias'             => $categorias_count,
                'total_visualizacoes'    => $total_visualizacoes,
                'is_admin'               => $is_admin,
                'solicitacoes_pendentes' => $is_admin ? SolicitacaoAutor::pendente()->count() : 0,
            ],
            'grafico_visualizacoes' => array_values($dias),
            'posts_mais_vistos'     => $posts_mais_vistos,
        ]);
    }
}

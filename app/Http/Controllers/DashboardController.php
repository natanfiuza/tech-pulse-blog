<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\PostView;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Dashboard pessoal do leitor: histórico de visualizações, comentários e perfil.
     */
    public function index(): Response|RedirectResponse
    {
        $user = Auth::user();

        // Autores e admins usam o painel admin
        if ($user->possui_papel(User::ROLE_AUTOR, User::ROLE_ADMIN)) {
            return redirect()->route('admin.home');
        }

        $visualizacoes = PostView::where('user_id', $user->id)
            ->with('post:id,uuid,title,slug')
            ->orderByDesc('viewed_at')
            ->take(20)
            ->get();

        $comentarios = Comment::where('user_id', $user->id)
            ->with('post:id,uuid,title,slug')
            ->orderByDesc('created_at')
            ->take(20)
            ->get();

        return Inertia::render('Reader/Dashboard', [
            'visualizacoes' => $visualizacoes,
            'comentarios' => $comentarios,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\SolicitacaoAutor;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SolicitacaoAutorController extends Controller
{
    /**
     * Exibe a página "Torne-se um Autor" com o estado adequado para o visitante.
     * Acessível publicamente; o estado do usuário é passado ao componente Vue.
     */
    public function show(): Response
    {
        /** @var User|null $user */
        $user = Auth::user();

        $solicitacao = null;

        if ($user && $user->possui_papel(User::ROLE_LEITOR)) {
            $solicitacao = SolicitacaoAutor::where('user_id', $user->id)
                ->latest()
                ->first();
        }

        return Inertia::render('Auth/TornarSeAutor', [
            'solicitacao' => $solicitacao ? [
                'status'     => $solicitacao->status,
                'motivacao'  => $solicitacao->motivacao,
                'admin_nota' => $solicitacao->admin_nota,
                'created_at' => $solicitacao->created_at?->format('d/m/Y'),
            ] : null,
        ]);
    }

    /**
     * Persiste uma nova solicitação de perfil de autor.
     * Requer usuário autenticado com papel 'leitor' sem solicitação pendente.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->possui_papel(User::ROLE_LEITOR)) {
            return back()->with('error', 'Somente leitores podem solicitar o perfil de autor.');
        }

        $pendente_existente = SolicitacaoAutor::where('user_id', $user->id)
            ->where('status', SolicitacaoAutor::STATUS_PENDENTE)
            ->exists();

        if ($pendente_existente) {
            return back()->with('error', 'Você já possui uma solicitação em análise.');
        }

        $request->validate([
            'motivacao' => 'required|string|min:50|max:2000',
        ], [
            'motivacao.required' => 'Por favor, descreva sua motivação.',
            'motivacao.min'      => 'A motivação deve ter pelo menos 50 caracteres.',
            'motivacao.max'      => 'A motivação deve ter no máximo 2000 caracteres.',
        ]);

        SolicitacaoAutor::create([
            'user_id'   => $user->id,
            'motivacao' => $request->motivacao,
            'status'    => SolicitacaoAutor::STATUS_PENDENTE,
        ]);

        return redirect()->route('tornar-se-autor.show')
            ->with('success', 'Solicitação enviada com sucesso! Em breve nossa equipe entrará em contato.');
    }

    /**
     * Lista todas as solicitações para revisão administrativa.
     * Somente admins têm acesso (via middleware na rota).
     */
    public function index(): Response
    {
        $solicitacoes = SolicitacaoAutor::with(['user:id,name,email,avatar,uuid', 'admin:id,name'])
            ->latest()
            ->get()
            ->map(fn ($s) => [
                'id'         => $s->id,
                'status'     => $s->status,
                'motivacao'  => $s->motivacao,
                'admin_nota' => $s->admin_nota,
                'created_at' => $s->created_at?->format('d/m/Y H:i'),
                'user'       => $s->user ? [
                    'id'         => $s->user->id,
                    'name'       => $s->user->name,
                    'email'      => $s->user->email,
                    'avatar_url' => $s->user->avatar_url,
                ] : null,
                'admin'      => $s->admin ? ['name' => $s->admin->name] : null,
            ]);

        return Inertia::render('Admin/SolicitacoesAutor', [
            'solicitacoes' => $solicitacoes,
        ]);
    }

    /**
     * Aprova ou rejeita uma solicitação de autor.
     * Ao aprovar, eleva o papel do usuário para 'autor'.
     */
    public function update(Request $request, SolicitacaoAutor $solicitacao): RedirectResponse
    {
        $request->validate([
            'acao'       => 'required|in:aprovar,rejeitar',
            'admin_nota' => 'nullable|string|max:1000',
        ]);

        /** @var User $admin */
        $admin = Auth::user();

        if ($request->acao === 'aprovar') {
            $solicitacao->update([
                'status'     => SolicitacaoAutor::STATUS_APROVADA,
                'admin_id'   => $admin->id,
                'admin_nota' => $request->admin_nota,
            ]);

            // Eleva o papel do usuário
            $solicitacao->user->update(['role' => User::ROLE_AUTOR]);

            return back()->with('success', "Solicitação de {$solicitacao->user->name} aprovada. Usuário promovido a autor.");
        }

        // Rejeitar
        $solicitacao->update([
            'status'     => SolicitacaoAutor::STATUS_REJEITADA,
            'admin_id'   => $admin->id,
            'admin_nota' => $request->admin_nota,
        ]);

        return back()->with('success', "Solicitação de {$solicitacao->user->name} rejeitada.");
    }
}


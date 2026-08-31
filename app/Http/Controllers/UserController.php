<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Lista os usuários para gestão de privilégios (somente admin).
     */
    public function index(): Response
    {
        $usuarios = User::withCount('posts')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role', 'avatar']);

        return Inertia::render('Admin/Users', ['usuarios' => $usuarios]);
    }

    /**
     * Atualiza o papel (perfil) de um usuário.
     */
    public function update_role(Request $request, User $user): RedirectResponse
    {
        $this->autorizar_gestao($user);

        $request->validate([
            'role' => 'required|in:'.implode(',', User::ROLES),
        ]);

        $user->update(['role' => $request->role]);

        return back()->with('success', "Perfil de {$user->name} atualizado para {$request->role}.");
    }

    /**
     * Remove um usuário com soft delete (dados e conteúdo permanecem).
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->autorizar_gestao($user);

        $user->delete();

        return back()->with('success', "Usuário {$user->name} removido.");
    }

    /**
     * Regras de gestão: ninguém altera/exclui a si mesmo e admin não
     * rebaixa nem exclui outro admin (evita lockout do blog).
     */
    private function autorizar_gestao(User $user): void
    {
        if ($user->id === Auth::user()->id || $user->possui_papel(User::ROLE_ADMIN)) {
            abort(403);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\UserProfile;
use Inertia\Inertia;
use Inertia\Response;

class PublicProfileController extends Controller
{
    /**
     * Exibe a página de perfil público do autor com seus dados autorizados e posts paginados.
     */
    public function show(string $username): Response
    {
        $username = strtolower($username);

        // Bloqueia palavras reservadas do sistema
        if (in_array($username, UserProfileController::PALAVRAS_RESERVADAS, true)) {
            abort(404);
        }

        $profile = UserProfile::where('username', $username)
            ->where('public_profile_enabled', true)
            ->with('user')
            ->firstOrFail();

        $user = $profile->user;

        // Se o usuário foi removido (soft delete), bloqueia perfil público
        if ($user->trashed()) {
            abort(404);
        }

        $posts = Post::publicado()
            ->where('user_id', $user->id)
            ->with('category', 'hashtags', 'user')
            ->orderByRaw('created_at DESC')
            ->paginate(10);

        // Filtra links sociais que o usuário autorizou exibir
        $links_sociais_autorizados = [];
        foreach ($profile->social_links ?? [] as $rede => $config) {
            if (! empty($config['url']) && (! isset($config['show']) || $config['show'])) {
                $links_sociais_autorizados[$rede] = [
                    'url' => $config['url'],
                ];
            }
        }

        $autor = [
            'username' => $profile->username,
            'name' => $profile->show_name ? $user->name : $profile->username,
            'email' => $profile->show_email ? $user->email : null,
            'bio' => $profile->bio,
            'avatar_url' => $user->avatar_url,
            'social_links' => $links_sociais_autorizados,
        ];

        return Inertia::render('Public/Profile', [
            'author' => $autor,
            'posts' => $posts,
        ]);
    }
}

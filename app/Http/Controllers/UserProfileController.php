<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use App\Services\AvatarService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UserProfileController extends Controller
{
    public const PALAVRAS_RESERVADAS = [
        'admin',
        'administrator',
        'login',
        'register',
        'logout',
        'minha-conta',
        'post',
        'posts',
        'tags',
        'categories',
        'api',
        'assets',
        'dashboard',
        'perfil',
        'profile',
        'user',
        'users',
        'home',
        'techpulse',
        'feed',
        'rss',
        'storage',
        'build',
        'css',
        'js',
        'images',
        'favicon',
    ];

    public function __construct(private AvatarService $avatar_service) {}

    /**
     * Exibe o formulário de edição de perfil para autores e admins no painel admin.
     */
    public function edit_admin(Request $request): Response
    {
        $user = $request->user();
        $profile = $user->obter_ou_criar_perfil();

        return Inertia::render('Admin/Profile/Edit', [
            'profile_user' => $this->formatar_dados_usuario($user, $profile),
        ]);
    }

    /**
     * Exibe o formulário de edição de perfil para leitores na área minha-conta.
     */
    public function edit_reader(Request $request): Response
    {
        $user = $request->user();
        $profile = $user->obter_ou_criar_perfil();

        return Inertia::render('Reader/Profile', [
            'profile_user' => $this->formatar_dados_usuario($user, $profile),
        ]);
    }

    /**
     * Atualiza os dados de perfil do usuário.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->obter_ou_criar_perfil();

        $dados = $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[a-zA-Z0-9_\-\.]+$/',
                Rule::unique(UserProfile::class, 'username')->ignore($profile->id),
                Rule::notIn(self::PALAVRAS_RESERVADAS),
            ],
            'bio' => 'nullable|string|max:1000',
            'social_links' => 'nullable|array',
            'social_links.*.url' => 'nullable|string|max:255',
            'social_links.*.show' => 'nullable|boolean',
            'public_profile_enabled' => 'nullable|boolean',
            'show_email' => 'nullable|boolean',
            'show_name' => 'nullable|boolean',
            'show_author_box' => 'nullable|boolean',
            'avatar' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:5120',
            'reset_avatar' => 'nullable|boolean',
        ], [
            'name.required' => __('O nome é obrigatório.'),
            'username.required' => __('O nome de usuário é obrigatório.'),
            'username.unique' => __('Este nome de usuário já está em uso.'),
            'username.regex' => __('O nome de usuário deve conter apenas letras, números, hífens e underscores.'),
            'username.not_in' => __('Este nome de usuário é reservado pelo sistema.'),
            'avatar.image' => __('O arquivo de avatar deve ser uma imagem válida.'),
            'avatar.mimes' => __('O avatar deve ser PNG, JPG ou WebP.'),
            'avatar.max' => __('O avatar deve ter no máximo 5MB.'),
        ]);

        // Atualiza o nome do usuário
        $user->name = $dados['name'];
        $user->save();

        // Tratamento de avatar
        if ($request->boolean('reset_avatar')) {
            $this->avatar_service->gerar_e_salvar_avatar_padrao($user);
            $user->avatar = null;
            $user->save();
        } elseif ($request->hasFile('avatar')) {
            $this->avatar_service->salvar_upload($user, $request->file('avatar'));
            $user->avatar = $user->uuid;
            $user->save();
        }

        // Atualiza perfil
        $profile->update([
            'username' => strtolower($dados['username']),
            'bio' => $dados['bio'] ?? null,
            'social_links' => $dados['social_links'] ?? [],
            'public_profile_enabled' => $request->boolean('public_profile_enabled', true),
            'show_email' => $request->boolean('show_email', false),
            'show_name' => $request->boolean('show_name', true),
            'show_author_box' => $request->boolean('show_author_box', true),
        ]);

        return back()->with('success', __('Perfil atualizado com sucesso!'));
    }

    /**
     * Formata os dados do usuário e perfil para o frontend.
     */
    private function formatar_dados_usuario($user, $profile): array
    {
        return [
            'id' => $user->id,
            'uuid' => $user->uuid,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'avatar_url' => $user->avatar_url,
            'username' => $profile->username,
            'bio' => $profile->bio ?? '',
            'social_links' => $profile->social_links ?? [
                'github' => ['url' => '', 'show' => true],
                'twitter_x' => ['url' => '', 'show' => true],
                'linkedin' => ['url' => '', 'show' => true],
                'website' => ['url' => '', 'show' => true],
            ],
            'public_profile_enabled' => (bool) $profile->public_profile_enabled,
            'show_email' => (bool) $profile->show_email,
            'show_name' => (bool) $profile->show_name,
            'show_author_box' => (bool) $profile->show_author_box,
            'public_url' => url('/'.$profile->username),
        ];
    }
}


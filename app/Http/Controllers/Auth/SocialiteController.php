<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redireciona o usuário para a página de autenticação do Google.
     */
    public function redirect_to_google()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtém as informações do usuário do Google e faz o login.
     */
    public function handle_google_callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $avatar_url = (string) $googleUser->getAvatar();
            $ja_existia = User::where('google_id', $googleUser->getId())->exists();

            // Encontra ou cria o usuário no banco de dados
            $user = User::updateOrCreate(
                ['google_id' => $googleUser->getId()], // Condição para encontrar
                [                                      // Dados para atualizar ou criar
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'avatar' => $avatar_url,
                    'password' => null, // Ou Hash::make(Str::random(24)) se precisar de senha
                ]
            );

            // Garante perfil e persiste avatar na pasta de imagens do perfil
            $user->obter_ou_criar_perfil();
            $avatar_service = app(\App\Services\AvatarService::class);
            if (! $ja_existia || ! $avatar_service->existe($user->uuid)) {
                if (! empty($avatar_url)) {
                    $avatar_service->salvar_avatar_google($user, $avatar_url);
                } else {
                    $avatar_service->gerar_e_salvar_avatar_padrao($user);
                }
            }

            // Faz o login do usuário
            Auth::login($user, true); // O 'true' ativa o "lembrar-me"

            // Redireciona conforme o perfil: leitor → dashboard pessoal; autor/admin → admin
            return redirect()->intended(caminho_inicial_do_usuario($user));

        } catch (\Exception $e) {
            // Em caso de erro, redireciona de volta para o login com uma mensagem de erro
            Log::error('Erro no Login com Google: '.$e->getMessage()); // Loga o erro

            return redirect('/login')->with('error', 'Falha ao autenticar com o Google. Tente novamente.');
        }
    }
}

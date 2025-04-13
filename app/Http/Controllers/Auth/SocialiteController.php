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

            // Encontra ou cria o usuário no banco de dados
            $user = User::updateOrCreate(
                ['google_id' => $googleUser->getId()], // Condição para encontrar
                [                                      // Dados para atualizar ou criar
                    'name'     => $googleUser->getName(),
                    'email'    => $googleUser->getEmail(),
                    'avatar'   => $googleUser->getAvatar(),
                    'password' => null, // Ou Hash::make(Str::random(24)) se precisar de senha
                ]
            );

                                      // Faz o login do usuário
            Auth::login($user, true); // O 'true' ativa o "lembrar-me"

                                                         // Redireciona para o dashboard ou página inicial
            return redirect()->intended('/admin/home'); // Ou use route('dashboard')

        } catch (\Exception $e) {
                                                                          // Em caso de erro, redireciona de volta para o login com uma mensagem de erro
            Log::error('Erro no Login com Google: ' . $e->getMessage()); // Loga o erro
            return redirect('/login')->with('error', 'Falha ao autenticar com o Google. Tente novamente.');
        }
    }
}

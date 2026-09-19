<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Services\AvatarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_perfil_publico_exibe_dados_e_posts_do_autor(): void
    {
        Storage::fake(AvatarService::DISCO);

        $autor = User::factory()->create([
            'name' => 'Nataniel Fiuza',
            'email' => 'natan@techpulse.dev.br',
            'role' => User::ROLE_AUTOR,
        ]);
        $autor->refresh();

        $categoria = Category::create([
            'name' => 'Tecnologia',
            'slug' => 'tecnologia',
        ]);

        // 3 posts publicados pelo autor
        for ($i = 1; $i <= 3; $i++) {
            Post::create([
                'user_id' => $autor->id,
                'category_id' => $categoria->id,
                'title' => "Post Publicado {$i}",
                'content' => "Conteúdo do post {$i}",
                'excerpt' => "Resumo do post {$i}",
                'image' => "capa_{$i}.jpg",
                'status' => 'publicado',
            ]);
        }

        // 1 rascunho (não deve aparecer)
        Post::create([
            'user_id' => $autor->id,
            'category_id' => $categoria->id,
            'title' => 'Post Rascunho',
            'content' => 'Conteúdo do rascunho',
            'excerpt' => 'Resumo do rascunho',
            'image' => 'capa_rascunho.jpg',
            'status' => 'rascunho',
        ]);

        $response = $this->get('/'.$autor->profile->username);

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Public/Profile')
            ->has('author')
            ->where('author.username', $autor->profile->username)
            ->where('author.name', 'Nataniel Fiuza')
            ->has('posts.data', 3)
        );
    }

    public function test_perfil_publico_desabilitado_retorna_404(): void
    {
        $autor = User::factory()->create([
            'role' => User::ROLE_AUTOR,
        ]);
        $autor->refresh();

        $autor->profile->update(['public_profile_enabled' => false]);

        $response = $this->get('/'.$autor->profile->username);

        $response->assertNotFound();
    }

    public function test_palavra_reservada_no_perfil_publico_retorna_404(): void
    {
        $response = $this->get('/admin');
        // A rota /admin é prefixo ou reservada, não deve cair no show do perfil
        $this->assertNotSame('Public/Profile', $response->baseResponse->original['component'] ?? null);
    }

    public function test_perfil_publico_oculta_email_quando_desautorizado(): void
    {
        $autor = User::factory()->create([
            'role' => User::ROLE_AUTOR,
            'email' => 'secreto@techpulse.dev.br',
        ]);
        $autor->refresh();

        $autor->profile->update([
            'show_email' => false,
            'public_profile_enabled' => true,
        ]);

        $response = $this->get('/'.$autor->profile->username);

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('author.email', null)
        );
    }

    public function test_perfil_publico_exibe_username_quando_nome_completo_desautorizado(): void
    {
        $autor = User::factory()->create([
            'name' => 'Nome Real Secreto',
            'role' => User::ROLE_AUTOR,
        ]);
        $autor->refresh();

        $autor->profile->update([
            'show_name' => false,
            'public_profile_enabled' => true,
        ]);

        $response = $this->get('/'.$autor->profile->username);

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('author.name', $autor->profile->username)
        );
    }
}

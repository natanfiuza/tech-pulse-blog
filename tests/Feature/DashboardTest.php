<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PostView;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_visualiza_metricas_completas_incluindo_categorias(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $autor = User::factory()->create(['role' => User::ROLE_AUTOR]);

        Category::create([
            'name' => 'Desenvolvimento',
            'slug' => 'desenvolvimento',
            'description' => 'Categoria de teste',
        ]);

        // Post do admin
        Post::create([
            'user_id' => $admin->id,
            'title' => 'Post Admin Publicado',
            'excerpt' => 'Resumo',
            'content' => 'Conteudo',
            'image' => '',
            'status' => 'publicado',
        ]);

        // Posts do autor
        $post_autor = Post::create([
            'user_id' => $autor->id,
            'title' => 'Post Autor Publicado',
            'excerpt' => 'Resumo',
            'content' => 'Conteudo',
            'image' => '',
            'status' => 'publicado',
        ]);

        Post::create([
            'user_id' => $autor->id,
            'title' => 'Post Autor Rascunho',
            'excerpt' => 'Resumo',
            'content' => 'Conteudo',
            'image' => '',
            'status' => 'rascunho',
        ]);

        Post::create([
            'user_id' => $autor->id,
            'title' => 'Post Autor Agendado',
            'excerpt' => 'Resumo',
            'content' => 'Conteudo',
            'image' => '',
            'status' => 'agendado',
            'published_at' => now()->addDays(2),
        ]);

        // Visualização
        PostView::create([
            'user_id' => $admin->id,
            'post_id' => $post_autor->id,
            'viewed_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.home'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/AdminHome')
            ->where('metricas.posts_publicados', 2)
            ->where('metricas.rascunhos', 1)
            ->where('metricas.agendados', 1)
            ->where('metricas.categorias', 1)
            ->where('metricas.total_visualizacoes', 1)
            ->where('metricas.is_admin', true)
            ->has('grafico_visualizacoes', 30)
        );
    }

    public function test_autor_visualiza_apenas_as_suas_proprias_metricas(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $autor = User::factory()->create(['role' => User::ROLE_AUTOR]);

        Category::create([
            'name' => 'Design',
            'slug' => 'design',
            'description' => 'Categoria de teste',
        ]);

        // Post do admin
        Post::create([
            'user_id' => $admin->id,
            'title' => 'Post Admin',
            'excerpt' => 'Resumo',
            'content' => 'Conteudo',
            'image' => '',
            'status' => 'publicado',
        ]);

        // Posts do autor
        $post_autor = Post::create([
            'user_id' => $autor->id,
            'title' => 'Post Autor',
            'excerpt' => 'Resumo',
            'content' => 'Conteudo',
            'image' => '',
            'status' => 'publicado',
        ]);

        PostView::create([
            'user_id' => $autor->id,
            'post_id' => $post_autor->id,
            'viewed_at' => now(),
        ]);

        $response = $this->actingAs($autor)->get(route('admin.home'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/AdminHome')
            ->where('metricas.posts_publicados', 1)
            ->where('metricas.rascunhos', 0)
            ->where('metricas.agendados', 0)
            ->where('metricas.categorias', 0)
            ->where('metricas.total_visualizacoes', 1)
            ->where('metricas.is_admin', false)
        );
    }

    public function test_autor_consegue_criar_post_agendado(): void
    {
        $autor = User::factory()->create(['role' => User::ROLE_AUTOR]);
        $data_agendada = now()->addDays(5)->format('Y-m-d\TH:i');

        $response = $this->actingAs($autor)->post(route('posts.store'), [
            'title' => 'Novo Artigo Futuro',
            'excerpt' => 'Resumo do artigo futuro',
            'content' => base64_encode('# Conteudo Markdown'),
            'status' => 'agendado',
            'published_at' => $data_agendada,
        ]);

        $response->assertRedirect(route('posts.index'));

        $this->assertDatabaseHas('posts', [
            'title' => 'Novo Artigo Futuro',
            'status' => 'agendado',
            'user_id' => $autor->id,
        ]);
    }

    public function test_leitor_consegue_excluir_proprio_comentario(): void
    {
        $leitor = User::factory()->create(['role' => User::ROLE_LEITOR]);
        $autor = User::factory()->create(['role' => User::ROLE_AUTOR]);

        $post = Post::create([
            'user_id' => $autor->id,
            'title' => 'Post Comentado',
            'excerpt' => 'Resumo',
            'content' => 'Conteudo',
            'image' => '',
            'status' => 'publicado',
        ]);

        $comentario = Comment::create([
            'user_id' => $leitor->id,
            'post_id' => $post->id,
            'content' => 'Comentario para ser excluido.',
        ]);

        $response = $this->actingAs($leitor)->delete(route('comments.destroy', ['comment' => $comentario->id]));

        $response->assertRedirect();
        $this->assertDatabaseMissing('comments', [
            'id' => $comentario->id,
        ]);
    }
}


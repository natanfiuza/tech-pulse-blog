<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ImagensDeConteudo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImagensDeConteudoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Um usuário com papel de autor (o grupo de rotas exige role:autor,admin).
     */
    private function autor(): User
    {
        return User::factory()->create(['role' => User::ROLE_AUTOR]);
    }

    public function test_autor_envia_imagem_e_recebe_a_url_absoluta_de_producao(): void
    {
        Storage::fake('local');

        $resposta = $this->actingAs($this->autor())->postJson(
            route('posts.content_images.store'),
            [
                'image' => UploadedFile::fake()->image('captura.png', 20, 20),
                'title' => 'Meu Post de Teste',
            ]
        );

        $resposta->assertCreated();

        $uuid = $resposta->json('uuid');
        $url_esperada = config('techpulse.url_publica').'/post/content/images/meu-post-teste/'.$uuid;

        $this->assertSame($url_esperada, $resposta->json('url'));
        Storage::disk('local')->assertExists(config('techpulse.imagens_conteudo').'/'.$uuid);
    }

    public function test_titulo_vazio_usa_o_slug_de_fallback(): void
    {
        Storage::fake('local');

        $resposta = $this->actingAs($this->autor())->postJson(
            route('posts.content_images.store'),
            ['image' => UploadedFile::fake()->image('captura.png', 20, 20)]
        );

        $resposta->assertCreated();
        $this->assertStringContainsString('/sem-titulo/', $resposta->json('url'));
    }

    public function test_arquivo_que_nao_e_imagem_e_recusado(): void
    {
        Storage::fake('local');

        $resposta = $this->actingAs($this->autor())->postJson(
            route('posts.content_images.store'),
            ['image' => UploadedFile::fake()->create('documento.pdf', 10, 'application/pdf')]
        );

        $resposta->assertStatus(422);
        $resposta->assertJsonValidationErrors('image');
    }

    public function test_imagem_acima_de_cinco_megabytes_e_recusada(): void
    {
        Storage::fake('local');

        $resposta = $this->actingAs($this->autor())->postJson(
            route('posts.content_images.store'),
            ['image' => UploadedFile::fake()->create('grande.png', 6000, 'image/png')]
        );

        $resposta->assertStatus(422);
        $resposta->assertJsonValidationErrors('image');
    }

    public function test_visitante_nao_pode_enviar_imagem(): void
    {
        Storage::fake('local');

        $resposta = $this->postJson(
            route('posts.content_images.store'),
            ['image' => UploadedFile::fake()->image('captura.png', 20, 20)]
        );

        $resposta->assertStatus(401);
    }

    public function test_leitor_nao_pode_enviar_imagem(): void
    {
        Storage::fake('local');

        $resposta = $this->actingAs(User::factory()->create(['role' => User::ROLE_LEITOR]))->postJson(
            route('posts.content_images.store'),
            ['image' => UploadedFile::fake()->image('captura.png', 20, 20)]
        );

        $resposta->assertStatus(403);
    }

    public function test_imagem_e_servida_com_o_slug_decorativo(): void
    {
        Storage::fake('local');

        $autor = $this->autor();

        $uuid = $this->actingAs($autor)->postJson(
            route('posts.content_images.store'),
            [
                'image' => UploadedFile::fake()->image('captura.png', 20, 20),
                'title' => 'Titulo Original',
            ]
        )->json('uuid');

        // O slug da URL não é o do título de propósito: a leitura resolve o
        // arquivo apenas pelo uuid, então renomear o post nunca quebra a imagem.
        $resposta = $this->get('/post/content/images/slug-completamente-diferente/'.$uuid);

        $resposta->assertOk();
        $this->assertSame('image/png', $resposta->headers->get('Content-Type'));
        $this->assertStringContainsString('immutable', $resposta->headers->get('Cache-Control'));
    }

    public function test_imagem_inexistente_devolve_404(): void
    {
        Storage::fake('local');

        $this->get('/post/content/images/qualquer-post/'.fake()->uuid())->assertNotFound();
    }

    public function test_uuid_invalido_na_url_devolve_404(): void
    {
        Storage::fake('local');

        $this->get('/post/content/images/qualquer-post/nao-e-um-uuid')->assertNotFound();
    }

    public function test_o_diretorio_configurado_e_o_do_enunciado(): void
    {
        $this->assertSame('data/post_content_images', config('techpulse.imagens_conteudo'));
        $this->assertSame('post/content/images', ImagensDeConteudo::ROTA);
    }
}

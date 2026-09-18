<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * A limpeza acontece no save: o que saiu do markdown é apagado do disco, desde
 * que nenhum outro post ainda referencie a imagem.
 */
class LimpezaDeImagensDeConteudoTest extends TestCase
{
    use RefreshDatabase;

    private User $autor;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->autor = User::factory()->create(['role' => User::ROLE_AUTOR]);
    }

    /**
     * Cria um post diretamente no banco (não há PostFactory no projeto e
     * user_id não está no $fillable do modelo).
     */
    private function criar_post(string $conteudo, string $titulo = 'Post de Teste'): Post
    {
        $post = new Post;
        $post->user_id = $this->autor->id;
        $post->uuid = (string) Str::uuid();
        $post->title = $titulo;
        $post->image = '';
        $post->excerpt = 'Resumo do post.';
        $post->content = $conteudo;
        $post->status = 'publicado';
        $post->save();

        return $post;
    }

    /**
     * Coloca um arquivo de imagem no disco e devolve o caminho relativo.
     */
    private function gravar_imagem(string $uuid): string
    {
        $caminho = config('techpulse.imagens_conteudo').'/'.$uuid;
        Storage::disk('local')->put($caminho, 'bytes-de-uma-imagem');

        return $caminho;
    }

    /**
     * Monta o markdown de uma imagem de conteúdo, como o editor o grava.
     */
    private function markdown_da_imagem(string $uuid, string $slug = 'post-de-teste'): string
    {
        return '![captura]('.config('techpulse.url_publica').'/post/content/images/'.$slug.'/'.$uuid.')';
    }

    /**
     * Salva o post pelo endpoint real de update (o diff é feito no controller).
     */
    private function salvar_post(Post $post, string $novo_conteudo)
    {
        return $this->actingAs($this->autor)->post(route('posts.update'), [
            'uuid' => $post->uuid,
            'title' => $post->title,
            'excerpt' => $post->excerpt,
            'content' => base64_encode($novo_conteudo),
            'status' => 'publicado',
        ]);
    }

    public function test_apaga_a_imagem_que_saiu_do_texto(): void
    {
        $uuid_que_fica = (string) Str::uuid();
        $uuid_removida = (string) Str::uuid();

        $this->gravar_imagem($uuid_que_fica);
        $caminho_removida = $this->gravar_imagem($uuid_removida);

        $post = $this->criar_post(
            "Texto com duas imagens.\n\n"
            .$this->markdown_da_imagem($uuid_que_fica)."\n\n"
            .$this->markdown_da_imagem($uuid_removida)
        );

        $this->salvar_post($post, "Texto com uma imagem só.\n\n".$this->markdown_da_imagem($uuid_que_fica))
            ->assertRedirect(route('posts.index'));

        Storage::disk('local')->assertMissing($caminho_removida);
        Storage::disk('local')->assertExists(config('techpulse.imagens_conteudo').'/'.$uuid_que_fica);
    }

    public function test_nao_apaga_nada_quando_o_texto_nao_muda(): void
    {
        $uuid = (string) Str::uuid();
        $caminho = $this->gravar_imagem($uuid);

        $conteudo = "Texto com imagem.\n\n".$this->markdown_da_imagem($uuid);
        $post = $this->criar_post($conteudo);

        $this->salvar_post($post, $conteudo)->assertRedirect(route('posts.index'));

        Storage::disk('local')->assertExists($caminho);
    }

    public function test_nao_apaga_imagem_que_outro_post_ainda_referencia(): void
    {
        $uuid = (string) Str::uuid();
        $caminho = $this->gravar_imagem($uuid);

        $post = $this->criar_post("Imagem emprestada.\n\n".$this->markdown_da_imagem($uuid));

        // Segundo post usando exatamente a mesma imagem.
        $this->criar_post($this->markdown_da_imagem($uuid), 'Outro Post');

        $this->salvar_post($post, 'Agora sem imagem.')->assertRedirect(route('posts.index'));

        Storage::disk('local')->assertExists($caminho);
    }

    public function test_destroy_apaga_as_imagens_de_conteudo_do_post(): void
    {
        $uuid_um = (string) Str::uuid();
        $uuid_dois = (string) Str::uuid();

        $this->gravar_imagem($uuid_um);
        $this->gravar_imagem($uuid_dois);

        $post = $this->criar_post(
            $this->markdown_da_imagem($uuid_um)."\n\n".$this->markdown_da_imagem($uuid_dois)
        );

        $this->actingAs($this->autor)
            ->delete(route('posts.destroy', ['uuid' => $post->uuid]))
            ->assertRedirect(route('posts.index'));

        Storage::disk('local')->assertMissing(config('techpulse.imagens_conteudo').'/'.$uuid_um);
        Storage::disk('local')->assertMissing(config('techpulse.imagens_conteudo').'/'.$uuid_dois);
    }

    public function test_destroy_nao_apaga_imagem_referenciada_por_outro_post(): void
    {
        $uuid = (string) Str::uuid();
        $caminho = $this->gravar_imagem($uuid);

        $post = $this->criar_post("Imagem emprestada.\n\n".$this->markdown_da_imagem($uuid));
        $this->criar_post($this->markdown_da_imagem($uuid), 'Outro Post');

        $this->actingAs($this->autor)
            ->delete(route('posts.destroy', ['uuid' => $post->uuid]))
            ->assertRedirect(route('posts.index'));

        Storage::disk('local')->assertExists($caminho);
    }
}

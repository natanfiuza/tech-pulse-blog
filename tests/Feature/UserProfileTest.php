<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AvatarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_novo_recebe_uuid_e_perfil_automaticamente(): void
    {
        Storage::fake(AvatarService::DISCO);

        $user = User::factory()->create([
            'name' => 'Nataniel Fiuza',
            'email' => 'natan@teste.com',
            'role' => User::ROLE_AUTOR,
        ]);

        $this->assertNotNull($user->uuid);
        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id,
            'username' => 'nataniel-fiuza',
        ]);

        $avatar_service = app(AvatarService::class);
        $this->assertTrue(Storage::disk(AvatarService::DISCO)->exists($avatar_service->caminho($user->uuid)));
    }

    public function test_avatar_padrao_possui_iniciais_corretas(): void
    {
        $avatar_service = app(AvatarService::class);

        $this->assertSame('NF', $avatar_service->extrair_iniciais('Nataniel Fiuza'));
        $this->assertSame('NS', $avatar_service->extrair_iniciais('Nataniel Silva Santos'));
        $this->assertSame('NA', $avatar_service->extrair_iniciais('Natan'));

        $svg = $avatar_service->gerar_svg_padrao('Nataniel Fiuza');
        $this->assertStringContainsString('NF', $svg);
        $this->assertStringContainsString('#1A1A1A', $svg);
    }

    public function test_rota_avatar_serve_imagem(): void
    {
        Storage::fake(AvatarService::DISCO);

        $user = User::factory()->create([
            'name' => 'Carlos Silva',
            'role' => User::ROLE_AUTOR,
        ]);

        $response = $this->get(route('user.avatar', ['uuid' => $user->uuid]));

        $response->assertOk();
        $this->assertStringContainsString('image/svg+xml', (string) $response->headers->get('Content-Type'));
    }

    public function test_autor_consegue_atualizar_perfil(): void
    {
        Storage::fake(AvatarService::DISCO);

        $user = User::factory()->create([
            'name' => 'Nome Antigo',
            'role' => User::ROLE_AUTOR,
        ]);

        $response = $this->actingAs($user)->put(route('admin.profile.update'), [
            'name' => 'Nome Novo',
            'username' => 'nomenovo',
            'bio' => 'Minha nova bio de tecnologia.',
            'public_profile_enabled' => true,
            'show_email' => true,
            'show_name' => true,
            'show_author_box' => true,
            'social_links' => [
                'github' => ['url' => 'https://github.com/nomenovo', 'show' => true],
            ],
        ]);

        $response->assertRedirect();
        $user->refresh();

        $this->assertSame('Nome Novo', $user->name);
        $this->assertSame('nomenovo', $user->profile->username);
        $this->assertSame('Minha nova bio de tecnologia.', $user->profile->bio);
        $this->assertTrue($user->profile->show_email);
        $this->assertSame('https://github.com/nomenovo', $user->profile->social_links['github']['url']);
    }

    public function test_username_reservado_e_rejeitado(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_AUTOR,
        ]);

        $response = $this->actingAs($user)->put(route('admin.profile.update'), [
            'name' => $user->name,
            'username' => 'admin',
        ]);

        $response->assertSessionHasErrors('username');
    }

    public function test_autor_consegue_enviar_foto_e_depois_redefinir_para_padrao(): void
    {
        Storage::fake(AvatarService::DISCO);

        $user = User::factory()->create([
            'role' => User::ROLE_AUTOR,
        ]);
        $user->refresh();

        $avatar_service = app(AvatarService::class);

        // Upload de nova foto
        $foto = UploadedFile::fake()->image('meu_avatar.png', 200, 200);
        $response = $this->actingAs($user)->put(route('admin.profile.update'), [
            'name' => $user->name,
            'username' => $user->profile->username,
            'avatar' => $foto,
        ]);

        $response->assertRedirect();
        $this->assertTrue(Storage::disk(AvatarService::DISCO)->exists($avatar_service->caminho($user->uuid)));

        // Redefinir para iniciais padrão
        $response_reset = $this->actingAs($user)->put(route('admin.profile.update'), [
            'name' => $user->name,
            'username' => $user->profile->username,
            'reset_avatar' => true,
        ]);

        $response_reset->assertRedirect();
        $conteudo_arquivo = Storage::disk(AvatarService::DISCO)->get($avatar_service->caminho($user->uuid));
        $this->assertStringContainsString('<svg', $conteudo_arquivo);
    }
}

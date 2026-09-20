<?php

namespace Tests\Feature;

use App\Models\NewsletterConfirmation;
use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class NewsletterConfirmTest extends TestCase
{
    use RefreshDatabase;

    public function test_tela_de_confirmacao_exibe_formulario_para_token_valido(): void
    {
        $uuid = (string) Str::uuid();
        NewsletterConfirmation::create([
            'uuid' => $uuid,
            'email' => 'dev@techpulse.test',
            'is_confirmed' => false,
        ]);

        $response = $this->get(route('newsletter.confirm', ['uuid' => $uuid]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('NewsletterConfirmPage')
            ->where('status', 'form')
            ->where('confirmation.email', 'dev@techpulse.test')
        );
    }

    public function test_tela_de_confirmacao_retorna_not_found_para_token_inexistente(): void
    {
        $response = $this->get(route('newsletter.confirm', ['uuid' => (string) Str::uuid()]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('NewsletterConfirmPage')
            ->where('status', 'not_found')
        );
    }

    public function test_tela_de_confirmacao_retorna_expired_quando_criado_ha_mais_de_24h(): void
    {
        $uuid = (string) Str::uuid();
        $confirmation = NewsletterConfirmation::create([
            'uuid' => $uuid,
            'email' => 'dev@techpulse.test',
            'is_confirmed' => false,
        ]);
        $confirmation->created_at = now()->subHours(25);
        $confirmation->save();

        $response = $this->get(route('newsletter.confirm', ['uuid' => $uuid]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('NewsletterConfirmPage')
            ->where('status', 'expired')
        );
    }

    public function test_submissao_valida_cria_assinante_ativo_e_marca_confirmacao(): void
    {
        $uuid = (string) Str::uuid();
        $confirmation = NewsletterConfirmation::create([
            'uuid' => $uuid,
            'email' => 'dev@techpulse.test',
            'is_confirmed' => false,
        ]);

        $response = $this->post(route('newsletter.confirm.submit', ['uuid' => $uuid]), [
            'name' => 'Nataniel Fiuza',
            'email' => 'dev@techpulse.test',
            'github' => 'natanfiuza',
            'phone' => '+55 11 99999-9999',
            'lang' => 'pt_br',
        ]);

        $response->assertRedirect(route('newsletter.confirm', ['uuid' => $uuid, 'lang' => 'pt_br']));

        $this->assertDatabaseHas('newsletter_subscribers', [
            'name' => 'Nataniel Fiuza',
            'email' => 'dev@techpulse.test',
            'github' => 'natanfiuza',
            'lang' => 'pt_br',
            'is_canceled' => false,
        ]);

        $this->assertTrue($confirmation->fresh()->is_confirmed);
        $this->assertNotNull($confirmation->fresh()->date_confirmed);
    }

    public function test_submissao_com_email_divergente_do_token_retorna_erro_422(): void
    {
        $uuid = (string) Str::uuid();
        NewsletterConfirmation::create([
            'uuid' => $uuid,
            'email' => 'dev@techpulse.test',
            'is_confirmed' => false,
        ]);

        $response = $this->post(route('newsletter.confirm.submit', ['uuid' => $uuid]), [
            'name' => 'Invasor',
            'email' => 'outro@email.test',
            'lang' => 'pt_br',
        ]);

        $response->assertStatus(422);
    }

    public function test_submissao_reativa_assinante_previamente_cancelado(): void
    {
        $uuid = (string) Str::uuid();
        NewsletterConfirmation::create([
            'uuid' => $uuid,
            'email' => 'dev@techpulse.test',
            'is_confirmed' => false,
        ]);

        NewsletterSubscriber::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Antigo Nome',
            'email' => 'dev@techpulse.test',
            'is_canceled' => true,
            'date_canceled' => now()->subMonths(2),
        ]);

        $response = $this->post(route('newsletter.confirm.submit', ['uuid' => $uuid]), [
            'name' => 'Novo Nome',
            'email' => 'dev@techpulse.test',
            'lang' => 'en',
        ]);

        $response->assertRedirect();

        $subscriber = NewsletterSubscriber::where('email', 'dev@techpulse.test')->first();
        $this->assertFalse($subscriber->is_canceled);
        $this->assertNull($subscriber->date_canceled);
        $this->assertEquals('Novo Nome', $subscriber->name);
        $this->assertEquals('en', $subscriber->lang);
    }
}

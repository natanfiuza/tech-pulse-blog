<?php

namespace Tests\Feature;

use App\Mail\ConfirmationMail;
use App\Models\NewsletterConfirmation;
use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class NewsletterSubscribeTest extends TestCase
{
    use RefreshDatabase;

    public function test_inscricao_valida_cria_confirmacao_pendente_e_envia_email(): void
    {
        Mail::fake();

        $response = $this->post(route('newsletter.subscribe'), [
            'email' => 'dev@techpulse.test',
            'lang' => 'pt_br',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('newsletter.status', 'sent');

        $this->assertDatabaseHas('newsletter_confirmations', [
            'email' => 'dev@techpulse.test',
            'is_confirmed' => false,
        ]);

        Mail::assertSent(ConfirmationMail::class, function ($mail) {
            return $mail->hasTo('dev@techpulse.test');
        });
    }

    public function test_inscricao_com_confirmacao_pendente_reutiliza_mesmo_uuid_dentro_de_24h(): void
    {
        Mail::fake();

        $uuid = (string) Str::uuid();
        NewsletterConfirmation::create([
            'uuid' => $uuid,
            'email' => 'dev@techpulse.test',
            'is_confirmed' => false,
            'created_at' => now()->subHours(2),
        ]);

        $response = $this->post(route('newsletter.subscribe'), [
            'email' => 'dev@techpulse.test',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('newsletter.status', 'sent');

        $this->assertEquals(1, NewsletterConfirmation::where('email', 'dev@techpulse.test')->count());
        $this->assertEquals($uuid, NewsletterConfirmation::where('email', 'dev@techpulse.test')->first()->uuid);

        Mail::assertSent(ConfirmationMail::class);
    }

    public function test_inscricao_com_confirmacao_expirada_cria_novo_registro(): void
    {
        Mail::fake();

        $oldUuid = (string) Str::uuid();
        $expired = NewsletterConfirmation::create([
            'uuid' => $oldUuid,
            'email' => 'dev@techpulse.test',
            'is_confirmed' => false,
        ]);
        $expired->created_at = now()->subHours(25);
        $expired->save();

        $response = $this->post(route('newsletter.subscribe'), [
            'email' => 'dev@techpulse.test',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('newsletter.status', 'sent');

        $this->assertEquals(2, NewsletterConfirmation::where('email', 'dev@techpulse.test')->count());
    }

    public function test_inscricao_de_email_ja_confirmado_retorna_status_already_confirmed(): void
    {
        Mail::fake();

        NewsletterSubscriber::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Nataniel',
            'email' => 'dev@techpulse.test',
            'lang' => 'pt_br',
            'is_canceled' => false,
        ]);

        $response = $this->post(route('newsletter.subscribe'), [
            'email' => 'dev@techpulse.test',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('newsletter.status', 'already_confirmed');

        Mail::assertNothingSent();
    }

    public function test_inscricao_com_email_invalido_retorna_erro_de_validacao(): void
    {
        $response = $this->post(route('newsletter.subscribe'), [
            'email' => 'email-invalido',
        ]);

        $response->assertSessionHasErrors('email');
    }
}


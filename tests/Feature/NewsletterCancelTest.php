<?php

namespace Tests\Feature;

use App\Mail\CancelLinkMail;
use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class NewsletterCancelTest extends TestCase
{
    use RefreshDatabase;

    public function test_solicitacao_de_link_de_cancelamento_envia_email_para_inscrito_ativo(): void
    {
        Mail::fake();

        $uuid = (string) Str::uuid();
        NewsletterSubscriber::create([
            'uuid' => $uuid,
            'name' => 'Nataniel',
            'email' => 'dev@techpulse.test',
            'is_canceled' => false,
        ]);

        $response = $this->post(route('newsletter.send-cancel-link'), [
            'email' => 'dev@techpulse.test',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('newsletter.status', 'cancel_link_sent');

        Mail::assertSent(CancelLinkMail::class, function ($mail) use ($uuid) {
            return $mail->hasTo('dev@techpulse.test') && str_contains($mail->url, $uuid);
        });
    }

    public function test_solicitacao_de_link_com_email_inexistente_retorna_resposta_generica_sem_enviar_email(): void
    {
        Mail::fake();

        $response = $this->post(route('newsletter.send-cancel-link'), [
            'email' => 'desconhecido@techpulse.test',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('newsletter.status', 'cancel_link_sent');

        Mail::assertNothingSent();
    }

    public function test_pagina_de_cancelamento_exibe_confirmacao_para_uuid_valido(): void
    {
        $uuid = (string) Str::uuid();
        NewsletterSubscriber::create([
            'uuid' => $uuid,
            'name' => 'Nataniel',
            'email' => 'dev@techpulse.test',
            'is_canceled' => false,
        ]);

        $response = $this->get(route('newsletter.cancel', ['uuid' => $uuid]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('NewsletterCancelPage')
            ->where('status', 'form')
            ->where('subscriber.email', 'dev@techpulse.test')
        );
    }

    public function test_execucao_do_cancelamento_marca_como_cancelado(): void
    {
        $uuid = (string) Str::uuid();
        $subscriber = NewsletterSubscriber::create([
            'uuid' => $uuid,
            'name' => 'Nataniel',
            'email' => 'dev@techpulse.test',
            'is_canceled' => false,
        ]);

        $response = $this->post(route('newsletter.unsubscribe', ['uuid' => $uuid]));

        $response->assertRedirect(route('newsletter.cancel', ['uuid' => $uuid, 'lang' => 'pt_br', 'done' => 1]));

        $this->assertTrue($subscriber->fresh()->is_canceled);
        $this->assertNotNull($subscriber->fresh()->date_canceled);
    }
}

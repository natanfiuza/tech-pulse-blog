<?php

namespace Tests\Feature;

use App\Mail\NewsletterMail;
use App\Models\NewsletterSubscriber;
use App\Support\NewsletterContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class NewsletterSendCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        // Cria o arquivo temporário de edição para os testes
        $dir = public_path('content/newsletter/test_edition');
        File::ensureDirectoryExists($dir);
        File::put("{$dir}/newsletter_body.md", "# Test Edition\n\nConteúdo técnico de teste.");
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(public_path('content/newsletter/test_edition'));
        parent::tearDown();
    }

    public function test_envio_da_newsletter_para_todos_os_assinantes_ativos(): void
    {
        Mail::fake();

        NewsletterSubscriber::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Assinante 1',
            'email' => 'sub1@techpulse.test',
            'lang' => 'pt_br',
            'is_canceled' => false,
        ]);

        NewsletterSubscriber::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Assinante 2',
            'email' => 'sub2@techpulse.test',
            'lang' => 'en',
            'is_canceled' => false,
        ]);

        NewsletterSubscriber::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Assinante Cancelado',
            'email' => 'cancelado@techpulse.test',
            'lang' => 'pt_br',
            'is_canceled' => true,
        ]);

        $this->artisan('newsletter:send', [
            'edition' => 'test_edition',
            '--interval' => 0,
        ])->assertSuccessful();

        Mail::assertSent(NewsletterMail::class, 2);
        Mail::assertSent(NewsletterMail::class, fn ($mail) => $mail->hasTo('sub1@techpulse.test'));
        Mail::assertSent(NewsletterMail::class, fn ($mail) => $mail->hasTo('sub2@techpulse.test'));
        Mail::assertNotSent(NewsletterMail::class, fn ($mail) => $mail->hasTo('cancelado@techpulse.test'));

        $this->assertEquals('test_edition', NewsletterContent::last_sent_edition());
    }

    public function test_bloqueia_reenvio_sem_opcao_force(): void
    {
        Mail::fake();

        NewsletterContent::mark_sent('test_edition');

        NewsletterSubscriber::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Assinante 1',
            'email' => 'sub1@techpulse.test',
            'is_canceled' => false,
        ]);

        $this->artisan('newsletter:send', [
            'edition' => 'test_edition',
        ])
            ->expectsOutputToContain('já foi disparada')
            ->assertSuccessful();

        Mail::assertNothingSent();
    }

    public function test_permite_reenvio_com_opcao_force(): void
    {
        Mail::fake();

        NewsletterContent::mark_sent('test_edition');

        NewsletterSubscriber::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Assinante 1',
            'email' => 'sub1@techpulse.test',
            'is_canceled' => false,
        ]);

        $this->artisan('newsletter:send', [
            'edition' => 'test_edition',
            '--force' => true,
            '--interval' => 0,
        ])->assertSuccessful();

        Mail::assertSent(NewsletterMail::class, 1);
    }

    public function test_retorna_falha_quando_corpo_da_edicao_nao_existe(): void
    {
        $this->artisan('newsletter:send', [
            'edition' => 'edicao_inexistente_999',
        ])->assertFailed();
    }
}


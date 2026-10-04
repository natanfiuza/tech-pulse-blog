<?php

namespace App\Console\Commands;

use App\Mail\NewsletterMail;
use App\Models\NewsletterSubscriber;
use App\Support\NewsletterContent;
use App\Support\NewsletterTranslations;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

class NewsletterSendCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'newsletter:send
                            {edition? : Edição da newsletter (ex: 01, 1)}
                            {--force : Ignora aviso de já enviado e tempo estimado de envio}
                            {--interval=5 : Intervalo em segundos entre cada disparo}';

    /**
     * @var string
     */
    protected $description = 'Dispara a edição da newsletter semanal para todos os assinantes ativos.';

    public function handle(): int
    {
        $edition = $this->argument('edition') ?: NewsletterContent::latest_edition();

        if (! $edition) {
            $this->error('Nenhuma edição encontrada em public/content/newsletter. Especifique uma edição: php artisan newsletter:send 01');

            return self::FAILURE;
        }

        if (NewsletterContent::last_sent_edition() === $edition && ! $this->option('force')) {
            $this->info("A edição {$edition} da newsletter já foi disparada anteriormente. Utilize --force para reenviar.");

            return self::SUCCESS;
        }

        // Fail-fast: valida se a edição possui corpo em algum idioma antes de iterar sobre os inscritos
        if (! NewsletterContent::edition_exists($edition)) {
            $this->error("Corpo da newsletter não encontrado para a edição {$edition}.");

            return self::FAILURE;
        }

        $subscribers = NewsletterSubscriber::active()->get();

        if ($subscribers->isEmpty()) {
            $this->info('Nenhum assinante ativo encontrado.');

            return self::SUCCESS;
        }

        // Avisa quais idiomas vão cair no fallback pt-BR por não terem tradução própria
        $langs_com_assinantes = $subscribers
            ->map(fn (NewsletterSubscriber $subscriber) => NewsletterTranslations::normalize($subscriber->lang))
            ->unique();

        foreach ($langs_com_assinantes as $lang_assinante) {
            if ($lang_assinante !== 'pt_br' && ! NewsletterContent::body_exists($edition, $lang_assinante)) {
                $this->warn("Sem tradução própria para o idioma {$lang_assinante}: os assinantes receberão o corpo em pt_br.");
            }
        }

        $interval = (int) $this->option('interval');
        $estimatedSeconds = $subscribers->count() * max(0, $interval);

        if ($estimatedSeconds > 3600 && ! $this->option('force')) {
            $this->warn(sprintf(
                'O envio para %d assinantes levará cerca de %.1f horas. Utilize --force para prosseguir.',
                $subscribers->count(),
                $estimatedSeconds / 3600
            ));

            return self::FAILURE;
        }

        $bar = $this->output->createProgressBar($subscribers->count());

        foreach ($subscribers as $subscriber) {
            $lang = NewsletterTranslations::normalize($subscriber->lang);

            try {
                $html = NewsletterContent::body_html($edition, $lang);

                Mail::to($subscriber->email)->send(new NewsletterMail(
                    htmlBody: $html,
                    edition: $edition,
                    unsubscribeUrl: route('newsletter.cancel', [
                        'uuid' => $subscriber->uuid,
                        'lang' => $lang,
                    ]),
                    strings: NewsletterTranslations::for($lang),
                    lang: $lang,
                ));
            } catch (Throwable $e) {
                // Falha em um assinante individual não interrompe o lote
                $this->warn("Falha ao enviar para {$subscriber->email}: {$e->getMessage()}");
            }

            $bar->advance();

            if ($interval > 0) {
                sleep($interval);
            }
        }

        $bar->finish();
        $this->newLine();

        NewsletterContent::mark_sent($edition);

        $this->info("Newsletter edição {$edition} enviada com sucesso para {$subscribers->count()} assinante(s).");

        return self::SUCCESS;
    }
}

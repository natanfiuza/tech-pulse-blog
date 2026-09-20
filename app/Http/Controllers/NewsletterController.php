<?php

namespace App\Http\Controllers;

use App\Mail\CancelLinkMail;
use App\Mail\ConfirmationMail;
use App\Models\NewsletterConfirmation;
use App\Models\NewsletterSubscriber;
use App\Support\NewsletterTranslations;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class NewsletterController extends Controller
{
    /**
     * Inicia o fluxo de inscrição via Double Opt-in.
     */
    public function subscribe(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $lang = NewsletterTranslations::normalize($request->input('lang', 'pt_br'));
        $email = strtolower(trim($validated['email']));

        // Se já for um assinante ativo
        if (NewsletterSubscriber::where('email', $email)->where('is_canceled', false)->exists()) {
            return back()->with('newsletter', [
                'status' => 'already_confirmed',
                'email' => $email,
            ]);
        }

        // Reutiliza confirmação pendente válida (< 24h) ou cria uma nova
        $pending = NewsletterConfirmation::where('email', $email)
            ->where('is_confirmed', false)
            ->notExpired()
            ->first();

        $confirmation = $pending ?: NewsletterConfirmation::create([
            'uuid' => (string) Str::uuid(),
            'email' => $email,
            'is_confirmed' => false,
        ]);

        Mail::to($email)->send(new ConfirmationMail(
            url: route('newsletter.confirm', ['uuid' => $confirmation->uuid, 'lang' => $lang]),
            strings: NewsletterTranslations::for($lang),
            lang: $lang,
        ));

        return back()->with('newsletter', [
            'status' => 'sent',
            'email' => $email,
        ]);
    }

    /**
     * Envia link de cancelamento sob demanda (resposta com anti-enumeração).
     */
    public function send_cancel_link(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $lang = NewsletterTranslations::normalize($request->input('lang', 'pt_br'));
        $email = strtolower(trim($validated['email']));

        $subscriber = NewsletterSubscriber::where('email', $email)
            ->where('is_canceled', false)
            ->first();

        if ($subscriber) {
            Mail::to($email)->send(new CancelLinkMail(
                url: route('newsletter.cancel', ['uuid' => $subscriber->uuid, 'lang' => $lang]),
                strings: NewsletterTranslations::for($lang),
                lang: $lang,
            ));
        }

        return back()->with('newsletter', [
            'status' => 'cancel_link_sent',
        ]);
    }

    /**
     * Exibe a página de confirmação de inscrição.
     */
    public function confirm(Request $request, string $uuid): Response
    {
        $lang = NewsletterTranslations::normalize($request->query('lang', 'pt_br'));
        $strings = NewsletterTranslations::for($lang);

        $confirmation = NewsletterConfirmation::where('uuid', $uuid)->first();

        if (! $confirmation) {
            return Inertia::render('NewsletterConfirmPage', [
                'status' => 'not_found',
                'current_lang' => $lang,
                'ui_strings' => $strings,
            ]);
        }

        if ($confirmation->is_confirmed) {
            return Inertia::render('NewsletterConfirmPage', [
                'status' => 'already_confirmed',
                'confirmation' => [
                    'uuid' => $confirmation->uuid,
                    'email' => $confirmation->email,
                ],
                'current_lang' => $lang,
                'ui_strings' => $strings,
            ]);
        }

        if ($confirmation->created_at->lt(now()->subHours(24))) {
            return Inertia::render('NewsletterConfirmPage', [
                'status' => 'expired',
                'confirmation' => [
                    'uuid' => $confirmation->uuid,
                    'email' => $confirmation->email,
                ],
                'current_lang' => $lang,
                'ui_strings' => $strings,
            ]);
        }

        return Inertia::render('NewsletterConfirmPage', [
            'status' => 'form',
            'confirmation' => [
                'uuid' => $confirmation->uuid,
                'email' => $confirmation->email,
            ],
            'current_lang' => $lang,
            'ui_strings' => $strings,
        ]);
    }

    /**
     * Processa a submissão do formulário de perfil e ativa a inscrição.
     */
    public function confirm_submit(Request $request, string $uuid): RedirectResponse
    {
        $confirmation = NewsletterConfirmation::where('uuid', $uuid)->firstOrFail();

        if ($confirmation->is_confirmed || $confirmation->created_at->lt(now()->subHours(24))) {
            return redirect()->route('newsletter.confirm', ['uuid' => $uuid]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'github' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'lang' => ['required', 'string', 'in:'.implode(',', NewsletterTranslations::LANGS)],
        ]);

        if (strtolower(trim($validated['email'])) !== strtolower(trim($confirmation->email))) {
            abort(422, 'O e-mail informado não confere com o token de confirmação.');
        }

        $subscriber = NewsletterSubscriber::firstOrNew([
            'email' => strtolower(trim($confirmation->email)),
        ]);

        $subscriber->uuid = $subscriber->uuid ?? (string) Str::uuid();
        $subscriber->name = trim($validated['name']);
        $subscriber->github = ! empty($validated['github']) ? trim($validated['github']) : null;
        $subscriber->phone = ! empty($validated['phone']) ? trim($validated['phone']) : null;
        $subscriber->lang = $validated['lang'];
        $subscriber->is_canceled = false;
        $subscriber->date_canceled = null;
        $subscriber->save();

        $confirmation->update([
            'is_confirmed' => true,
            'date_confirmed' => now(),
        ]);

        return redirect()->route('newsletter.confirm', [
            'uuid' => $uuid,
            'lang' => $validated['lang'],
        ]);
    }

    /**
     * Exibe a página de cancelamento de inscrição.
     */
    public function cancel(Request $request, string $uuid): Response
    {
        $lang = NewsletterTranslations::normalize($request->query('lang', 'pt_br'));
        $strings = NewsletterTranslations::for($lang);

        $subscriber = NewsletterSubscriber::where('uuid', $uuid)->first();

        if (! $subscriber) {
            return Inertia::render('NewsletterCancelPage', [
                'status' => 'not_found',
                'current_lang' => $lang,
                'ui_strings' => $strings,
            ]);
        }

        if ($request->query('done')) {
            return Inertia::render('NewsletterCancelPage', [
                'status' => 'done',
                'current_lang' => $lang,
                'ui_strings' => $strings,
            ]);
        }

        if ($subscriber->is_canceled) {
            return Inertia::render('NewsletterCancelPage', [
                'status' => 'already_canceled',
                'current_lang' => $lang,
                'ui_strings' => $strings,
            ]);
        }

        return Inertia::render('NewsletterCancelPage', [
            'status' => 'form',
            'subscriber' => [
                'uuid' => $subscriber->uuid,
                'email' => $subscriber->email,
            ],
            'current_lang' => $lang,
            'ui_strings' => $strings,
        ]);
    }

    /**
     * Processa o cancelamento da inscrição.
     */
    public function unsubscribe(Request $request, string $uuid): RedirectResponse
    {
        $subscriber = NewsletterSubscriber::where('uuid', $uuid)->first();

        if ($subscriber) {
            $subscriber->update([
                'is_canceled' => true,
                'date_canceled' => now(),
            ]);
        }

        $lang = $subscriber ? $subscriber->lang : 'pt_br';

        return redirect()->route('newsletter.cancel', [
            'uuid' => $uuid,
            'lang' => $lang,
            'done' => 1,
        ]);
    }
}

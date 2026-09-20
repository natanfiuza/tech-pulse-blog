<?php

namespace App\Support;

final class NewsletterTranslations
{
    public const LANGS = ['pt_br', 'en', 'pt_pt', 'es', 'fr'];

    /**
     * @var array<string, array<string, string>>
     */
    private const STRINGS = [
        'pt_br' => [
            'mail_confirm_subject' => 'Confirme sua inscrição na Newsletter TechPulse',
            'mail_confirm_intro' => 'Obrigado pelo interesse na Newsletter semanal do TechPulse! Clique no botão abaixo para confirmar seu e-mail e completar sua inscrição.',
            'mail_confirm_button' => 'Confirmar Inscrição',
            'mail_confirm_note_24h' => 'Este link expira em 24 horas. Se você não solicitou esta inscrição, ignore esta mensagem.',
            'mail_cancel_subject' => 'Link para cancelar inscrição na Newsletter TechPulse',
            'mail_cancel_intro' => 'Recebemos uma solicitação para cancelar o envio da Newsletter TechPulse. Clique no botão abaixo para confirmar o descadastro.',
            'mail_cancel_button' => 'Cancelar Inscrição',
            'mail_newsletter_subject' => 'TechPulse #{edition} — O Pulsar da Tecnologia',
            'mail_newsletter_unsubscribe' => 'Cancelar recebimento desta newsletter',
            'ui_subscribe_title' => 'Boletim Informativo',
            'ui_subscribe_desc' => 'Receba os pulsos de tecnologia e hacks de programação diretamente no seu e-mail, toda segunda-feira.',
            'ui_subscribe_placeholder' => 'seu@email.com',
            'ui_subscribe_button' => 'Inscrever Agora',
            'ui_subscribe_sending' => 'Enviando...',
            'ui_subscribe_sent' => 'Enviamos um e-mail com o link para confirmar sua inscrição. Verifique sua caixa de entrada!',
            'ui_subscribe_already_confirmed' => 'Este e-mail já está inscrito em nossa newsletter.',
            'ui_subscribe_send_cancel' => 'Enviar link de cancelamento',
            'ui_subscribe_cancel_sent' => 'Se o e-mail estiver cadastrado, enviamos o link para cancelamento.',
            'ui_confirm_title' => 'Confirmar Inscrição na Newsletter',
            'ui_confirm_desc' => 'Complete seus dados para receber as edições semanais do TechPulse toda segunda-feira.',
            'ui_confirm_name_label' => 'Nome Completo',
            'ui_confirm_name_placeholder' => 'Seu nome',
            'ui_confirm_email_label' => 'E-mail',
            'ui_confirm_github_label' => 'Usuário do GitHub (opcional)',
            'ui_confirm_github_placeholder' => 'ex: octocat',
            'ui_confirm_phone_label' => 'Telefone (opcional)',
            'ui_confirm_phone_placeholder' => '+55 11 99999-9999',
            'ui_confirm_lang_label' => 'Idioma de Preferência',
            'ui_confirm_submit' => 'Confirmar Inscrição',
            'ui_confirm_submitting' => 'Confirmando...',
            'ui_confirm_already_confirmed_title' => 'Inscrição já confirmada!',
            'ui_confirm_already_confirmed_desc' => 'Seu e-mail já está ativo e você receberá as próximas edições da newsletter.',
            'ui_confirm_expired_title' => 'Link de confirmação expirado',
            'ui_confirm_expired_desc' => 'Este link expirou pois foi gerado há mais de 24 horas. Por favor, solicite uma nova inscrição na página inicial.',
            'ui_confirm_not_found_title' => 'Link inválido ou não encontrado',
            'ui_confirm_not_found_desc' => 'Não encontramos a solicitação de confirmação para este link.',
            'ui_cancel_title' => 'Cancelar Inscrição na Newsletter',
            'ui_cancel_confirm_question' => 'Tem certeza de que deseja parar de receber a newsletter semanal do TechPulse?',
            'ui_cancel_submit' => 'Cancelar Inscrição',
            'ui_cancel_submitting' => 'Cancelando...',
            'ui_cancel_done_title' => 'Inscrição cancelada com sucesso',
            'ui_cancel_done_desc' => 'Você não receberá mais os e-mails da newsletter TechPulse. Sentiremos sua falta!',
            'ui_cancel_already_title' => 'Inscrição já cancelada',
            'ui_cancel_already_desc' => 'Este e-mail já estava descadastrado da nossa lista.',
            'ui_cancel_not_found_title' => 'Link inválido',
            'ui_cancel_not_found_desc' => 'Assinante não encontrado.',
            'ui_back_to_home' => 'Voltar para a Página Inicial',
        ],
        'en' => [
            'mail_confirm_subject' => 'Confirm your subscription to TechPulse Newsletter',
            'mail_confirm_intro' => 'Thank you for subscribing to TechPulse weekly newsletter! Click the button below to confirm your email and complete your subscription.',
            'mail_confirm_button' => 'Confirm Subscription',
            'mail_confirm_note_24h' => 'This link expires in 24 hours. If you did not request this, you can ignore this email.',
            'mail_cancel_subject' => 'Link to unsubscribe from TechPulse Newsletter',
            'mail_cancel_intro' => 'We received a request to unsubscribe from TechPulse Newsletter. Click the button below to confirm.',
            'mail_cancel_button' => 'Unsubscribe',
            'mail_newsletter_subject' => 'TechPulse #{edition} — The Pulse of Technology',
            'mail_newsletter_unsubscribe' => 'Unsubscribe from this newsletter',
            'ui_subscribe_title' => 'Newsletter',
            'ui_subscribe_desc' => 'Get tech pulses and coding insights delivered directly to your inbox every Monday.',
            'ui_subscribe_placeholder' => 'you@email.com',
            'ui_subscribe_button' => 'Subscribe Now',
            'ui_subscribe_sending' => 'Sending...',
            'ui_subscribe_sent' => 'We sent a confirmation link to your email. Please check your inbox!',
            'ui_subscribe_already_confirmed' => 'This email is already subscribed to our newsletter.',
            'ui_subscribe_send_cancel' => 'Send unsubscribe link',
            'ui_subscribe_cancel_sent' => 'If the email is registered, an unsubscribe link has been sent.',
            'ui_confirm_title' => 'Confirm Newsletter Subscription',
            'ui_confirm_desc' => 'Complete your details to receive weekly TechPulse editions every Monday.',
            'ui_confirm_name_label' => 'Full Name',
            'ui_confirm_name_placeholder' => 'Your name',
            'ui_confirm_email_label' => 'Email',
            'ui_confirm_github_label' => 'GitHub Username (optional)',
            'ui_confirm_github_placeholder' => 'e.g. octocat',
            'ui_confirm_phone_label' => 'Phone (optional)',
            'ui_confirm_phone_placeholder' => '+1 555-0199',
            'ui_confirm_lang_label' => 'Preferred Language',
            'ui_confirm_submit' => 'Confirm Subscription',
            'ui_confirm_submitting' => 'Confirming...',
            'ui_confirm_already_confirmed_title' => 'Subscription already confirmed!',
            'ui_confirm_already_confirmed_desc' => 'Your email is active and you will receive the next editions.',
            'ui_confirm_expired_title' => 'Confirmation link expired',
            'ui_confirm_expired_desc' => 'This link has expired (> 24 hours). Please subscribe again on the homepage.',
            'ui_confirm_not_found_title' => 'Invalid confirmation link',
            'ui_confirm_not_found_desc' => 'We could not find a confirmation request for this link.',
            'ui_cancel_title' => 'Unsubscribe from Newsletter',
            'ui_cancel_confirm_question' => 'Are you sure you want to stop receiving TechPulse weekly newsletter?',
            'ui_cancel_submit' => 'Unsubscribe',
            'ui_cancel_submitting' => 'Unsubscribing...',
            'ui_cancel_done_title' => 'Successfully unsubscribed',
            'ui_cancel_done_desc' => 'You will no longer receive TechPulse newsletter emails.',
            'ui_cancel_already_title' => 'Already unsubscribed',
            'ui_cancel_already_desc' => 'This email was already unsubscribed from our list.',
            'ui_cancel_not_found_title' => 'Invalid link',
            'ui_cancel_not_found_desc' => 'Subscriber not found.',
            'ui_back_to_home' => 'Back to Homepage',
        ],
        'pt_pt' => [
            'mail_confirm_subject' => 'Confirme a sua subscrição na Newsletter TechPulse',
            'mail_confirm_intro' => 'Obrigado pelo interesse na Newsletter semanal do TechPulse! Clique no botão abaixo para confirmar o seu e-mail.',
            'mail_confirm_button' => 'Confirmar Subscrição',
            'mail_confirm_note_24h' => 'Esta ligação expira em 24 horas. Se não solicitou esta subscrição, ignore esta mensagem.',
            'mail_cancel_subject' => 'Ligação para cancelar a subscrição na Newsletter TechPulse',
            'mail_cancel_intro' => 'Recebemos um pedido para cancelar o envio da Newsletter TechPulse. Clique no botão abaixo para confirmar.',
            'mail_cancel_button' => 'Cancelar Subscrição',
            'mail_newsletter_subject' => 'TechPulse #{edition} — O Pulsar da Tecnologia',
            'mail_newsletter_unsubscribe' => 'Cancelar subscrição desta newsletter',
            'ui_subscribe_title' => 'Boletim Informativo',
            'ui_subscribe_desc' => 'Receba novidades tecnológicas e dicas de programação no seu e-mail todas as segundas-feiras.',
            'ui_subscribe_placeholder' => 'o.seu@email.com',
            'ui_subscribe_button' => 'Subscrever Agora',
            'ui_subscribe_sending' => 'A enviar...',
            'ui_subscribe_sent' => 'Enviámos um e-mail com a ligação de confirmação. Verifique a sua caixa de entrada!',
            'ui_subscribe_already_confirmed' => 'Este e-mail já se encontra subscrito.',
            'ui_subscribe_send_cancel' => 'Enviar ligação de cancelamento',
            'ui_subscribe_cancel_sent' => 'Se o e-mail estiver registado, enviámos a ligação de cancelamento.',
            'ui_confirm_title' => 'Confirmar Subscrição na Newsletter',
            'ui_confirm_desc' => 'Complete os seus dados para receber as edições semanais do TechPulse.',
            'ui_confirm_name_label' => 'Nome Completo',
            'ui_confirm_name_placeholder' => 'O seu nome',
            'ui_confirm_email_label' => 'E-mail',
            'ui_confirm_github_label' => 'Utilizador do GitHub (opcional)',
            'ui_confirm_github_placeholder' => 'ex: octocat',
            'ui_confirm_phone_label' => 'Telefone (opcional)',
            'ui_confirm_phone_placeholder' => '+351 912 345 678',
            'ui_confirm_lang_label' => 'Idioma de Preferência',
            'ui_confirm_submit' => 'Confirmar Subscrição',
            'ui_confirm_submitting' => 'A confirmar...',
            'ui_confirm_already_confirmed_title' => 'Subscrição já confirmada!',
            'ui_confirm_already_confirmed_desc' => 'O seu e-mail está ativo e receberá as próximas edições.',
            'ui_confirm_expired_title' => 'Ligação expirada',
            'ui_confirm_expired_desc' => 'Esta ligação expirou (> 24 horas). Por favor subscreva novamente.',
            'ui_confirm_not_found_title' => 'Ligação inválida',
            'ui_confirm_not_found_desc' => 'Não encontrámos o pedido de confirmação.',
            'ui_cancel_title' => 'Cancelar Subscrição',
            'ui_cancel_confirm_question' => 'Tem a certeza de que deseja parar de receber a newsletter do TechPulse?',
            'ui_cancel_submit' => 'Cancelar Subscrição',
            'ui_cancel_submitting' => 'A cancelar...',
            'ui_cancel_done_title' => 'Subscrição cancelada',
            'ui_cancel_done_desc' => 'Não receberá mais os e-mails da newsletter.',
            'ui_cancel_already_title' => 'Já cancelada',
            'ui_cancel_already_desc' => 'Este e-mail já estava cancelado.',
            'ui_cancel_not_found_title' => 'Ligação inválida',
            'ui_cancel_not_found_desc' => 'Subscritor não encontrado.',
            'ui_back_to_home' => 'Voltar à Página Principal',
        ],
        'es' => [
            'mail_confirm_subject' => 'Confirma tu suscripción al Boletín TechPulse',
            'mail_confirm_intro' => '¡Gracias por suscribirte al boletín semanal de TechPulse! Haz clic en el botón de abajo para confirmar tu correo.',
            'mail_confirm_button' => 'Confirmar Suscripción',
            'mail_confirm_note_24h' => 'Este enlace caduca en 24 horas. Si no solicitaste esto, puedes ignorarlo.',
            'mail_cancel_subject' => 'Enlace para cancelar suscripción a TechPulse',
            'mail_cancel_intro' => 'Recibimos una solicitud para cancelar tu suscripción a TechPulse. Haz clic para confirmar.',
            'mail_cancel_button' => 'Cancelar Suscripción',
            'mail_newsletter_subject' => 'TechPulse #{edition} — El Pulso de la Tecnología',
            'mail_newsletter_unsubscribe' => 'Cancelar suscripción a este boletín',
            'ui_subscribe_title' => 'Boletín Informativo',
            'ui_subscribe_desc' => 'Recibe las últimas noticias y consejos técnicos todos los lunes en tu correo.',
            'ui_subscribe_placeholder' => 'tu@correo.com',
            'ui_subscribe_button' => 'Suscribirme',
            'ui_subscribe_sending' => 'Enviando...',
            'ui_subscribe_sent' => '¡Te enviamos un enlace de confirmación! Revisa tu bandeja de entrada.',
            'ui_subscribe_already_confirmed' => 'Este correo ya está suscrito.',
            'ui_subscribe_send_cancel' => 'Enviar enlace de cancelación',
            'ui_subscribe_cancel_sent' => 'Si el correo está registrado, enviamos el enlace de cancelación.',
            'ui_confirm_title' => 'Confirmar Suscripción al Boletín',
            'ui_confirm_desc' => 'Completa tus datos para recibir TechPulse cada lunes.',
            'ui_confirm_name_label' => 'Nombre Completo',
            'ui_confirm_name_placeholder' => 'Tu nombre',
            'ui_confirm_email_label' => 'Correo Electrónico',
            'ui_confirm_github_label' => 'Usuario de GitHub (opcional)',
            'ui_confirm_github_placeholder' => 'ej: octocat',
            'ui_confirm_phone_label' => 'Teléfono (opcional)',
            'ui_confirm_phone_placeholder' => '+34 600 000 000',
            'ui_confirm_lang_label' => 'Idioma de Preferencia',
            'ui_confirm_submit' => 'Confirmar Suscripción',
            'ui_confirm_submitting' => 'Confirmando...',
            'ui_confirm_already_confirmed_title' => '¡Suscripción ya confirmada!',
            'ui_confirm_already_confirmed_desc' => 'Tu correo está activo para las próximas ediciones.',
            'ui_confirm_expired_title' => 'Enlace expirado',
            'ui_confirm_expired_desc' => 'Este enlace caducó (> 24h). Por favor solicítalo de nuevo.',
            'ui_confirm_not_found_title' => 'Enlace no válido',
            'ui_confirm_not_found_desc' => 'No encontramos la solicitud de confirmación.',
            'ui_cancel_title' => 'Cancelar Suscripción',
            'ui_cancel_confirm_question' => '¿Estás seguro de que deseas dejar de recibir el boletín TechPulse?',
            'ui_cancel_submit' => 'Cancelar Suscripción',
            'ui_cancel_submitting' => 'Cancelando...',
            'ui_cancel_done_title' => 'Suscripción cancelada',
            'ui_cancel_done_desc' => 'Ya no recibirás correos de TechPulse.',
            'ui_cancel_already_title' => 'Ya cancelado',
            'ui_cancel_already_desc' => 'Este correo ya estaba cancelado.',
            'ui_cancel_not_found_title' => 'Enlace no válido',
            'ui_cancel_not_found_desc' => 'Suscriptor no encontrado.',
            'ui_back_to_home' => 'Volver a la Página Principal',
        ],
        'fr' => [
            'mail_confirm_subject' => 'Confirmez votre inscription à la Newsletter TechPulse',
            'mail_confirm_intro' => 'Merci pour votre intérêt pour la newsletter TechPulse ! Cliquez sur le bouton ci-dessous pour confirmer votre email.',
            'mail_confirm_button' => 'Confirmer l\'inscription',
            'mail_confirm_note_24h' => 'Ce lien expire dans 24 heures. Si vous n\'avez pas demandé cela, ignorez ce message.',
            'mail_cancel_subject' => 'Lien de désinscription de la Newsletter TechPulse',
            'mail_cancel_intro' => 'Nous avons reçu une demande de désinscription de la Newsletter TechPulse. Cliquez pour confirmer.',
            'mail_cancel_button' => 'Se désinscrire',
            'mail_newsletter_subject' => 'TechPulse #{edition} — Le Pouls de la Technologie',
            'mail_newsletter_unsubscribe' => 'Se désinscrire de cette newsletter',
            'ui_subscribe_title' => 'Bulletin d\'information',
            'ui_subscribe_desc' => 'Recevez l\'actualité tech et les astuces de développement chaque lundi.',
            'ui_subscribe_placeholder' => 'votre@email.com',
            'ui_subscribe_button' => 'S\'abonner',
            'ui_subscribe_sending' => 'Envoi...',
            'ui_subscribe_sent' => 'Un email de confirmation vous a été envoyé. Vérifiez votre boîte de réception !',
            'ui_subscribe_already_confirmed' => 'Cet email est déjà inscrit.',
            'ui_subscribe_send_cancel' => 'Envoyer le lien de désinscription',
            'ui_subscribe_cancel_sent' => 'Si l\'email est enregistré, le lien a été envoyé.',
            'ui_confirm_title' => 'Confirmer l\'abonnement à la Newsletter',
            'ui_confirm_desc' => 'Complétez vos coordonnées pour recevoir TechPulse chaque lundi.',
            'ui_confirm_name_label' => 'Nom Complet',
            'ui_confirm_name_placeholder' => 'Votre nom',
            'ui_confirm_email_label' => 'Email',
            'ui_confirm_github_label' => 'Nom d\'utilisateur GitHub (optionnel)',
            'ui_confirm_github_placeholder' => 'ex: octocat',
            'ui_confirm_phone_label' => 'Téléphone (optionnel)',
            'ui_confirm_phone_placeholder' => '+33 6 00 00 00 00',
            'ui_confirm_lang_label' => 'Langue Préférée',
            'ui_confirm_submit' => 'Confirmer l\'abonnement',
            'ui_confirm_submitting' => 'Confirmation...',
            'ui_confirm_already_confirmed_title' => 'Abonnement déjà confirmé !',
            'ui_confirm_already_confirmed_desc' => 'Votre email est actif et recevra les prochaines éditions.',
            'ui_confirm_expired_title' => 'Lien expiré',
            'ui_confirm_expired_desc' => 'Ce lien a expiré (> 24h). Veuillez vous réinscrire.',
            'ui_confirm_not_found_title' => 'Lien invalide',
            'ui_confirm_not_found_desc' => 'Demande de confirmation non trouvée.',
            'ui_cancel_title' => 'Se désinscrire',
            'ui_cancel_confirm_question' => 'Êtes-vous sûr de vouloir arrêter de recevoir la newsletter ?',
            'ui_cancel_submit' => 'Se désinscrire',
            'ui_cancel_submitting' => 'Désinscription...',
            'ui_cancel_done_title' => 'Désinscription réussie',
            'ui_cancel_done_desc' => 'Vous ne recevrez plus nos newsletters.',
            'ui_cancel_already_title' => 'Déjà désinscrit',
            'ui_cancel_already_desc' => 'Cet email était déjà désinscrit.',
            'ui_cancel_not_found_title' => 'Lien invalide',
            'ui_cancel_not_found_desc' => 'Abonné non trouvé.',
            'ui_back_to_home' => 'Retour à l\'accueil',
        ],
    ];

    /**
     * Retorna todas as strings para o idioma especificado com fallback para 'pt_br'.
     *
     * @return array<string, string>
     */
    public static function for(?string $lang): array
    {
        $lang = self::normalize($lang);

        return self::STRINGS[$lang] ?? self::STRINGS['pt_br'];
    }

    /**
     * Retorna uma string específica com substituição de variáveis.
     *
     * @param  array<string, string>  $replace
     */
    public static function get(?string $lang, string $key, array $replace = []): string
    {
        $strings = self::for($lang);
        $value = $strings[$key] ?? self::STRINGS['pt_br'][$key] ?? $key;

        if (! empty($replace)) {
            $value = strtr($value, $replace);
        }

        return $value;
    }

    /**
     * Normaliza a chave do idioma.
     */
    public static function normalize(?string $lang): string
    {
        if (! $lang) {
            return 'pt_br';
        }

        $cleaned = strtolower(str_replace('-', '_', trim($lang)));

        return in_array($cleaned, self::LANGS, true) ? $cleaned : 'pt_br';
    }
}


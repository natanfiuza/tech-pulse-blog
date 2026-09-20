<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ strtr($strings['mail_newsletter_subject'] ?? 'TechPulse #{edition}', ['{edition}' => $edition]) }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#1e293b;">
    <div style="background-color:#f1f5f9;padding:32px 12px;min-height:100vh;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td align="center">
                    <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background-color:#ffffff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.05);">
                        <!-- Header -->
                        <tr>
                            <td style="background-color:#000b2b;padding:24px 28px;border-bottom:3px solid #2563eb;">
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>
                                            <div style="color:#ffffff;font-size:22px;font-weight:900;letter-spacing:-0.5px;text-transform:uppercase;">
                                                Tech<span style="color:#38bdf8;">Pulse</span>
                                            </div>
                                            <div style="color:#94a3b8;font-size:11px;letter-spacing:1.5px;text-transform:uppercase;margin-top:4px;font-weight:600;">
                                                O Pulsar da Tecnologia
                                            </div>
                                        </td>
                                        <td align="right" style="vertical-align:bottom;">
                                            <span style="background-color:#1e3a8a;color:#bfdbfe;font-size:11px;font-weight:700;padding:4px 10px;border-radius:4px;text-transform:uppercase;letter-spacing:1px;">
                                                Edição #{{ $edition }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- Body Content (Markdown rendered to HTML) -->
                        <tr>
                            <td style="padding:32px 28px;">
                                <div style="font-size:15px;line-height:1.7;color:#334155;">
                                    {!! $htmlBody !!}
                                </div>
                            </td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                            <td style="background-color:#f8fafc;padding:24px 28px;border-top:1px solid #e2e8f0;font-size:12px;color:#64748b;line-height:1.6;text-align:center;">
                                <p style="margin:0 0 8px;">
                                    Você recebeu este e-mail porque está inscrito na Newsletter do <strong>TechPulse</strong>.
                                </p>
                                <p style="margin:0;">
                                    <a href="{{ $unsubscribeUrl }}" style="color:#2563eb;text-decoration:underline;">
                                        {{ $strings['mail_newsletter_unsubscribe'] ?? 'Cancelar recebimento desta newsletter' }}
                                    </a>
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>


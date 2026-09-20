<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $strings['mail_confirm_subject'] ?? 'Confirmação de Inscrição' }}</title>
</head>
<body style="margin:0;padding:0;background-color:#0f172a;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
    <div style="background-color:#0f172a;padding:32px 16px;min-height:100vh;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td align="center">
                    <table role="presentation" width="500" cellpadding="0" cellspacing="0" style="max-width:500px;width:100%;background-color:#1e293b;border:1px solid #334155;border-radius:12px;overflow:hidden;box-shadow:0 10px 25px rgba(0,0,0,0.3);">
                        <tr>
                            <td style="background-color:#000b2b;padding:24px;border-bottom:1px solid #1e3a8a;">
                                <div style="color:#ffffff;font-size:20px;font-weight:900;letter-spacing:-0.5px;text-transform:uppercase;">
                                    Tech<span style="color:#38bdf8;">Pulse</span>
                                </div>
                                <div style="color:#94a3b8;font-size:11px;letter-spacing:1.5px;text-transform:uppercase;margin-top:4px;font-weight:600;">
                                    O Pulsar da Tecnologia
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:32px 24px;">
                                <h1 style="font-size:20px;color:#f8fafc;margin:0 0 16px;font-weight:800;">
                                    {{ $strings['mail_confirm_subject'] ?? 'Confirme sua inscrição' }}
                                </h1>
                                <p style="font-size:15px;color:#cbd5e1;line-height:1.6;margin:0 0 24px;">
                                    {{ $strings['mail_confirm_intro'] ?? 'Obrigado pelo interesse na Newsletter do TechPulse! Clique no botão abaixo para confirmar seu e-mail e completar sua inscrição.' }}
                                </p>
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td align="center" style="padding:12px 0 28px;">
                                            <a href="{{ $url }}" style="display:inline-block;background-color:#2563eb;color:#ffffff;padding:14px 28px;border-radius:8px;text-decoration:none;font-size:15px;font-weight:700;letter-spacing:0.3px;">
                                                {{ $strings['mail_confirm_button'] ?? 'Confirmar Inscrição' }}
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                                <p style="font-size:12px;color:#64748b;line-height:1.5;margin:0;border-top:1px solid #334155;padding-top:16px;">
                                    {{ $strings['mail_confirm_note_24h'] ?? 'Este link expira em 24 horas. Se você não solicitou esta inscrição, pode ignorar este e-mail.' }}
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


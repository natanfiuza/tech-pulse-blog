<?php

/*
|--------------------------------------------------------------------------
| Medição (Google Analytics GA4)
|--------------------------------------------------------------------------
|
| A tag do GA4 só entra na página depois do consentimento explícito no banner
| e exclusivamente no cliente (ADR 0003). Sem ID configurado nada é injetado,
| mesmo com aceite: é assim que o ambiente de testes e o local ficam isolados.
|
*/

return [

    // ID de medição do GA4 (G-XXXXXXXXXX). Vazio desliga a medição.
    'ga4_id' => env('ANALYTICS_GA4_ID', ''),

    // Nome do cookie da decisão do visitante ('granted' ou 'denied').
    'cookie' => 'consent',

    // Validade do cookie, em dias. Sem cookie o banner é exibido novamente.
    'cookie_days' => (int) env('ANALYTICS_CONSENT_DAYS', 180),

];

# 0003: Arquitetura e Implementação da Medição com Google Analytics (GA4) sob Consentimento

Status: Aceito  
Data: 2026-10-20  
Relacionados: ADR-038 (DevTools)

## Contexto

A coleta de métricas de acesso às publicações do Tech Pulse Blog (páginas mais lidas, temas mais acessados e comportamento de leitura) é importante para orientar a produção editorial. Contudo, essa medição deve operar sob estrito respeito à privacidade dos leitores (LGPD e GDPR) e em consonância com a stack do blog (Laravel 10, Inertia.js 2, Vue 3 e Tailwind CSS v3 "Midnight Pulse").

O padrão promovido pelo Google de injetar tags estáticas `<script src="https://www.googletagmanager.com/gtag/js?id=G-...">` diretamente no `<head>` do HTML (`resources/views/app.blade.php`) foi rejeitado por motivos fundamentais:

1. **Violação do Princípio de Zero Requisições Antes do Consentimento**: A simples inclusão da tag no HTML forçaria o navegador a disparar chamadas HTTP para os servidores do Google antes mesmo da interação do visitante com o banner de cookies. Mesmo o Consent Mode v2 em modo `denied` emite pings com parâmetros sem cookies, ferindo o compromisso de privacidade.
2. **Navegação SPA com Inertia.js**: A navegação entre posts e categorias se dá sem recarregamento de página (`history.pushState`). A medição precisa acompanhar o ciclo de vida do cliente sem duplicar tags no DOM.
3. **Contrato da Fila do `gtag.js` (`arguments`, nunca Array)**: Conforme documentado no incidente do ADR-038 do DevTools, o motor interno do `gtag.js` só despacha comandos quando o item empurrado em `window.dataLayer` é um objeto `arguments` nativo (`[object Arguments]`). Enfileirar Arrays comuns (`dataLayer.push([...])`) é tratado silenciosamente pelo despachante do Google como campos de modelo, ignorando o comando e resultando em zero hits sem nenhum erro no console.

## Decisão

Adotou-se a arquitetura de medição sob demanda orientada a consentimento do cliente com resolução de estado no servidor.

### 1. Resolução do Estado de Consentimento no Servidor (Zero-Flash)
- A decisão do visitante é armazenada no cookie `consent` (validade de 180 dias, configurada em `config/analytics.php` via `ANALYTICS_CONSENT_DAYS`).
- Os valores aceitos são unicamente `granted` ou `denied` (`App\Support\Consent`).
- Sem cookie, o estado é `null`, exibindo o banner.
- Se o cookie contiver valor corrompido ou inesperado, `App\Support\Consent::isCorrupt()` detecta e o middleware `App\Http\Middleware\HandleConsent` remove o cookie inválido no cabeçalho `Set-Cookie`.
- O cookie é declarado em `App\Http\Middleware\EncryptCookies::$except` para tráfego legível entre cliente e servidor.
- `App\Http\Middleware\HandleInertiaRequests` compartilha a prop global:
  ```php
  'analytics' => [
      'ga4Id' => Analytics::ga4Id(),
      'consent' => Consent::fromRequest($request),
      'cookie' => Consent::cookie(),
      'days' => Consent::days(),
  ],
  ```

### 2. Zero Rastreamento no Servidor
- `resources/views/app.blade.php` não contém nenhum script de analytics ou menção a `gtag` e `dataLayer`.

### 3. Injeção Reativa no Cliente via Componente Invisível (`AnalyticsScript.vue`)
- Executa exclusivamente no cliente durante o ciclo de vida do Vue (`onMounted` e `watch(consentimento)`).
- A injeção só ocorre se `consentimento === 'granted'` e se `analytics.ga4Id` for uma string não vazia.

### 4. Inicialização com Consent Mode v2 em Estado Negado
- Antes da inclusão do script, enfileira:
  ```javascript
  gtag('consent', 'default', {
      ad_storage: 'denied',
      ad_user_data: 'denied',
      ad_personalization: 'denied',
      analytics_storage: 'denied',
  });
  ```
- O consentimento do usuário libera estritamente `analytics_storage: 'granted'`.

### 5. Blindagem Ativa contra Publicidade (`SEM_ANUNCIOS`)
- Sinais publicitários são terminantemente desligados na configuração:
  ```javascript
  gtag('config', id, {
      allow_google_signals: false,
      allow_ad_personalization_signals: false,
  });
  ```

### 6. Contrato da Fila `window.dataLayer`: `arguments`, Nunca Array
- A função shim utilitária `gtag` empurra rigorosamente o objeto `arguments`:
  ```javascript
  function gtag() {
      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push(arguments);
  }
  ```

### 7. Idempotência e Revogação Dinâmica
- O script é injetado com atributo `data-analytics="G-XXXXXXXXXX"`.
- Aceitar novamente reutiliza a tag existente apenas emitindo `gtag('consent', 'update', { analytics_storage: 'granted' })`.
- Recusar após ter aceito emite `gtag('consent', 'update', { analytics_storage: 'denied' })`.

### 8. Delimitação de Áreas e Rodapé
- O banner e o script são montados na raiz da aplicação em `resources/js/app.js`, ativos para páginas públicas e do leitor, e inibidos quando a tela pertence ao painel administrativo (`!page.component.startsWith('Admin/')`).
- O rodapé (`Footer.vue`) inclui o botão "Cookies" para permitir ao leitor revisar ou revogar seu consentimento a qualquer momento.

### 9. Isolamento por Ambiente
- `ANALYTICS_GA4_ID` define o ID. Em ambiente local e testes, permanece vazio, resultando em `Analytics::ga4Id() === null`, desativando qualquer injeção.

## Consequências

- **Conformidade Legal**: Zero chamadas HTTP a terceiros antes da escolha explícita do visitante.
- **Experiência do Usuário**: Zero layout shift / sem banner piscando para leitores que já responderam.
- **Isolamento Editorial**: A área `/admin` não afeta as métricas de audiência do blog.
- **Robustez**: Evita a falha silenciosa de envio de eventos do GA4 decorrente de arrays na fila.

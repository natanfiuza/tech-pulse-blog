# Guia - Google Analytics (GA4) no Tech Pulse Blog

Como ligar a medição de audiência do blog. O carregamento já existe no código (ADR 0003): para ativar em produção, basta criar o fluxo de dados na console do Google Analytics e preencher a variável `ANALYTICS_GA4_ID` no `.env`. Não há código adicional para escrever.

## Três Regras Absolutas

1. **Apenas Produção**: O ID do stream vive no `.env` do servidor de produção. Em ambiente de desenvolvimento local, testes automatizados e CI, a variável permanece vazia — ID vazio significa medição desligada, mesmo se o visitante clicar em "Aceitar" no banner.
2. **Zero Requisições Antes do Consentimento**: A tag do Google só entra no cliente após o visitante aceitar a medição no banner. Recusar significa **zero requisições** aos servidores do Google.
3. **Banner como Compromisso**: O banner declara que apenas métricas anônimas de páginas vistas são medidas, sem anúncios. Toda a configuração da console deve respeitar estritamente essa promessa.

---

## 1. Criar a Propriedade e o Fluxo de Dados (Console do Google)

1. Acesse [Google Analytics](https://analytics.google.com).
2. Vá em **Admin** (engrenagem no canto inferior esquerdo) -> **Criar** -> **Propriedade**.
3. Defina o nome (ex.: `Tech Pulse Blog`), fuso horário `(GMT-03:00) São Paulo` e moeda `Real brasileiro (BRL)`.
4. Escolha a plataforma **Web**, informe a URL `https://tech-pulse.natanfiuza.dev.br` e o nome do fluxo (ex.: `Blog Web`).
5. Copie o **Measurement ID** (`G-XXXXXXXXXX`) exibido no canto superior direito do fluxo de dados criado.

> [!WARNING]
> **Não cole o snippet "Google tag (gtag.js)" sugerido pela console no `app.blade.php`.**  
> A tag deste projeto é injetada reativamente no cliente via JavaScript somente após o consentimento explícito. Colocar o snippet no Blade violaria a LGPD e o ADR 0003.

---

## 2. Configurar o Enhanced Measurement na Console

Na tela de detalhes do fluxo de dados:
1. Em **Métricas Otimizadas (Enhanced Measurement)**, clique no ícone de engrenagem.
2. **Mantenha ativado apenas "Visualizações de página"** (com a opção avançada *"Mudanças de página com base em eventos do histórico do navegador"* marcada). É essa opção que mede a navegação SPA do Inertia.js.
3. **Desative os demais 5 interruptores**:
   - Rolagens
   - Cliques de saída
   - Pesquisa no site
   - Engajamento com vídeos
   - Downloads de arquivos
4. Salve as alterações.

### O que NÃO ligar na Console

| Configuração | Caminho na Console | Motivo |
|---|---|---|
| **Google Signals** | Admin -> Coleta e modificação de dados -> Google signals | Cruzaria navegação com contas Google; o blog promete ausência de publicidade |
| **Personalização de anúncios** | Admin -> Coleta e modificação de dados | Não há finalidade de anúncios no blog |
| **User-ID** | Admin -> Coleta de dados | O blog não envia identificadores de usuários logados para o GA |
| **Retenção de dados** | Admin -> Retenção de dados | Deixar no padrão seguro de **14 meses** |

---

## 3. Ligar no Servidor de Produção

No arquivo `.env` do servidor de produção:

```dotenv
ANALYTICS_GA4_ID=G-XXXXXXXXXX
```

Em seguida, limpe o cache das configurações:

```bash
php artisan optimize:clear && php artisan optimize
```

A validade do cookie de consentimento padrão é de 180 dias (`ANALYTICS_CONSENT_DAYS=180`).

---

## 4. Conferir a Validação

1. Abra o blog em uma **janela anônima** (sem cookie de consentimento).
2. Abra o DevTools (**F12**) -> aba **Network** e filtre por `googletagmanager`:
   - Nenhuma requisição deve existir.
   - Clique em **Recusar**: deve continuar com zero requisições.
   - Abra as preferências pelo link **Cookies** no rodapé e clique em **Aceitar**:
     - Uma única requisição para `googletagmanager.com/gtag/js?id=G-...` deve ser disparada (status 200).
3. Na aba **Network**, filtre por `collect`:
   - Verifique o envio de uma requisição para `/g/collect` com status **204** (hit de visualização de página).
4. Na aba **Application -> Cookies**:
   - `consent=granted` deve estar presente, acompanhado dos cookies `_ga` e `_ga_<id>`.
5. No Google Analytics -> **Relatórios -> Tempo Real**:
   - A visita em tempo real deverá constar após o aceite.
6. No código-fonte da página (Ctrl+U):
   - Confirme que nenhuma tag `googletagmanager` ou `gtag(` está embutida no HTML entregue pelo servidor.

---

## 5. Estrutura de Arquivos no Código

| Finalidade | Arquivo |
|---|---|
| Configuração de ID e validade do cookie | `config/analytics.php` |
| Suporte e normalização do ID | `app/Support/Analytics.php` |
| Gerenciamento e validação de consentimento | `app/Support/Consent.php` |
| Middleware de expiração de cookie corrompido | `app/Http/Middleware/HandleConsent.php` |
| Compartilhamento com Inertia | `app/Http/Middleware/HandleInertiaRequests.php` |
| Módulo utilitário de injeção e Consent Mode v2 | `resources/js/lib/analytics.js` |
| Utilitário de leitura/escrita de cookies | `resources/js/lib/cookies.js` |
| Composable reativo de consentimento | `resources/js/Composables/use_consent.js` |
| Componente de injeção invisível no cliente | `resources/js/Components/AnalyticsScript.vue` |
| Componente visual do banner | `resources/js/Components/ConsentBanner.vue` |
| Botão de revisão no rodapé | `resources/js/Components/Footer.vue` |
| Ponto de montagem global | `resources/js/app.js` |

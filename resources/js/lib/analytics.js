import { definir_cookie } from '@/lib/cookies';

/**
 * Medição com Google Analytics (GA4) sob consentimento (ADR 0003).
 *
 * A tag só entra no <head> após o consentimento explícito e exclusivamente no cliente.
 * Nem o HTML do servidor nem o bundle SSR contêm referências a gtag ou dataLayer.
 */

const ATRIBUTO_TAG = 'data-analytics';
const GTAG_SRC = 'https://www.googletagmanager.com/gtag/js';

/**
 * Google Consent Mode v2 com negação de todas as permissões por padrão.
 */
const PADRAO_NEGADO = {
    ad_storage: 'denied',
    ad_user_data: 'denied',
    ad_personalization: 'denied',
    analytics_storage: 'denied',
};

/**
 * Opções de configuração: blindagem contra publicidade e cruzamento com contas Google.
 */
const SEM_ANUNCIOS = {
    allow_google_signals: false,
    allow_ad_personalization_signals: false,
};

/**
 * Enfileira um comando para o despachante do gtag.
 *
 * ATENÇÃO CRÍTICA (ADR 0003): O despachante interno minificado do Google só
 * processa comandos quando o item inserido em window.dataLayer é um objeto
 * arguments nativo ([object Arguments]). Inserir Arrays simples (como rest params)
 * faz com que os comandos sejam interpretados silenciosamente como campos de modelo,
 * resultando em zero eventos despachados para o GA4 sem nenhum aviso no console.
 */
function gtag() {
    window.dataLayer = window.dataLayer || [];
    // eslint-disable-next-line prefer-rest-params
    window.dataLayer.push(arguments);
}

/**
 * Grava a decisão do visitante no cookie correspondente.
 *
 * @param {string} cookie
 * @param {string} decisao
 * @param {number} dias
 */
export function gravar_consentimento(cookie, decisao, dias) {
    definir_cookie(cookie, decisao, dias);
}

/**
 * Injeta a tag do GA4 de forma idempotente.
 *
 * @param {string} id
 */
export function carregar_analytics(id) {
    if (typeof document === 'undefined' || !id) {
        return;
    }

    if (document.querySelector(`script[${ATRIBUTO_TAG}]`) !== null) {
        gtag('consent', 'update', { analytics_storage: 'granted' });
        return;
    }

    gtag('consent', 'default', PADRAO_NEGADO);
    gtag('consent', 'update', { analytics_storage: 'granted' });
    gtag('js', new Date());
    gtag('config', id, SEM_ANUNCIOS);

    const script = document.createElement('script');
    script.async = true;
    script.src = `${GTAG_SRC}?id=${encodeURIComponent(id)}`;
    script.setAttribute(ATRIBUTO_TAG, id);
    document.head.append(script);
}

/**
 * Revoga dinamicamente a medição via Consent Mode v2 caso o script já esteja em memória.
 */
export function negar_analytics() {
    if (
        typeof document === 'undefined' ||
        document.querySelector(`script[${ATRIBUTO_TAG}]`) === null
    ) {
        return;
    }

    gtag('consent', 'update', { analytics_storage: 'denied' });
}

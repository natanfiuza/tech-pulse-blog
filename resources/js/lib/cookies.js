/**
 * Manipulação de cookies no navegador (respeitando SameSite=Lax).
 */

const DIAS_PADRAO = 180;

/**
 * Grava um cookie no navegador com expiração em dias e SameSite=Lax.
 *
 * @param {string} nome
 * @param {string} valor
 * @param {number} dias
 */
export function definir_cookie(nome, valor, dias = DIAS_PADRAO) {
    if (typeof document === 'undefined') {
        return;
    }

    const max_age = dias * 24 * 60 * 60;
    document.cookie = `${nome}=${encodeURIComponent(valor)};path=/;max-age=${max_age};SameSite=Lax`;
}

/**
 * Lê o valor de um cookie a partir do nome.
 *
 * @param {string} nome
 * @returns {string|null}
 */
export function obter_cookie(nome) {
    if (typeof document === 'undefined') {
        return null;
    }

    const prefixo = `${nome}=`;
    const partes = document.cookie.split(';');

    for (let i = 0; i < partes.length; i++) {
        const item = partes[i].trim();
        if (item.startsWith(prefixo)) {
            return decodeURIComponent(item.slice(prefixo.length));
        }
    }

    return null;
}

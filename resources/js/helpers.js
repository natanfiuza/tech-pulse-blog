/**
 * Calcula o tempo estimado de leitura para um dado texto.
 *
 * Esta função estima o tempo de leitura em minutos, baseando-se em uma
 * velocidade média de leitura de 200 palavras por minuto (PPM).
 * Considera apenas palavras com caracteres alfabéticos, ignorando caracteres
 * especiais e pontuações. Retorna um tempo mínimo de 1 minuto para textos curtos.
 *
 * @param {string} texto - O texto para o qual se deseja estimar o tempo de leitura.
 *
 * @returns {number} O tempo de leitura estimado em minutos, arredondado para o
 *                   número inteiro mais próximo. Retorna 1 como tempo mínimo.
 *
 * @example
 * // Retorna 1 (mínimo)
 * tempoDeLeitura("Olá mundo!");
 *
 * @example
 * // Retorna 3
 * tempoDeLeitura("Lorem ipsum dolor sit amet, consectetur adipiscing elit. ... (aproximadamente 500 palavras)");
 */
export function tempo_leitura(texto) {
    // Define a velocidade média de leitura em palavras por minuto (PPM)
    const ppm = 200;

    // Remove caracteres especiais e converte para minúsculas para uma contagem mais precisa
    const textoLimpo = texto.replace(/[^a-zA-Z\s]/g, "").toLowerCase();

    // Divide o texto em palavras
    const palavras = textoLimpo.split(/\s+/);

    // Conta o número de palavras
    const numeroDePalavras = palavras.length;

    // Calcula o tempo de leitura em minutos
    const tempoDeLeituraEmMinutos = Math.round(numeroDePalavras / ppm);

    // Retorna o tempo de leitura, com um mínimo de 1 minuto
    return Math.max(1, tempoDeLeituraEmMinutos);
}

/**
 * Monta a URL de exibição da imagem de um post.
 *
 * O campo `image` do banco pode vir vazio, como nome de arquivo puro
 * (uuid do post) ou com o prefixo `/storage/images/`; todas as variantes
 * são normalizadas para a rota `post/image/{filename}`, que lê direto
 * do storage sem depender do link simbólico.
 *
 * @param {Object} post - O post com os campos `image` e `uuid`.
 *
 * @returns {string} A URL pública da imagem.
 */
export function url_da_imagem(post) {
    const imagem = post.image || post.uuid;
    if (/^https?:\/\//.test(imagem)) {
        return imagem;
    }
    return "/post/image/" + imagem.split("/").pop();
}

/**
 * Troca a origem das imagens de conteúdo pela origem do navegador.
 *
 * O banco guarda a URL absoluta com o domínio de produção (o app Flutter
 * consome a API e precisa dela), mas o navegador pode estar em outra origem
 * (desenvolvimento, staging). Sem a troca, a página local tentaria carregar as
 * imagens do servidor de produção.
 *
 * Só as URLs do domínio configurado que terminam no caminho das imagens de
 * conteúdo são reescritas — uma imagem externa que por acaso use o mesmo
 * caminho não é tocada.
 *
 * @param {string} texto - Markdown ou HTML contendo as URLs.
 * @param {string} url_publica - Domínio de produção (props.techpulse_url_publica).
 *
 * @returns {string} O mesmo texto, com a origem das imagens de conteúdo normalizada.
 *
 * @example
 * // Em localhost, devolve "/post/content/images/meu-post/<uuid>" com a
 * // origem local no lugar de "https://tech-pulse.natanfiuza.dev.br".
 * normalizar_origem_conteudo(markdown, "https://tech-pulse.natanfiuza.dev.br");
 */
export function normalizar_origem_conteudo(texto, url_publica) {
    if (!texto || !url_publica || typeof window === "undefined") {
        return texto;
    }

    const origem = window.location.origin;
    const origem_publica = String(url_publica).replace(/\/+$/, "");

    // Já estamos na origem de produção: nada a reescrever.
    if (!origem_publica || origem === origem_publica) {
        return texto;
    }

    // split/join com separador string é literal, então o domínio não precisa
    // ser escapado para regex.
    const alvo = origem_publica + "/post/content/images/";
    const substituto = origem + "/post/content/images/";

    return texto.split(alvo).join(substituto);
}

/**
 * Normaliza um texto para busca removendo acentuação e convertendo para minúsculas.
 *
 * @param {string} texto - Texto a ser normalizado.
 * @returns {string} Texto normalizado em minúsculas e sem acentos.
 */
export function normalizar_texto(texto) {
    if (!texto) {
        return "";
    }
    return String(texto)
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .toLowerCase()
        .trim();
}


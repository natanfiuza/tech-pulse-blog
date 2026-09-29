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

/**
 * Analisa uma imagem através de um elemento Canvas para determinar
 * o nível de brilho/luminância da área onde o texto fica sobreposto.
 *
 * @param {HTMLImageElement|string} imagem_origem - Elemento de imagem ou URL.
 * @param {Object} [opcoes] - Opções de análise.
 * @param {number} [opcoes.amostra_inicio_y=0.4] - Posição Y inicial relativa (0 a 1) para a área de amostra.
 *
 * @returns {Promise<{ eh_escura: boolean, luminancia: number, r: number, g: number, b: number }>}
 */
export async function analisar_contraste_imagem(imagem_origem, opcoes = {}) {
    const amostra_inicio_y = opcoes.amostra_inicio_y ?? 0.4;

    return new Promise((resolve) => {
        const fallback = { eh_escura: true, luminancia: 0, r: 0, g: 0, b: 0 };

        if (!imagem_origem || typeof window === "undefined") {
            return resolve(fallback);
        }

        const processar_imagem = (img) => {
            try {
                const canvas = document.createElement("canvas");
                const ctx = canvas.getContext("2d", { willReadFrequently: true });
                if (!ctx) {
                    return resolve(fallback);
                }

                // Redimensiona para uma escala de análise rápida e leve (100x100)
                const largura = 100;
                const altura = 100;
                canvas.width = largura;
                canvas.height = altura;

                ctx.drawImage(img, 0, 0, largura, altura);

                // Analisa a região inferior onde o texto é renderizado
                const inicio_y = Math.floor(altura * amostra_inicio_y);
                const altura_regiao = altura - inicio_y;

                const dados_imagem = ctx.getImageData(0, inicio_y, largura, altura_regiao).data;

                let soma_r = 0;
                let soma_g = 0;
                let soma_b = 0;
                let total_pixels = 0;

                // Lê pixels amostrados para desempenho ideal
                for (let i = 0; i < dados_imagem.length; i += 16) {
                    soma_r += dados_imagem[i];
                    soma_g += dados_imagem[i + 1];
                    soma_b += dados_imagem[i + 2];
                    total_pixels++;
                }

                if (total_pixels === 0) {
                    return resolve(fallback);
                }

                const r = Math.round(soma_r / total_pixels);
                const g = Math.round(soma_g / total_pixels);
                const b = Math.round(soma_b / total_pixels);

                // Fórmula padrão de luminância perceptiva (ITU-R BT.601)
                const luminancia = Math.round(0.299 * r + 0.587 * g + 0.114 * b);
                const eh_escura = luminancia < 128;

                resolve({ eh_escura, luminancia, r, g, b });
            } catch (erro) {
                resolve(fallback);
            }
        };

        if (typeof imagem_origem === "string") {
            const img = new Image();
            img.crossOrigin = "anonymous";
            img.onload = () => processar_imagem(img);
            img.onerror = () => resolve(fallback);
            img.src = imagem_origem;
        } else if (imagem_origem.complete && imagem_origem.naturalWidth > 0) {
            processar_imagem(imagem_origem);
        } else {
            imagem_origem.addEventListener("load", () => processar_imagem(imagem_origem), { once: true });
            imagem_origem.addEventListener("error", () => resolve(fallback), { once: true });
        }
    });
}


/**
 * Analisa o conteúdo de markdown e extrai o título, a data e o conteúdo.
 *
 * A função espera um texto formatado em markdown com metadados específicos
 * demarcados por "-- Title:", "-- Date:" e "-- Content:". O conteúdo deve
 * seguir a linha "-- Content:" e qualquer linha que não comece com "--" será
 * tratada como parte do conteúdo até o final da string.
 *
 * @param {string} text - O texto formatado em markdown para analisar.
 * @returns {{title: string, date: string, content: string}} Um objeto contendo o título,
 * a data e o conteúdo extraídos. Se um campo não for encontrado, ele será omitido
 * do objeto retornado. Se várias linhas de título ou data forem encontradas, a última
 * terá precedência. O espaço em branco inicial e final é removido do título, data e conteúdo.
 * O conteúdo pode conter várias palavras em uma linha e novas linhas são ignoradas.
 *
 * @example
 * // Returns {title: "Meu Título", date: "2023-10-27", content: "Este é o conteúdo do meu post."}
 * parse_markdown_content("-- Title: Meu Título\n-- Date: 2023-10-27\n-- Content:\nEste é o conteúdo\ndo meu post.");
 *
 * @example
 * // Returns {title: "Outro Título", content: "Mais conteúdo aqui."}
 * parse_markdown_content("-- Title: Outro Título\n-- Content:\nMais conteúdo aqui.");
 *
 * @example
 * // Returns {content: "Apenas algum conteúdo."}
 * parse_markdown_content("-- Content:\nApenas algum conteúdo.");
 *
 * @example
 * // Returns {}
 * parse_markdown_content("Sem metadados aqui.");
 */
function parse_markdown_content(text) {
    const lines = text.split("\n");
    const result = {};

    for (const line of lines) {
        if (line.startsWith("-- Title:")) {
            result.title = line.substring("-- Title:".length).trim();
        } else if (line.startsWith("-- Date:")) {
            result.date = line.substring("-- Date:".length).trim();
        } else if (line.startsWith("-- ImageSrc:")) {
            result.image_src = line.substring("-- ImageSrc:".length).trim();
        } else if (line.startsWith("-- ImageAlt:")) {
            result.image_alt = line.substring("-- ImageAlt:".length).trim();
        } else if (line.startsWith("-- Author:")) {
            result.author = line.substring("-- Author:".length).trim();
        } else if (line.startsWith("-- Content:")) {
            result.content = "";
            continue;
        } else if (result.hasOwnProperty("content")) {
            if (result.content === "") {
                result.content += line.trim();
            } else {
                result.content += "\n" + line.trim();
            }
        }
    }

    return result;
}

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
function tempo_leitura(texto) {
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
 * Formata uma data no formato 'YYYY-MM-DD' para o formato por extenso.
 *
 * Exemplo: '2024-12-28' retorna '28 de dezembro de 2024'.
 *
 * @param {string} dataString - A data no formato 'YYYY-MM-DD'.
 * @returns {string} A data formatada por extenso ou 'Data inválida' se a entrada for inválida.
 */
function formatar_data_porextenso(dataString) {
    const meses = [
        "janeiro",
        "fevereiro",
        "março",
        "abril",
        "maio",
        "junho",
        "julho",
        "agosto",
        "setembro",
        "outubro",
        "novembro",
        "dezembro",
    ];

    const partesData = dataString.split("-");
    const ano = parseInt(partesData[0]);
    const mes = parseInt(partesData[1]);
    const dia = parseInt(partesData[2]);

    // Validações simples
    if (
        isNaN(ano) ||
        isNaN(mes) ||
        isNaN(dia) ||
        mes < 1 ||
        mes > 12 ||
        dia < 1 ||
        dia > 31
    ) {
        return "Data inválida";
    }

    const mesPorExtenso = meses[mes - 1];

    return `${dia} de ${mesPorExtenso} de ${ano}`;
}

function load_article(url) {
    fetch(url)
        .then((response) => {
            if (!response.ok) {
                throw new Error(
                    "Erro ao carregar o conteúdo: " + response.status
                );
            }
            return response.text();
        })
        .then((content) => {
            const content_object = parse_markdown_content(content);

            document.title = content_object.title + " " + document.title;
            const article_content = document.getElementById("article-content");
            document.getElementById("article-image").src =
                content_object.image_src;
            document.getElementById("article-image").alt =
                content_object.image_alt;
            document.getElementById("article-title").innerHTML =
                content_object.title;
            document.getElementById("article-author").innerHTML =
                content_object.author;
            document.getElementById("article-date").innerHTML =
                formatar_data_porextenso(content_object.date);
            document.getElementById("reader-time").innerHTML = tempo_leitura(
                content_object.content
            );
            article_content.innerHTML = marked.parse(content_object.content);
        })
        .catch((error) => {
            console.error("Erro:", error);
        });
}

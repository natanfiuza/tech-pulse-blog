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

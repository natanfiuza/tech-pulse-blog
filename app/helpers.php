<?php
/*

    Funções auxiliares

*/

if (! function_exists('criar_slug')) {
    /**
     * Cria um slug a partir de um título, removendo palavras irrelevantes,
     * convertendo para minúsculas, removendo caracteres especiais e espaços,
     * e substituindo espaços por hífens.
     *
     * @param string $titulo O título a ser convertido em slug.
     * @return string O slug gerado a partir do título.
     */
    function criar_slug($titulo)
    {
        // 1. Remover palavras irrelevantes (artigos e preposições)
        $palavras_irrelevantes = ['de', 'do', 'da', 'das', 'o', 'a', 'os', 'as', 'em', 'um', 'uma', 'uns', 'umas', 'para', 'com', 'por'];
        $titulo                = preg_replace('/\b(' . implode('|', $palavras_irrelevantes) . ')\b/i', '', $titulo);

        // 2. Converter para minúsculas
        $titulo = strtolower($titulo);

                                                               // 3. Remover caracteres especiais e espaços em branco
        $titulo = preg_replace('/[^a-z0-9\s-]/', '', $titulo); // Mantém letras, números, espaços e hífens
        $titulo = trim($titulo);                               // Remove espaços em branco no início e no fim
        $titulo = preg_replace('/\s+/', '-', $titulo);         // Substitui espaços por hífens

        // 4. Remover hífens duplicados
        $titulo = preg_replace('/-+/', '-', $titulo);

        return $titulo;
    }

}

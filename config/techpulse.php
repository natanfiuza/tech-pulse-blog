<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Domínio público do blog
    |--------------------------------------------------------------------------
    |
    | Prefixo absoluto gravado nas URLs das imagens de conteúdo. É fixo no
    | domínio de produção de propósito: o app Flutter consome a API e recebe a
    | URL pronta, e o frontend troca a origem pela do navegador na renderização.
    |
    | Não usar config('app.url') aqui — no .env local ele aponta para o
    | ambiente de desenvolvimento e vazaria para dentro do banco.
    |
    */

    'url_publica' => env('TECHPULSE_URL_PUBLICA', 'https://tech-pulse.natanfiuza.dev.br'),

    /*
    |--------------------------------------------------------------------------
    | Pasta das imagens de conteúdo
    |--------------------------------------------------------------------------
    |
    | Caminho relativo à raiz do disco "local" (storage/app). Os arquivos são
    | gravados flat, com nome = uuid da imagem e sem extensão — mesma convenção
    | da imagem de capa. O MIME é resolvido na entrega, não pelo nome.
    |
    */

    'imagens_conteudo' => 'data/post_content_images',

    /*
    |--------------------------------------------------------------------------
    | Pasta das fotos de perfil de usuários
    |--------------------------------------------------------------------------
    |
    | Caminho relativo à raiz do disco "local" (storage/app). Os arquivos são
    | gravados flat, com nome = uuid do usuário e sem extensão.
    |
    */
    'imagens_perfil' => 'data/user_profile_images',

];

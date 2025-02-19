# Configurar o Shiki com spatie/laravel-markdown

Configurar o Shiki com `spatie/laravel-markdown` no Laravel. Essa é uma ótima combinação, pois você terá a facilidade de uso do pacote para renderizar o Markdown e a qualidade superior de highlighting do Shiki.

**Passos:**

1.  **Instalação do `spatie/laravel-markdown` (se ainda não tiver):**

    ```bash
    composer require spatie/laravel-markdown
    ```

2.  **Instalação do Shiki:**

    ```bash
    npm install shiki
    ```

3.  **Publicar a Configuração do `spatie/laravel-markdown`:**

    ```bash
    php artisan vendor:publish --tag=markdown-config
    ```

    Isso criará o arquivo `config/markdown.php`.

4.  **Configurar o `markdown.php`:**

    Abra o arquivo `config/markdown.php` e faça as seguintes alterações:

    ```php
    <?php

    return [
        'code_highlighting' => [
            'enabled' => true, // Habilita o highlighting

            'theme' => 'github-light', // Tema padrão.  MUDE ISSO!

            'shiki' => [  // Configurações do Shiki
                'theme' => 'material-theme-palenight', // Escolha um tema do Shiki!
                //'theme' => 'github-light',
                //Adicione outras configurações do shiki, se precisar

                //Você pode definir vários temas para modos claro/escuro
                // 'themes' => [
                //    'light' => 'github-light',
                //    'dark' => 'github-dark',
                // ],
            ],
        ],

        // ... outras configurações (deixe como estão, por enquanto) ...

        'extensions' => [
            // ... habilite extensões do CommonMark aqui, se precisar ...
            // CommonMark: https://commonmark.thephpleague.com/2.3/extensions/overview/
            // Exemplo:
            // League\CommonMark\Extension\Table\TableExtension::class,
        ],

        // ...
    ];
    ```

      * **`'code_highlighting' => ['enabled' => true,]`:** Certifique-se de que o highlighting esteja habilitado.
      * **`'theme' => 'github-light',`:**  *Mude isso\!*  O tema padrão (`github-light`) é bastante básico.
      * **`'shiki' => ['theme' => 'material-theme-palenight',]`:**  Aqui você configura o Shiki.
          * **`'theme'`:**  *Escolha um tema do Shiki*.  Recomendo fortemente que você use um tema do VS Code, como `'material-theme-palenight'`, `'nord'`, `'dracula'`, ou qualquer outro que você goste.  Veja a lista completa aqui: [https://shiki.style/themes](https://www.google.com/url?sa=E&source=gmail&q=https://shiki.style/themes).
          * Você também pode usar um objeto para definir temas diferentes para modo claro/escuro (veja o exemplo comentado no código).
      * **`extensions`:** O `spatie/laravel-markdown` usa a biblioteca `league/commonmark` para o parsing do Markdown. Aqui você pode habilitar *extensões* do CommonMark, como tabelas, listas de tarefas, etc. Veja a documentação do CommonMark para mais detalhes: [https://commonmark.thephpleague.com/2.3/extensions/overview/](https://www.google.com/url?sa=E&source=gmail&q=https://commonmark.thephpleague.com/2.3/extensions/overview/)

5.  **Usar o `MarkdownRenderer` no Controller:**

    ```php
    // app/Http/Controllers/PostController.php
    use Spatie\LaravelMarkdown\MarkdownRenderer; // Importe a classe

    public function show($slug)
    {
        $post = Post::where('slug', $slug)->with('user')->firstOrFail();

        // Renderiza o Markdown para HTML *no servidor*
        $post->content = app(MarkdownRenderer::class)->toHtml($post->content);

        return Inertia::render('Post', [
            'post' => $post,
        ]);
    }
    ```

      * **`app(MarkdownRenderer::class)->toHtml($post->content)`:**  Isso usa o serviço `MarkdownRenderer` (que o pacote `spatie/laravel-markdown` registra automaticamente) para converter o conteúdo Markdown do post (`$post->content`) em HTML.  O Shiki será usado para o highlighting de código, de acordo com as configurações que você fez no arquivo `config/markdown.php`.

6.  **Remova o highlight do front-end:**

      * Remova todo o código do `onMounted` e `watch` do seu componente Vue (`Post.vue`) que você adicionou anteriormente para tentar fazer o highlighting com o Highlight.js/Shiki no cliente.  Você *não* precisa mais disso, pois o highlighting está sendo feito no servidor.
      * Remova o `import hljs from 'highlight.js';` (e qualquer outra importação relacionada a highlighting no cliente).
      * Remova o `import * as shiki from 'shiki';`.
      * Remova o `ref` para `shikiHighlighter`.
      * Remova a tag `<style>` que importa o CSS do highlight.js.

    Seu componente `Post.vue` agora deve ficar assim (ou muito parecido):

    ```vue
    <template>
      <div class="post-container">
        <Head>
          <title>{{ post.title }}</title>
          <meta name="description" :content="post.excerpt">
        </Head>

        <h1 class="post-title">{{ post.title }}</h1>
        <img :src="`/storage/${post.image}`" :alt="post.title" class="post-image" />

        <div class="post-meta">
          <span>Publicado em: {{ formattedDate }}</span>
          <span>Autor: {{ post.user.name }}</span>
        </div>

        <div class="post-content" v-html="post.content"></div>
      </div>
    </template>

    <script setup>
    import { defineProps, computed } from 'vue';
    import { Head } from '@inertiajs/inertia-vue3';
    import { DateTime } from 'luxon';

    const props = defineProps({
      post: Object,
    });

    const formattedDate = computed(() => {
        if(!props.post.created_at){
            return null
        }
      return DateTime.fromISO(props.post.created_at).toLocaleString(DateTime.DATE_FULL);
    });

    </script>

    <style scoped>
    /* ... seus estilos CSS (sem estilos de highlight) ... */
    </style>
    ```

7.  **Limpe os Caches:**

    ```bash
    php artisan optimize:clear
    php artisan config:clear  # Importante: limpar o cache de configuração!
    ```

    Reinicie os servidores (Vite e Laravel).

8.  **Verifique se a classe `hljs` está sendo adicionada:**

      * Inspecione o HTML gerado de um bloco de código. O `spatie/laravel-markdown`, por padrão, adiciona classes compatíveis com o Highlight.js (mesmo usando o Shiki por baixo dos panos).  Você deverá ver algo como:

        ```html
        <pre><code class="hljs language-php">...</code></pre>

        Se você vir essa classe `hljs`, o highlighting *deveria* estar funcionando, desde que você tenha o CSS do Highlight.js carregado (veja o próximo passo).

        ```

9.  **Adicione o CSS do Highlight.js (ou do tema do Shiki, se necessário):**

      * **Se você escolheu um tema do Shiki que *já é compatível* com o Highlight.js** (como `material-theme-palenight`, `nord`, `dracula` - muitos temas do VS Code são), você *não precisa* fazer mais nada. O `spatie/laravel-markdown` já está gerando classes compatíveis com o Highlight.js, e o CSS desses temas já inclui os estilos necessários.
      * **Se você escolheu um tema do Shiki que *não* é compatível com o Highlight.js** (ou se você quer usar um CSS customizado), você pode:
        1.  **Usar um CSS do Highlight.js:** Adicione o link para um CSS do Highlight.js no seu layout Blade (como você já fez antes):

            ```html
            <link rel="stylesheet" href="[https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github.min.css](https://www.google.com/search?q=https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github.min.css)">

            ```

            Isso *sobrescreverá* os estilos do tema do Shiki (mas o Shiki continuará sendo usado para gerar o HTML).

        2.  **(Avançado) Gerar um CSS a partir do tema do Shiki:** O Shiki tem uma função `codeToThemedTokens` que você pode usar para gerar tokens de cores e, a partir deles, criar um CSS customizado. Isso é mais complexo, mas permite que você tenha controle total sobre a aparência. Veja a documentação do Shiki para mais detalhes.

**Resumo:**

1.  Configure o `spatie/laravel-markdown` para usar o Shiki (arquivo `config/markdown.php`).  Escolha um tema\!
2.  Use `app(MarkdownRenderer::class)->toHtml()` no seu controller.
3.  Remova o highlighting do lado do cliente (Vue).
4.  Limpe os caches.
5.  Verifique se a classe `hljs` está sendo adicionada aos blocos de código.
6.  Se necessário, adicione o CSS do Highlight.js (ou gere um CSS customizado a partir do Shiki).

Com esses passos, você terá o highlighting de código funcionando corretamente, com a qualidade do Shiki e a facilidade de uso do `spatie/laravel-markdown`.

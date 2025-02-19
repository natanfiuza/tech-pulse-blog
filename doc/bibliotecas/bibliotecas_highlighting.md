# Instalando bibliotecas de highlighting para code

Vamos adaptar as opções de highlighting para usar com `marked`. O `marked` tem uma forma um pouco diferente de lidar com o highlighting, mas a ideia geral é a mesma: usar uma biblioteca externa (Prism.js, highlight.js, ou Shiki) para aplicar os estilos.

**Opção 1: Prism.js com Marked**

  * **Instalação do Prism.js:** (Igual à explicação anterior)

      * **Via CDN (no seu layout Blade principal, ou no `<head>` do componente com o `<Head>` do Inertia):**

        ```html
        <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css" rel="stylesheet" />

        <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>

        ```

        Escolha um tema em [https://prismjs.com/\#themes](https://www.google.com/search?q=https://prismjs.com/%23themes).

      * **Via npm (recomendado para projetos maiores):**

        ```bash
        npm install prismjs
        ```

        No seu componente:

        ```vue
        <script setup>
        // ...
        import { onMounted } from 'vue';
        import 'prismjs/themes/prism.css'; // Ou o tema que você escolher
        import Prism from 'prismjs';  // Importe o Prism *depois* do CSS

        onMounted(() => {
            Prism.highlightAll();
        });
        // ...
        </script>

        ```

        E adicione `prismjs` como external no vite config.

        ```js
            //vite.config.js
            export default defineConfig({
                // ...
                build: {
                    rollupOptions: {
                        external: [
                            'prismjs'
                        ]
                    }
                }
            });

        ```

  * **Configurar `marked`:** O `marked` tem uma opção chamada `highlight` que você pode usar para integrar com o Prism.js (ou qualquer outra biblioteca).  Você *não* precisa mais da instância do `MarkdownIt`.

    ```vue
    <script setup>
    import { defineProps, computed } from 'vue';
    import { marked } from 'marked';
    import Prism from 'prismjs'; // Importa

    const props = defineProps({
      post: Object,
    });

    // Configura o marked com a função de highlight
    marked.setOptions({
      highlight: function (code, lang) {
        if (lang && Prism.languages[lang]) {
          return Prism.highlight(code, Prism.languages[lang], lang);
        }
        return code; // Retorna o código sem realce se a linguagem não for suportada
      },
      // Outras opções do Marked (opcional):
      gfm: true,       // GitHub Flavored Markdown
      breaks: true,    // Quebras de linha com <br>
    });

    const renderedMarkdown = computed(() => {
      return marked(props.post.content);
    });
    </script>
    ```

      * **`marked.setOptions({ ... });`:** Configura o `marked` globalmente.  Você *pode* fazer isso dentro da `computed`, mas configurar globalmente é mais eficiente, pois evita recriar a configuração a cada renderização.
      * **`highlight: function (code, lang) { ... }`:**  A função de callback que será chamada para cada bloco de código.
          * `code`: O código dentro do bloco.
          * `lang`: A linguagem especificada (ex: "php", "js").
          * `if (lang && Prism.languages[lang])`: Verifica se o Prism.js suporta a linguagem.
          * `Prism.highlight(...)`: Usa o Prism.js para realçar.
          * `return code;`:  Retorna o código original se a linguagem não for suportada.
      * **Outras Opções do Marked:**
          * `gfm: true`: Habilita o GitHub Flavored Markdown (suporte a tabelas, listas de tarefas, etc.).
          * `breaks: true`: Insere tags `<br>` em quebras de linha simples (como no Markdown do GitHub).

**Opção 2: Highlight.js com Marked**

  * **Instalação:**

    ```bash
    npm install highlight.js
    ```

  * **CSS (no seu layout Blade ou no componente - dentro de `<style>` *não* scoped):**

    ```html
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/default.min.css">

    ```

    Escolha um tema em [https://highlightjs.org/static/demo/](https://www.google.com/url?sa=E&source=gmail&q=https://highlightjs.org/static/demo/) e substitua `default.min.css` pelo nome do tema (ex: `github.min.css`, `atom-one-dark.min.css`, etc.). Você também pode baixar o CSS e colocá-lo na sua pasta `public`.
    Ou importe no componente:

    ```vue
      <style>
      @import 'highlight.js/styles/default.css'; /*Ou o tema que desejar*/
      </style>
    ```

  * **Configurar o `marked`:**

<!-- end list -->

```vue
<script setup>
import { defineProps, computed } from 'vue';
import { marked } from 'marked';
import hljs from 'highlight.js'; // Importa

const props = defineProps({
  post: Object,
});

marked.setOptions({
  highlight: function (code, lang) {
    const language = hljs.getLanguage(lang) ? lang : 'plaintext'; // Trata linguagens inválidas
    return hljs.highlight(code, { language }).value;
  },
  // Outras opções do Marked (opcional):
  gfm: true,
  breaks: true,
});

const renderedMarkdown = computed(() => {
  return marked(props.post.content);
});
</script>
```

  * **`import hljs from 'highlight.js';`:** Importa o `highlight.js`.
  * **`highlight: function (code, lang) { ... }`:**
      * `hljs.getLanguage(lang) ? lang : 'plaintext'`: Verifica se o `highlight.js` suporta a linguagem. Se não, usa `'plaintext'` para evitar erros.
      * `hljs.highlight(code, { language }).value`: Usa o `highlight.js` para realçar.

**Opção 3: Shiki com Marked (Mais Avançado)**

```vue
<script setup>
import { defineProps, computed, onMounted, ref } from 'vue';
import { marked } from 'marked';
import { getHighlighter } from 'shiki';

const props = defineProps({
    post: Object
});

let shikiHighlighter = ref(null); // Ref para o highlighter

onMounted(async () => {
    shikiHighlighter.value = await getHighlighter({
        theme: 'material-theme-palenight',  // Escolha seu tema
        langs: ['js', 'php', 'css', 'html', 'bash', 'json', 'vue', 'sql'] //Adicione as linguagens
    });
});

marked.setOptions({
    highlight: (code, lang) => {
      if (shikiHighlighter.value && lang) {
        try{
          return shikiHighlighter.value.codeToHtml(code, { lang });
        }catch(error){
            console.log(error);
            return '';
        }
      }
        return code; // Retorna sem highlight se o Shiki não estiver pronto ou a linguagem não for suportada.
    }
});

const renderedMarkdown = computed(() => {
    if (!shikiHighlighter.value) {
      return ''; // Ou alguma mensagem de carregamento
    }
    return marked(props.post.content);
});

</script>
```

**Qual Escolher?**

  * **Prism.js:** Mais simples, bom para a maioria dos casos. Use o CDN + autoloader para começar rapidamente.
  * **Highlight.js:**  Também uma ótima opção, com muitos temas.
  * **Shiki:** Visualmente superior (idêntico ao VS Code), mas exige um pouco mais de configuração (carregamento assíncrono).

**Recomendação:**

1.  **Comece com Prism.js (CDN + autoloader).** É a maneira mais rápida de ter o highlighting funcionando.
2.  Se você quiser mais controle ou precisar de muitas linguagens, use Prism.js ou Highlight.js via npm.
3.  Se você quiser o melhor visual possível (e não se importar com a configuração um pouco mais complexa), use Shiki.

**Importante:**

  * **`v-html` e Segurança:**  *Sempre* sanitize o HTML gerado a partir do Markdown se o conteúdo vier de usuários (usando `DOMPurify`, como mostrado anteriormente).
  * **Escolha UMA biblioteca:** Não misture `markdown-it` e `marked`. Escolha um e use-o consistentemente.  Você já escolheu `marked`, então os exemplos acima são com ele.

Com essas configurações, seus blocos de código Markdown serão convertidos em HTML com o realce de sintaxe aplicado corretamente.

<template>
  <div class="md-editor-wrapper" :class="{ 'md-editor-arrastando': arrastando }">
    <textarea ref="editor" class="md-editor-textarea"></textarea>

    <!-- Seletor próprio: o EasyMDE só cria o input dele junto do botão nativo
         "upload-image", que usa o XHR interno e não serve para este endpoint. -->
    <input
      ref="file_input"
      type="file"
      accept="image/png,image/jpeg,image/webp"
      multiple
      class="hidden"
      aria-hidden="true"
      tabindex="-1"
      @change="ao_selecionar_arquivos"
    />

    <!-- O EasyMDE está com status:false, então este é o único feedback. -->
    <div class="md-editor-aviso" role="status" aria-live="polite">
      <span v-if="enviando > 0">Enviando imagem...</span>
      <span v-else-if="erro_upload" class="md-editor-aviso-erro">{{ erro_upload }}</span>
    </div>
  </div>
</template>

<script>
import EasyMDE from "easymde";
import "easymde/dist/easymde.min.css"; // Importante!
import { normalizar_origem_conteudo } from "@/helpers";

// Mesmos limites da imagem de capa (PostController::store).
const TIPOS_ACEITOS = ["image/png", "image/jpeg", "image/webp"];
const TAMANHO_MAXIMO = 5 * 1024 * 1024;
const MENSAGEM_TIPO = "Use PNG, JPG ou WebP de até 5MB.";
const MENSAGEM_TAMANHO = "A imagem deve ter no máximo 5MB.";

export default {
    props: {
        modelValue: {
            // Use modelValue para v-model
            type: String,
            default: "",
        },
        titulo: {
            // Título atual do form: o servidor usa para montar o slug da URL.
            type: String,
            default: "",
        },
    },
    data() {
        return {
            enviando: 0,
            erro_upload: "",
            arrastando: false,
        };
    },
    computed: {
        /**
         * Domínio de produção gravado nas URLs das imagens de conteúdo.
         *
         * Vem do servidor (prop compartilhada pelo HandleInertiaRequests)
         * porque o frontend não tem como conhecer o domínio sozinho.
         */
        url_publica() {
            return this.$page?.props?.techpulse_url_publica || "";
        },
    },
    mounted() {
        this.editor = new EasyMDE({
            element: this.$refs.editor,
            initialValue: this.modelValue,
            placeholder: "Escreva o conteúdo do post aqui... (Suporta Markdown)",
            spellChecker: false,
            status: false,
            minHeight: "560px",
            sideBySideFullscreen: false,
            // O upload é feito pelos handlers próprios abaixo: o caminho nativo
            // do EasyMDE decide imagem-vs-link pela extensão da URL, e a nossa
            // URL não tem extensão.
            uploadImage: false,
            toolbar: [
                "bold",
                "italic",
                "strikethrough",
                "|",
                "heading",
                "|",
                "quote",
                "code",
                "link",
                "image",
                {
                    // Nome próprio, e não "upload-image" (item nativo).
                    name: "upload-imagem-conteudo",
                    className: "upload-imagem-conteudo",
                    title: "Inserir imagem no conteúdo",
                    action: () => this.abrir_seletor_de_arquivos(),
                },
                "|",
                "preview",
                "side-by-side",
                "fullscreen",
            ],
        });

        this.editor.codemirror.on("change", () => {
            this.$emit("update:modelValue", this.editor.value());
        });

        // O preview do EasyMDE usa o `marked` embutido e precisa da mesma troca
        // de origem que a página pública faz. Atribuído depois de construir o
        // editor porque depende da instância para renderizar o markdown.
        this.editor.options.previewRender = (texto) =>
            this.editor.markdown(normalizar_origem_conteudo(texto, this.url_publica));

        this.registrar_handlers_de_arquivo();
    },

    beforeUnmount() {
        // Limpa o editor ao desmontar
        if (this.editor) {
            this.remover_handlers_de_arquivo();
            this.editor.toTextArea();
            this.editor = null;
        }
    },
    watch: {
        modelValue(newValue) {
            if (this.editor && this.editor.value() !== newValue) {
                this.editor.value(newValue);
            }
        },
    },
    methods: {
        /**
         * Registra os handlers de colar/arrastar arquivo no CodeMirror.
         *
         * Vão no `codemirror` (e não em elementos do template) porque o handler
         * interno do CodeMirror aborta quando o evento já veio com
         * defaultPrevented — é o que impede o navegador de inserir o arquivo
         * como texto. As referências ficam em `this` para que o off() do
         * beforeUnmount remova exatamente estas funções.
         */
        registrar_handlers_de_arquivo() {
            const cm = this.editor.codemirror;

            this.ao_colar_handler = (cm_instancia, evento) => this.ao_colar(evento);
            this.ao_soltar_handler = (cm_instancia, evento) => this.ao_soltar(cm_instancia, evento);
            this.ao_arrastar_handler = (cm_instancia, evento) => this.ao_arrastar_sobre(evento);
            this.ao_sair_arrasto_handler = () => {
                this.arrastando = false;
            };

            cm.on("paste", this.ao_colar_handler);
            cm.on("drop", this.ao_soltar_handler);
            cm.on("dragover", this.ao_arrastar_handler);
            cm.on("dragleave", this.ao_sair_arrasto_handler);
        },

        /**
         * Remove os handlers registrados, para não vazarem entre montagens.
         */
        remover_handlers_de_arquivo() {
            const cm = this.editor?.codemirror;

            if (!cm) {
                return;
            }

            cm.off("paste", this.ao_colar_handler);
            cm.off("drop", this.ao_soltar_handler);
            cm.off("dragover", this.ao_arrastar_handler);
            cm.off("dragleave", this.ao_sair_arrasto_handler);
        },

        /**
         * Trata a colagem de arquivos no editor.
         *
         * @param {ClipboardEvent} evento - O evento de colagem do CodeMirror.
         */
        ao_colar(evento) {
            // O CodeMirror tem dois listeners de paste (textarea e scroller): se
            // o evento chegasse aos dois, o upload sairia duplicado.
            if (evento.defaultPrevented) {
                return;
            }

            const arquivos = Array.from(evento.clipboardData?.files || []);

            if (!arquivos.length) {
                return; // colagem de texto comum: o CodeMirror trata normalmente
            }

            evento.preventDefault();
            this.enviar_arquivos(arquivos, this.editor.codemirror.getCursor());
        },

        /**
         * Trata o arquivo solto sobre o editor, inserindo na posição do mouse.
         *
         * @param {Object} cm_instancia - A instância do CodeMirror.
         * @param {DragEvent} evento - O evento de drop.
         */
        ao_soltar(cm_instancia, evento) {
            const arquivos = Array.from(evento.dataTransfer?.files || []);

            if (!arquivos.length) {
                return; // arrasto de texto interno do editor: não intercepta
            }

            evento.preventDefault();
            this.arrastando = false;

            // A posição só é resolvida dentro do evento; depois o layout muda.
            const posicao = cm_instancia.coordsChar(
                { left: evento.clientX, top: evento.clientY },
                "window"
            );

            this.enviar_arquivos(arquivos, posicao);
        },

        /**
         * Habilita o drop e liga o destaque visual enquanto há arquivo sobre o editor.
         *
         * @param {DragEvent} evento - O evento de dragover.
         */
        ao_arrastar_sobre(evento) {
            if (!this.arrasta_arquivos(evento)) {
                return; // arrasto de texto selecionado dentro do editor
            }

            // Sem o preventDefault o navegador abre o arquivo em vez de soltá-lo.
            evento.preventDefault();
            this.arrastando = true;
        },

        /**
         * Verifica se o arrasto carrega arquivos.
         *
         * Durante o dragover o navegador não expõe os arquivos, só os tipos.
         *
         * @param {DragEvent} evento - O evento de arrasto.
         *
         * @returns {boolean} True quando o arrasto traz arquivos do sistema.
         */
        arrasta_arquivos(evento) {
            const tipos = evento.dataTransfer?.types;

            return !!tipos && Array.from(tipos).indexOf("Files") !== -1;
        },

        /**
         * Abre o seletor de arquivos escondido.
         */
        abrir_seletor_de_arquivos() {
            this.$refs.file_input?.click();
        },

        /**
         * Trata os arquivos escolhidos no seletor.
         *
         * @param {Event} evento - O evento de change do input.
         */
        ao_selecionar_arquivos(evento) {
            const arquivos = Array.from(evento.target.files || []);
            evento.target.value = ""; // permite escolher o mesmo arquivo de novo

            if (!arquivos.length) {
                return;
            }

            this.enviar_arquivos(arquivos, this.editor.codemirror.getCursor());
        },

        /**
         * Envia os arquivos e insere o markdown de cada um na posição informada.
         *
         * O envio é sequencial de propósito: em paralelo, a ordem das imagens no
         * texto passaria a depender de qual requisição voltasse primeiro.
         *
         * @param {File[]} arquivos - Os arquivos escolhidos.
         * @param {Object} posicao - Posição do cursor ({line, ch}) onde inserir.
         */
        async enviar_arquivos(arquivos, posicao) {
            this.erro_upload = "";

            let cursor = posicao;

            for (const arquivo of arquivos) {
                const problema = this.problema_do_arquivo(arquivo);

                if (problema) {
                    this.erro_upload = problema;
                    continue;
                }

                this.enviando += 1;

                try {
                    const dados = new FormData();
                    dados.append("image", arquivo);
                    dados.append("title", this.titulo || "");

                    const resposta = await window.axios.post(
                        route("posts.content_images.store"),
                        dados
                    );

                    cursor = this.inserir_imagem(arquivo, resposta.data.url, cursor);
                } catch (erro) {
                    this.erro_upload =
                        erro?.response?.data?.errors?.image?.[0] ||
                        erro?.response?.data?.message ||
                        "Não foi possível enviar a imagem.";
                } finally {
                    this.enviando -= 1;
                }
            }
        },

        /**
         * Insere o markdown da imagem na posição informada e devolve o novo cursor.
         *
         * Usa replaceRange (e não replaceSelection) porque no drop a posição é a
         * do mouse, que nem sempre é onde o cursor está.
         *
         * @param {File} arquivo - O arquivo enviado, usado no texto alternativo.
         * @param {string} url - A URL absoluta devolvida pelo servidor.
         * @param {Object} posicao - Posição ({line, ch}) da inserção.
         *
         * @returns {Object} A posição do fim do texto inserido.
         */
        inserir_imagem(arquivo, url, posicao) {
            const markdown = `![${this.texto_alternativo(arquivo)}](${url})`;
            const cm = this.editor.codemirror;

            cm.replaceRange(markdown, posicao);

            // O markdown não tem quebra de linha, então o fim do texto inserido
            // é um deslocamento na mesma linha — e é o cursor da próxima imagem.
            const fim = { line: posicao.line, ch: posicao.ch + markdown.length };

            cm.setCursor(fim);
            cm.focus();

            return fim;
        },

        /**
         * Monta o texto alternativo da imagem a partir do nome do arquivo.
         *
         * @param {File} arquivo - O arquivo enviado.
         *
         * @returns {string} O nome sem extensão e sem caracteres que quebrariam o markdown.
         */
        texto_alternativo(arquivo) {
            const nome = (arquivo.name || "")
                .replace(/\.[^.]+$/, "")
                .replace(/[[\]()\r\n]/g, "")
                .trim();

            return nome || "imagem";
        },

        /**
         * Valida tipo e tamanho do arquivo antes de enviar.
         *
         * @param {File} arquivo - O arquivo escolhido.
         *
         * @returns {string} A mensagem de erro, ou string vazia se o arquivo serve.
         */
        problema_do_arquivo(arquivo) {
            if (TIPOS_ACEITOS.indexOf(arquivo.type) === -1) {
                return MENSAGEM_TIPO;
            }

            if (arquivo.size > TAMANHO_MAXIMO) {
                return MENSAGEM_TAMANHO;
            }

            return "";
        },
    },
};
</script>

<style scoped>
/* EasyMDE no tema dark "Midnight Pulse" */
.md-editor-wrapper {
    position: relative;
}

:deep(.EasyMDEContainer) {
    background: transparent;
    border: none;
    box-shadow: none;
    position: relative;
    transition: box-shadow 0.15s ease;
}

/* Arquivo pairando sobre o editor */
.md-editor-arrastando :deep(.EasyMDEContainer) {
    box-shadow: inset 0 0 0 2px #2b52ee;
}

:deep(.EasyMDEContainer .CodeMirror) {
    background: transparent;
    color: #f6f6f8;
    font-family: "JetBrains Mono", monospace;
    font-size: 0.95rem;
    line-height: 1.7;
}

:deep(.EasyMDEContainer .CodeMirror-scroll) {
    min-height: 560px;
}

:deep(.editor-toolbar) {
    background: #001247; /* surface */
    border: none;
    border-bottom: 1px solid rgba(98, 114, 180, 0.2);
    border-radius: 0;
    opacity: 1;
}

:deep(.editor-toolbar button) {
    color: #6272b4;
    transition: color 0.2s ease, background-color 0.2s ease;
}

:deep(.editor-toolbar button:hover),
:deep(.editor-toolbar button.active) {
    background: rgba(43, 82, 238, 0.1);
    border-color: transparent;
    color: #f6f6f8;
}

/* Ícone do botão de upload. A fonte é declarada aqui porque a classe
   .material-symbols-outlined não está definida no CSS do projeto — só o
   @font-face vem do <link> no app.blade.php. */
:deep(.editor-toolbar button.upload-imagem-conteudo)::before {
    content: "add_photo_alternate";
    display: inline-block;
    font-family: "Material Symbols Outlined";
    font-size: 18px;
    font-weight: normal;
    font-style: normal;
    line-height: 1;
    letter-spacing: normal;
    text-transform: none;
    white-space: nowrap;
    direction: ltr;
    font-feature-settings: "liga";
    -webkit-font-smoothing: antialiased;
}

:deep(.editor-toolbar i.separator) {
    border-left: 1px solid rgba(98, 114, 180, 0.2);
    border-right: none;
}

:deep(.editor-toolbar.fullscreen),
:deep(.editor-toolbar.no-fullscreen) {
    background: #001247;
}

:deep(.CodeMirror-cursor) {
    border-left: 2px solid #2b52ee;
}

:deep(.CodeMirror-selected) {
    background: rgba(43, 82, 238, 0.25) !important;
}

:deep(.CodeMirror .CodeMirror-placeholder) {
    color: rgba(246, 246, 248, 0.35);
}

:deep(.CodeMirror pre.CodeMirror-line),
:deep(.CodeMirror pre.CodeMirror-line-like) {
    color: #f6f6f8;
}

:deep(.editor-preview),
:deep(.editor-preview-side) {
    background: transparent;
    color: #f6f6f8;
    font-family: "Inter", sans-serif;
}

:deep(.editor-preview-side) {
    border-color: rgba(98, 114, 180, 0.2);
}

:deep(.EasyMDEContainer .CodeMirror-fullscreen) {
    background: #000b2b; /* surface-dim */
    z-index: 60;
}

/* Aviso de upload/erro */
.md-editor-aviso {
    min-height: 1.5rem;
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
    color: #6272b4; /* on-surface-variant */
}

.md-editor-aviso-erro {
    color: #ff5f56; /* error */
}
</style>

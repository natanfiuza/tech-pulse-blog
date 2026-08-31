<template>
  <textarea ref="editor" class="md-editor-textarea"></textarea>
</template>

<script>
import EasyMDE from "easymde";
import "easymde/dist/easymde.min.css"; // Importante!

export default {
    props: {
        modelValue: {
            // Use modelValue para v-model
            type: String,
            default: "",
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
                "|",
                "preview",
                "side-by-side",
                "fullscreen",
            ],
        });

        this.editor.codemirror.on("change", () => {
            this.$emit("update:modelValue", this.editor.value());
        });
    },

    beforeUnmount() {
        // Limpa o editor ao desmontar
        if (this.editor) {
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
};
</script>

<style scoped>
/* EasyMDE no tema dark "Midnight Pulse" */
:deep(.EasyMDEContainer) {
    background: transparent;
    border: none;
    box-shadow: none;
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
</style>

<template>
    <AdminLayout>
        <h2>Novo Post</h2>
        <form @submit.prevent="submit">
            <div class="mt-4 form-group">
                <label for="title">Título:</label>
                <input id="title" v-model="form.title" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.title }" />
                <div v-if="form.errors.title" class="erro-message">{{ form.errors.title }}</div>
            </div>
            <div class="mt-4">
                <label for="content">Conteúdo:</label>
                <MarkdownEditor v-model="form.content" :class="{ 'is-invalid': form.errors.content }" />
                <div v-if="form.errors.content" class="erro-message">{{ form.errors.content }}</div>
            </div>
            <div class="mt-3 form-group">
                <label for="excerpt">Resumo:</label>
                <textarea id="excerpt" v-model="form.excerpt" rows="5" class="form-control"
                    :class="{ 'is-invalid': form.errors.excerpt }"></textarea>
                <div v-if="form.errors.excerpt" class="erro-message">{{ form.errors.excerpt }}</div>
            </div>
            <div class="mt-3 form-group"> </-- Campo para upload de imagem -->
                <label for="image">Imagem:</label>
                <input id="image" type="file" @change="onFileChange" class="form-control" >
                <div v-if="form.errors.image" class="erro-message">{{ form.errors.image }}</div>
            </div>
            <button type="submit" class="btn btn-primary mt-4 btn-criar" :disabled="form.processing">
                Criar
            </button>
        </form>
    </AdminLayout>
</template>

<script>
import { useForm } from "@inertiajs/inertia-vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import MarkdownEditor from "@/Components/Admin/MarkdownEditor.vue";
import { ref, watch } from "vue"; // Importa ref para o conteúdo original

export default {
    components: {
        AdminLayout,
        MarkdownEditor,
    },
    setup() {
        // Ref para armazenar o conteúdo real do editor Markdown
        const originalContent = ref("");

        // Inicializa o formulário do Inertia SEM o 'content' diretamente
        // Adicionamos 'image' para o upload
        const form = useForm({
            title: "",
            excerpt: "",
            image: null, // Campo para a imagem
            // 'content' será adicionado via transform()
        });

        // Função chamada quando um arquivo é selecionado
        function onFileChange(event) {
            if (event.target.files.length > 0) {
                form.image = event.target.files[0]; // Pega o primeiro arquivo selecionado
            } else {
                form.image = null; // Limpa se nenhum arquivo for selecionado
            }
        }

        watch(() => form.title, (newTitleValue) => {
            if (newTitleValue && form.errors.title) {
                form.errors.title = null;
            }
        });
        watch(() => form.excerpt, (newExcerptValue) => {
            if (newExcerptValue && form.errors.excerpt) {
                form.errors.excerpt = null;
            }
        });
        watch(() => form.content, (newContentValue) => {
            if (newContentValue && form.errors.content) {
                form.errors.content = null;
            }
        });

        // Função de envio do formulário
        function submit() {
            // 1. Codifica o conteúdo do editor para Base64
            let encodedContent = null;
            try {
                const utf8Bytes = new TextEncoder().encode(originalContent.value);
                const binaryString = String.fromCharCode(...utf8Bytes);
                encodedContent = btoa(binaryString);
            } catch (e) {
                console.error("Erro ao codificar conteúdo:", e);
                form.setError("content", "Não foi possível codificar o conteúdo para envio.");
                return; // Impede o envio se a codificação falhar
            }

            // 2. Envia o formulário usando form.post()
            form
                .transform((data) => ({
                    // Cria um novo objeto com os dados existentes...
                    ...data,
                    // ...e adiciona o 'content' codificado
                    content: encodedContent,
                }))
                .post(route("posts.store"), {
                    // Usa a rota nomeada
                    forceFormData: form.image instanceof File, // Usa FormData SE houver um arquivo de imagem
                    // Opcional: callbacks de erro/sucesso
                    onError: (errors) => {
                        console.error("Erros:", errors);
                        // Limpa o erro de content se ele veio do try/catch e não do backend
                        if (
                            !errors.content &&
                            form.errors.content === "Não foi possível codificar o conteúdo para envio."
                        ) {
                            form.clearErrors("content");
                        }
                    },
                    // onSuccess: () => { /* Limpar formulário, etc. */ }
                });
        }

        // Retorna as variáveis e funções para serem usadas no template
        return {
            form,
            submit,
            originalContent, // Expõe o ref para v-model no MarkdownEditor
            onFileChange, // Expõe a função para o @change do input file
        };
    },
};
</script>

<style scoped>
.btn-criar {

    color: aliceblue;

}

.erro-message {
    color: crimson;
    font-size: .7rem;
}
</style>

<template>
  <AdminLayout>
    <h2>Novo Post</h2>
    <form @submit.prevent="submit">
      <div class="mt-4 form-group">
        <label for="title">Título:</label>
        <input id="title" v-model="form.title" type="text" class="form-control" />
        <div v-if="form.errors.title" class="erro-message">{{ form.errors.title }}</div>
      </div>
      <div>
        <label for="content">Conteúdo:</label>
        <MarkdownEditor v-model="originalContent" />
        <div v-if="form.errors.content" class="erro-message">
          {{ form.errors.content }}
        </div>
      </div>
      <div class="mt-3 form-group">
        <label for="excerpt">Resumo:</label>
        <textarea
          id="excerpt"
          v-model="form.excerpt"
          rows="5"
          class="form-control"
        ></textarea>
        <div v-if="form.errors.excerpt" class="erro-message">
          {{ form.errors.excerpt }}
        </div>
      </div>
      <div class="mt-3 form-group">
        <label for="image">Imagem:</label>
        <input id="image" type="file" @change="onFileChange" class="form-control" />
        <div v-if="form.errors.image" class="erro-message">{{ form.errors.image }}</div>
      </div>
      <button
        type="submit"
        class="btn btn-primary mt-4 btn-criar"
        :disabled="form.processing"
      >
        Criar
      </button>
    </form>
  </AdminLayout>
</template>

<script>
import { useForm } from "@inertiajs/inertia-vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import MarkdownEditor from "@/Components/Admin/MarkdownEditor.vue";
import { ref } from "vue"; // Importa o ref

export default {
  components: {
    AdminLayout,
    MarkdownEditor,
  },
  setup() {
    // Ref para o conteúdo real do editor
    const originalContent = ref("");

    // Inicializa o form SEM 'content', mas com 'image'
    const form = useForm({
      title: "",
      excerpt: "",
      image: null, // Para o arquivo de imagem
    });

    // Função para lidar com a seleção de arquivo
    function onFileChange(event) {
      if (event.target.files.length > 0) {
        form.image = event.target.files[0];
      } else {
        form.image = null;
      }
    }

    // Função de submit modificada
    function submit() {
      // 1. Codifica o conteúdo para Base64
      let encodedContent = null;
      try {
        const utf8Bytes = new TextEncoder().encode(originalContent.value);
        const binaryString = String.fromCharCode(...utf8Bytes);
        encodedContent = btoa(binaryString);
      } catch (e) {
        console.error("Erro ao codificar conteúdo:", e);
        // Define um erro no campo 'content' para feedback ao usuário
        form.setError("content", "Não foi possível codificar o conteúdo para envio.");
        return; // Interrompe o envio
      }

      // 2. Envia o formulário usando transform para adicionar o content codificado
      form
        .transform((data) => ({
          ...data, // Mantém os dados existentes (title, excerpt, image)
          content: encodedContent, // Adiciona o content codificado
        }))
        .post(route("posts.store"), {
          // Usa a rota nomeada
          forceFormData: form.image instanceof File, // Força FormData se houver imagem
          // Opcional: Tratamento de erro mais específico
          onError: (errors) => {
            console.error("Erros do Backend:", errors);
            // Limpa o erro de codificação se ele não veio do backend
            if (
              !errors.content &&
              form.errors.content === "Não foi possível codificar o conteúdo para envio."
            ) {
              form.clearErrors("content");
            }
          },
        });
    }

    // Retorna o necessário para o template
    return {
      form,
      submit,
      originalContent, // Para ligar ao MarkdownEditor
      onFileChange, // Para ligar ao input file
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
  font-size: 0.7rem;
}
</style>

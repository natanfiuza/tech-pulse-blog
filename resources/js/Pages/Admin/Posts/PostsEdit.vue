<template>
    <AdminLayout>
        <h1>Editar Post</h1>
        <form @submit.prevent="submit" enctype="multipart/form-data">
            <div class="mt-4 form-group">
                <label for="title">Título:</label>
                <input id="title" v-model="form.title" class="form-control" type="text" />
                <div v-if="form.errors.title">{{ form.errors.title }}</div>
            </div>
            <div class="mt-3 form-group">
                <label for="content">Conteúdo:</label>
                <MarkdownEditor :modelValue="originalContent" @update:modelValue="updateOriginalContent" />
                <div v-if="form.errors.content">{{ form.errors.content }}</div>
            </div>
            <div class="mt-3 form-group">
                <label for="excerpt">Resumo:</label>
                <textarea id="excerpt" v-model="form.excerpt" rows="5" class="form-control"></textarea>
                <div v-if="form.errors.excerpt">{{ form.errors.excerpt }}</div>
            </div>
            <div class="mt-3 form-group"> </-- Campo para upload de imagem -->
                <label for="image">Imagem:</label>
                <input id="image" type="file" @change="onFileChange" class="form-control">
                <div v-if="form.errors.image">{{ form.errors.image }}</div>
            </div>
            <button type="submit" class="btn btn-primary mt-4 btn-atualizar" :disabled="form.processing">
                Atualizar
            </button>
        </form>
    </AdminLayout>
</template>

<script>
import { useForm } from "@inertiajs/inertia-vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import MarkdownEditor from "@/Components/Admin/MarkdownEditor.vue";
import { ref } from "vue"; // Para o conteúdo original

export default {
  components: {
    AdminLayout,
    MarkdownEditor,
  },
  props: {
    post: Object,
  },
  setup(props) {
    // Armazena o conteúdo original do editor
    const originalContent = ref(props.post?.content || "");

    const form = useForm({
      title: props.post?.title || "",
      // content: '', // Não inicializa o content diretamente no form
      excerpt: props.post?.excerpt || "",
      image: null,
    });

    function submit() {
      // 1. Codifica o conteúdo antes de enviar
      let encodedContent = null;
      try {
        const utf8Bytes = new TextEncoder().encode(originalContent.value);
        const binaryString = String.fromCharCode(...utf8Bytes); // Converte bytes para string binária
        encodedContent = btoa(binaryString); // Codifica para Base64
      } catch (e) {
        console.error("Erro ao codificar conteúdo:", e);
        // Tratar o erro - talvez mostrar uma mensagem ao usuário
        form.setError("content", "Não foi possível codificar o conteúdo para envio.");
        return; // Impede o envio
      }


      // 2. Envia o formulário com o conteúdo codificado
      form
        .transform((data) => ({
          ...data,
          content: encodedContent, // Adiciona o conteúdo codificado aos dados
        }))
        .put(route("posts.update", { uuid: props.post.uuid }));
      // .post(route("posts.store"), {
      //   // Ou put para update
      //   forceFormData: form.image instanceof File, // Usa FormData só se houver imagem
      //   // onError: errors => { /* ... */ },
      //   // onSuccess: () => { /* ... */ }
      // });
    }

    function onFileChange(event) {
      form.image = event.target.files[0];
    }

    // Atualiza o ref originalContent quando o editor mudar
    const updateOriginalContent = (newContent) => {
      originalContent.value = newContent;
    };

    return {
      form,
      submit,
      onFileChange,
      originalContent, // Passa para o template
      updateOriginalContent, // Passa para o template
    };
  },

};
</script>

<style scoped>
.btn-atualizar {
  color: aliceblue;
}
</style>

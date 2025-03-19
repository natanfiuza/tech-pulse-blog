<template>
  <AdminLayout>
    <h2>Novo Post</h2>
    <form @submit.prevent="submit">
      <div class="mt-4 form-group">
        <label for="title">Título:</label>
        <input id="title" v-model="form.title" type="text" class="form-control" />
        <div v-if="form.errors.title">{{ form.errors.title }}</div>
      </div>
      <div>
        <label for="content">Conteúdo:</label>
        <MarkdownEditor v-model="form.content" />
        <div v-if="form.errors.content">{{ form.errors.content }}</div>
      </div>
      <div class="mt-3 form-group">
        <label for="excerpt">Resumo:</label>
        <textarea
          id="excerpt"
          v-model="form.excerpt"
          rows="5"
          class="form-control"
        ></textarea>
        <div v-if="form.errors.excerpt">{{ form.errors.excerpt }}</div>
      </div>
      <button type="submit" class="btn btn-primary mt-4" :disabled="form.processing">
        Criar
      </button>
    </form>
  </AdminLayout>
</template>

<script>
import { useForm } from "@inertiajs/inertia-vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import MarkdownEditor from "@/Components/Admin/MarkdownEditor.vue";

export default {
  components: {
    AdminLayout,
    MarkdownEditor,
  },
  setup() {
    const form = useForm({
      title: "",
      excerpt: "",
      content: "",
    });

    function submit() {
      form.post("/admin/posts/store"); //Envia o POST
    }

    return { form, submit };
  },
};
</script>

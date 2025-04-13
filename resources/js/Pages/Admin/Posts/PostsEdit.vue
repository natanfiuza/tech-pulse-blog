<template>
  <AdminLayout>
    <h1>Editar Post</h1>
    <form @submit.prevent="submit">
      <div class="mt-4 form-group">
        <label for="title">Título:</label>
        <input id="title" v-model="form.title" class="form-control" type="text" />
        <div v-if="form.errors.title">{{ form.errors.title }}</div>
      </div>
      <div class="mt-3 form-group">
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
      <button
        type="submit"
        class="btn btn-primary mt-4 btn-atualizar"
        :disabled="form.processing"
      >
        Atualizar
      </button>
    </form>
  </AdminLayout>
</template>

<script>
import { useForm } from "@inertiajs/inertia-vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import MarkdownEditor from "@/Components/Admin/MarkdownEditor.vue";
import { Ziggy } from "../../../ziggy"; // Importe o arquivo ziggy.js gerado
export default {
  components: {
    AdminLayout,
    MarkdownEditor,
  },
  props: {
    post: Object,
  },
  setup(props) {
    const form = useForm({
      uuid: props.post.uuid,
      title: props.post.title,
      content: props.post.content,
      excerpt: props.post.excerpt,
    });

    function submit() {
      form.put(route("posts.update", { uuid: props.post.uuid })); // Usando route()
    }

    return { form, submit };
  },
};
</script>

<style scoped>
.btn-atualizar {
  color: aliceblue;
}
</style>

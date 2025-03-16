<template>
  <AdminLayout>
    <h1>Editar Post</h1>
    <form @submit.prevent="submit">
      <div>
        <label for="title">Título:</label>
        <input id="title" v-model="form.title" type="text" />
        <div v-if="form.errors.title">{{ form.errors.title }}</div>
      </div>
      <div>
        <label for="content">Conteúdo:</label>
        <MarkdownEditor v-model="form.content" />
        <div v-if="form.errors.content">{{ form.errors.content }}</div>
      </div>
      <button type="submit" :disabled="form.processing">Atualizar</button>
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
  props: {
    post: Object,
  },
  setup(props) {
    const form = useForm({
      title: props.post.title,
      content: props.post.content,
    });

    function submit() {
      form.put(route("posts.update", props.post)); // Envia um PUT/PATCH
    }

    return { form, submit };
  },
};
</script>

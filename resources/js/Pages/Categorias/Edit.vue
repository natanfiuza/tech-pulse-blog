<template>
  <div>
    <Head>
      <title>Editar Categoria</title>
    </Head>
    <h1>Editar Categoria</h1>
    <form @submit.prevent="submit">
      <div class="mb-3">
        <label for="name" class="form-label">Nome:</label>
        <input type="text" id="name" v-model="form.name" class="form-control" required />
        <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
      </div>

      <div class="mb-3">
        <label for="description" class="form-label">Descrição:</label>
        <textarea
          id="description"
          v-model="form.description"
          class="form-control"
        ></textarea>
        <div v-if="form.errors.description" class="invalid-feedback">
          {{ form.errors.description }}
        </div>
      </div>

      <div class="mb-3">
        <label for="scope" class="form-label">Abrangência:</label>
        <textarea id="scope" v-model="form.scope" class="form-control"></textarea>
        <div v-if="form.errors.scope" class="invalid-feedback">
          {{ form.errors.scope }}
        </div>
      </div>

      <div class="mb-3">
        <label for="possible_contents" class="form-label">Possíveis Conteúdos:</label>
        <textarea
          id="possible_contents"
          v-model="form.possible_contents"
          class="form-control"
        ></textarea>
        <div v-if="form.errors.possible_contents" class="invalid-feedback">
          {{ form.errors.possible_contents }}
        </div>
      </div>

      <div class="mb-3">
        <label for="post_suggestions" class="form-label">Sugestões de Postagens:</label>
        <textarea
          id="post_suggestions"
          v-model="form.post_suggestions"
          class="form-control"
        ></textarea>
        <div v-if="form.errors.post_suggestions" class="invalid-feedback">
          {{ form.errors.post_suggestions }}
        </div>
      </div>

      <div class="mb-3">
        <label for="parent_id" class="form-label">Categoria Pai:</label>
        <select id="parent_id" v-model="form.parent_id" class="form-select">
          <option :value="null">Nenhuma (Categoria Principal)</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </option>
        </select>
        <div v-if="form.errors.parent_id" class="invalid-feedback">
          {{ form.errors.parent_id }}
        </div>
      </div>

      <button type="submit" class="btn btn-primary" :disabled="form.processing">
        Atualizar
      </button>
    </form>
  </div>
</template>

<script setup>
import { useForm } from "@inertiajs/inertia-vue3";
import { defineProps } from "vue";
import { Head } from "@inertiajs/inertia-vue3";

const props = defineProps({
  category: Object, // A categoria sendo editada
  categories: Array, //Todas as categorias
});

const form = useForm({
  name: props.category.name,
  description: props.category.description,
  scope: props.category.scope,
  possible_contents: props.category.possible_contents,
  post_suggestions: props.category.post_suggestions,
  parent_id: props.category.parent_id,
  _method: "PUT", // Importante para o Laravel saber que é uma atualização
});

const submit = () => {
  form.put(route("categories.update", { category: props.category.slug }), {
    preserveScroll: true, //Preserva o scroll da página
  });
};
</script>
<style scoped>
/*Seus estilos*/
</style>

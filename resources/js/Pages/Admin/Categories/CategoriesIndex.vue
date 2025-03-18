<template>
  <div>
    <Navbar />
    <Head>
      <title>Categorias</title>
    </Head>

    <h1>Categorias</h1>
    <InertiaLink :href="'/categories/create'" class="btn btn-primary mb-3"
      >Nova Categoria</InertiaLink
    >

    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Nome</th>
            <th>Slug</th>
            <th>Descrição</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="category in categories" :key="category.id">
            <td>
              <span :style="{ marginLeft: `${category.level * 20}px` }">
                {{ category.name }}
              </span>
            </td>
            <td>{{ category.slug }}</td>
            <td>{{ category.description }}</td>
            <td>
              <InertiaLink
                :href="'/categories/edit/' + category.slug"
                class="btn btn-sm btn-primary me-1"
                >Editar</InertiaLink
              >
              <button @click="deleteCategory(category)" class="btn btn-sm btn-danger">
                Excluir
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from "vue";
import { InertiaLink, useForm } from "@inertiajs/inertia-vue3";
import { Head } from "@inertiajs/inertia-vue3";
//import CategoryName from "@/Components/CategoryName.vue"; // Componente para exibir o nome recursivamente
import Navbar from "@/Components/Navbar.vue";

// import CategoryName from '@/Components/CategoryName.vue'; // Remove o componente recursivo

defineProps({
  categories: Array,
});

const deleteCategory = (category) => {
  if (confirm(`Tem certeza que deseja excluir a categoria "${category.name}"?`)) {
    const form = useForm({});
    form.delete(route("categories.destroy", { category: category.slug }));
  }
};
</script>
<style scoped>
/*Seus estilos*/
</style>

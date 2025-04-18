<template>
    <AdminLayout>
        <h2>Editar Categoria: {{ form.name }}</h2>

        <form @submit.prevent="submit">
            <div class="mb-3">
                <label for="name" class="form-label">Nome:</label>
                <input id="name" v-model="form.name" type="text" class="form-control" />
                <div v-if="form.errors.name" class="text-danger mt-1">{{ form.errors.name }}</div>
            </div>

            <div class="mb-3">
                <label for="parent_id" class="form-label">Categoria Pai:</label>
                <select id="parent_id" v-model="form.parent_id" class="form-select">
                    <option :value="null">-- Nenhuma --</option>
                    <option v-for="parent in categories" :key="parent.id" :value="parent.id">
                        {{ parent.name }}
                    </option>
                </select>
                <div v-if="form.errors.parent_id" class="text-danger mt-1">{{ form.errors.parent_id }}</div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Descrição:</label>
                <textarea id="description" v-model="form.description" rows="4" class="form-control"></textarea>
                <div v-if="form.errors.description" class="text-danger mt-1">{{ form.errors.description }}</div>
            </div>

            <div class="mb-3">
                <label for="scope" class="form-label">Abrangência:</label>
                <textarea id="scope" v-model="form.scope" rows="3" class="form-control"></textarea>
                <div v-if="form.errors.scope" class="text-danger mt-1">{{ form.errors.scope }}</div>
            </div>

            <div class="mb-3">
                <label for="possible_contents" class="form-label">Possíveis Conteúdos:</label>
                <textarea id="possible_contents" v-model="form.possible_contents" rows="5"
                    class="form-control"></textarea>
                <div v-if="form.errors.possible_contents" class="text-danger mt-1">{{ form.errors.possible_contents }}
                </div>
            </div>

            <div class="mb-3">
                <label for="post_suggestions" class="form-label">Sugestões de Postagens:</label>
                <textarea id="post_suggestions" v-model="form.post_suggestions" rows="5"
                    class="form-control"></textarea>
                <div v-if="form.errors.post_suggestions" class="text-danger mt-1">{{ form.errors.post_suggestions }}
                </div>
            </div>

            <button type="submit" class="btn btn-primary" :disabled="form.processing">
                Atualizar Categoria
            </button>
            <Link :href="route('admin.categories.index')" class="btn btn-secondary ms-2">
            Cancelar
            </Link>
        </form>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm } from '@inertiajs/vue3'; // Use vue3 imports
import { defineProps } from 'vue';

// Props recebidas do controller
const props = defineProps({
    category: Object, // A categoria sendo editada
    categories: Array, // Os pais disponíveis
});

// Inicializa o formulário com os dados da categoria atual
const form = useForm({
    name: props.category.name,
    description: props.category.description ?? '', // Usa ?? para tratar nulos
    scope: props.category.scope ?? '',
    possible_contents: props.category.possible_contents ?? '',
    post_suggestions: props.category.post_suggestions ?? '',
    parent_id: props.category.parent_id, // Pode ser null
});

// Função de submit
const submit = () => {
    form.put(route('admin.categories.update', { category: props.category.id }), {
        onError: (errors) => { console.error('Erros:', errors); }
    });
};
</script>

<style scoped>
.text-danger {
    color: #dc3545;
    font-size: 0.875em;
}
</style>

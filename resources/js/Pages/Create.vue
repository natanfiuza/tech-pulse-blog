<template>
    <div class="container">
        <h1>Criar Novo Post</h1>
        <form @submit.prevent="submit">
            <div>
                <label for="title">Título:</label>
                <input type="text" id="title" v-model="form.title" required>
                <div v-if="form.errors.title">{{ form.errors.title }}</div>
            </div>
            <div>
                <label for="content">Conteúdo:</label>
                <textarea id="content" v-model="form.content" required></textarea>
                <div v-if="form.errors.content">{{ form.errors.content }}</div>
            </div>
            <button type="submit" :disabled="form.processing">Criar Post</button>
        </form>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
const form = useForm({
    title: '',
    content: '',
});
const submit = () => {
    form.post('/posts'); // Rota para criar um novo post
};
</script>

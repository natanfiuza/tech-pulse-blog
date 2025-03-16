<template>
    <AdminLayout>
        <h1>Novo Post</h1>
        <form @submit.prevent="submit">
            <div>
                <label for="title">Título:</label>
                <input id="title" v-model="form.title" type="text">
                <div v-if="form.errors.title">{{ form.errors.title }}</div>
            </div>
            <div>
                <label for="content">Conteúdo:</label>
                <MarkdownEditor v-model="form.content" /> </*-- Nosso componente -->*/
                <div v-if="form.errors.content">{{ form.errors.content }}</div>
            </div>
            <button type="submit" :disabled="form.processing">Criar</button>
        </form>
    </AdminLayout>
</template>

<script>
import { useForm } from '@inertiajs/inertia-vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import MarkdownEditor from '@/Components/Admin/MarkdownEditor.vue';

export default {
    components: {
        AdminLayout,
        MarkdownEditor,
    },
    setup() {
        const form = useForm({
            title: '',
            content: '',
        });

        function submit() {
            form.post(route('posts.store')); //Envia o POST
        }

        return { form, submit };
    },
};
</script>

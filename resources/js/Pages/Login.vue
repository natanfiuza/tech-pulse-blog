<template>
    <div class="container">
        <h1>Login</h1>
        <form @submit.prevent="submit">
            <div>
                <label for="email">E-mail:</label>
                <input type="email" id="email" v-model="form.email" required>
                <div v-if="form.errors.email">{{ form.errors.email }}</div>
            </div>
            <div>
                <label for="password">Senha:</label>
                <input type="password" id="password" v-model="form.password" required>
                <div v-if="form.errors.password">{{ form.errors.password }}</div>
            </div>
            <div>
                <label>
                    <input type="checkbox" v-model="form.remember"> Lembrar de mim
                </label>
            </div>
            <button type="submit" :disabled="form.processing">Entrar</button>
            <div v-if="form.recentlySuccessful">
                Login realizado com sucesso.
            </div>
        </form>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login'); //Rota de login do Laravel
};

</script>

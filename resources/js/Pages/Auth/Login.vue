<template>
  <Navbar />
  <div class="login-container">
    <header class="login-header">
      <h1>Bem-vindo de Volta</h1>
      <p>Faça login para continuar no TechPulse</p>
    </header>

    <form @submit.prevent="submit">
      <div class="form-group">
        <label for="email">E-mail</label>
        <input id="email" v-model="form.email" type="email" required autofocus />
        <div v-if="form.errors.email" class="error">{{ form.errors.email }}</div>
      </div>

      <div class="form-group">
        <label for="password">Senha</label>
        <input id="password" v-model="form.password" type="password" required />
        <div v-if="form.errors.password" class="error">{{ form.errors.password }}</div>
      </div>

      <div class="forgot-password">
        <a href="#">Esqueceu sua senha?</a>
      </div>

      <button type="submit" class="btn" :disabled="form.processing">Entrar</button>
    </form>

    <div class="social-login">
      <p>Ou faça login com:</p>
      <div class="social-buttons">
        <a href="#" class="social-btn">
          <img
            src="/assets/img/social/google_ico.png"
            alt="Google"
            title="Login com Google"
          />
        </a>
        <a href="#" class="social-btn">
          <img
            src="/assets/img/social/github_ico.png"
            alt="GitHub"
            title="Login com GitHub"
          />
        </a>
        <a href="#" class="social-btn">
          <img
            src="/assets/img/social/linkedin_ico.png"
            alt="LinkedIn"
            title="Login com LinkedIn"
          />
        </a>
      </div>
    </div>

    <div class="signup-link">Não tem uma conta? <a href="#">Cadastre-se</a></div>
  </div>
</template>

<script>
import { useForm } from "@inertiajs/inertia-vue3";
import { Head } from "@inertiajs/inertia-vue3"; // Importa o componente Head
import Navbar from "@/Components/Navbar.vue";
import "../../../css/home.css";
export default {
  setup() {
    const form = useForm({
      email: "",
      password: "",
      remember: false,
    });

    function submit() {
      form.post("/login"); // Usa a rota nomeada!
    }

    return { form, submit };
  },
};
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: Arial, sans-serif;
  line-height: 1.6;
  background-color: #f4f4f4;
  color: #333;
}

.navbar {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  background-color: #333;
  color: #fff;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 2rem;
  z-index: 1000;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.logo {
  font-size: 1.5rem;
  font-weight: bold;
  color: #fff;
  text-decoration: none;
}

.nav-links {
  display: flex;
  list-style: none;
}

.nav-links li {
  margin-left: 20px;
}

.nav-links a {
  color: #fff;
  text-decoration: none;
  transition: color 0.3s ease;
}

.nav-links a:hover {
  color: #4caf50;
}

.login-container {
  max-width: 450px;
  margin: 100px auto 2rem;
  background-color: #fff;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.login-header {
  margin-bottom: 2rem;
  border-bottom: 1px solid #eee;
  padding-bottom: 1rem;
}

.login-header h1 {
  color: #4caf50;
}

.login-form {
  display: flex;
  flex-direction: column;
}

.form-group {
  margin-bottom: 1rem;
  text-align: left;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  color: #666;
}

.form-group input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

.btn {
  background-color: #4caf50;
  color: white;
  padding: 0.75rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
  transition: background-color 0.3s ease;
  margin-top: 1rem;
}

.btn:hover {
  background-color: #45a049;
}

.forgot-password {
  text-align: right;
  margin-top: 0.5rem;
}

.forgot-password a {
  color: #4caf50;
  text-decoration: none;
  font-size: 0.9rem;
}

.signup-link {
  margin-top: 1.5rem;
  padding-top: 1rem;
  border-top: 1px solid #eee;
}

.signup-link a {
  color: #4caf50;
  text-decoration: none;
  font-weight: bold;
}

.social-login {
  margin-top: 1.5rem;
}

.social-login p {
  margin-bottom: 1rem;
  color: #666;
}

.social-buttons {
  display: flex;
  justify-content: center;
  gap: 1rem;
}

.social-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background-color: #f4f4f4;
  text-decoration: none;
  color: #333;
  transition: background-color 0.3s ease;
}

.social-btn:hover {
  background-color: #e0e0e0;
}

.social-btn img {
  width: 24px;
  height: 24px;
}

.error {
  color: red;
  font-size: 0.8em;
}
</style>

<template>
  <div class="container">
    <h1>404</h1>
    <p>Oops! Página não encontrada.</p>
    <inertia-link href="/">Voltar para a página inicial</inertia-link>
  </div>
</template>

<script>
export default {
  // Não precisa de lógica aqui (por enquanto)
};
</script>

<style scoped>
/* Estilos (os mesmos da versão Blade, adapte se necessário) */
.container {
  text-align: center;
  /* ... outros estilos ... */
}

h1 {
  font-size: 3em;
  color: #333;
}

p {
  color: #666;
}
</style>

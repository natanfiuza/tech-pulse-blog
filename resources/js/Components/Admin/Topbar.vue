<template>
  <nav class="topbar">
    <a href="/" class="logo"><img src="/assets/img/logo_natanfiuza_branco.png" /></a>
  </nav>
</template>
<style scoped>
.topbar {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  background-color: #001247;
  color: #fff;
  display: flex;
  justify-content: space-between;
  align-items: center;
  /* padding: 1rem 2rem; */
  z-index: 1000;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
  flex-direction: row;
  flex-wrap: wrap;
}
.logo {
  font-size: 1.5rem;
  font-weight: bold;
  color: #fff;
  text-decoration: none;
}

.logo img {
  margin-top: 15px;
  margin-left: 20px;
  width: 150px;
  height: 30%;
}
/* Estilos */
</style>

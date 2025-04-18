<template>
  <div class="dashboard-container">
    <!-- Barra lateral (menu) -->
    <aside class="sidebar" :class="{ collapsed: isCollapsed }">
      <div class="sidebar-header">
        <h2 v-if="!isCollapsed">Tech-Pulse</h2>
        <h2 v-else>T</h2>
      </div>
      <nav class="sidebar-nav">
        <link
          v-for="item in menuItems"
          :key="item.path"
          :to="item.path"
          class="nav-item"
          active-class="active"
        >
          <i :class="item.icon"></i>
          <span v-if="!isCollapsed">{{ item.title }}</span>
        </link>
      </nav>
      <div class="sidebar-footer">
        <button @click="toggleSidebar" class="toggle-btn">
          <i :class="isCollapsed ? 'fas fa-chevron-right' : 'fas fa-chevron-left'"></i>
        </button>
      </div>
    </aside>

    <!-- Conteúdo principal -->
    <div class="main-content">
      <!-- Barra superior -->
      <header class="topbar">
        <div class="topbar-left">
          <button @click="toggleSidebar" class="menu-toggle">
            <i class="fas fa-bars"></i>
          </button>
          <h1>{{ currentRouteName }}</h1>
        </div>
        <div class="topbar-right">
          <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Pesquisar..." />
          </div>
          <div class="user-menu">
            <div class="notifications">
              <i class="fas fa-bell"></i>
              <span class="badge">3</span>
            </div>
            <div class="user-profile">
              <img src="https://via.placeholder.com/40" alt="User" />
              <span>John Doe</span>
              <i class="fas fa-chevron-down"></i>
            </div>
          </div>
        </div>
      </header>

      <!-- Área de conteúdo -->
      <main class="content-area">
        <slot />
      </main>
    </div>
  </div>
</template>

<script>
export default {
  name: "DashboardLayout",
  data() {
    return {
      isCollapsed: false,
      menuItems: [
        { title: "Dashboard", path: "/", icon: "fas fa-home" },
        { title: "Post", path: "/admin/posts", icon: "fas fa- blog" },
        // { title: "Post", path: "/admin/posts", icon: "fas fa-users" },
      ],
    };
  },
  computed: {
    currentRouteName() {
      //   const item = this.menuItems.find((item) => item.path === this.$route.path);
      //   return item ? item.title : "Dashboard";
      return "";
    },
  },
  methods: {
    toggleSidebar() {
      this.isCollapsed = !this.isCollapsed;
    },
  },
};
</script>

<style scoped>
/* Layout principal */
.dashboard-container {
  display: flex;
  min-height: 100vh;
  background-color: #f5f7fa;
}

/* Sidebar */
.sidebar {
  width: 250px;
  background: #2c3e50;
  color: white;
  transition: all 0.3s;
  display: flex;
  flex-direction: column;
}

.sidebar.collapsed {
  width: 70px;
}

.sidebar-header {
  padding: 20px;
  text-align: center;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-nav {
  flex: 1;
  padding: 10px 0;
}

.nav-item {
  display: flex;
  align-items: center;
  padding: 12px 20px;
  color: #b3b3b3;
  text-decoration: none;
  transition: all 0.3s;
}

.nav-item i {
  margin-right: 10px;
  font-size: 18px;
}

.nav-item:hover {
  background: rgba(255, 255, 255, 0.1);
  color: white;
}

.nav-item.active {
  background: rgba(255, 255, 255, 0.1);
  color: white;
  border-left: 3px solid #42b983;
}

.sidebar.collapsed .nav-item span {
  display: none;
}

.sidebar.collapsed .nav-item i {
  margin-right: 0;
  font-size: 20px;
}

.sidebar-footer {
  padding: 15px;
  text-align: center;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.toggle-btn {
  background: none;
  border: none;
  color: white;
  cursor: pointer;
  font-size: 16px;
}

/* Main content */
.main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

/* Topbar */
.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 20px;
  height: 60px;
  background: white;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.topbar-left {
  display: flex;
  align-items: center;
}

.menu-toggle {
  background: none;
  border: none;
  font-size: 18px;
  margin-right: 15px;
  cursor: pointer;
  display: none;
}

.topbar-right {
  display: flex;
  align-items: center;
}

.search-box {
  position: relative;
  margin-right: 20px;
}

.search-box i {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  color: #999;
}

.search-box input {
  padding: 8px 15px 8px 35px;
  border: 1px solid #ddd;
  border-radius: 4px;
  outline: none;
}

.user-menu {
  display: flex;
  align-items: center;
}

.notifications {
  position: relative;
  margin-right: 20px;
  cursor: pointer;
}

.badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: #ff4757;
  color: white;
  border-radius: 50%;
  width: 18px;
  height: 18px;
  font-size: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.user-profile {
  display: flex;
  align-items: center;
  cursor: pointer;
}

.user-profile img {
  width: 35px;
  height: 35px;
  border-radius: 50%;
  margin-right: 10px;
}

/* Content area */
.content-area {
  flex: 1;
  padding: 20px;
  overflow-y: auto;
}

/* Responsividade */
@media (max-width: 768px) {
  .sidebar {
    position: fixed;
    z-index: 1000;
    height: 100vh;
    left: -250px;
  }

  .sidebar.collapsed {
    left: 0;
    width: 70px;
  }

  .sidebar:not(.collapsed) {
    left: 0;
    width: 250px;
  }

  .main-content {
    margin-left: 0;
  }

  .menu-toggle {
    display: block;
  }

  .topbar h1 {
    font-size: 18px;
  }

  .search-box {
    display: none;
  }

  .user-profile span {
    display: none;
  }
}
</style>

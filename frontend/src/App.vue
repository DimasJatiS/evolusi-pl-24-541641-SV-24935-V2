<script setup>
</script>

<template>
  <div class="app-layout">
    <header class="app-header">
      <div class="container nav-container">
        <router-link to="/" class="brand-logo">
          <span class="logo-icon">⚡</span>
          <span class="brand-text">Laravel &amp; Vue Hub</span>
        </router-link>

        <nav class="main-nav">
          <router-link to="/" class="nav-link" active-class="active" exact-active-class="active">
            Beranda
          </router-link>
          <router-link to="/posts" class="nav-link" active-class="active">
            Daftar Post
          </router-link>
        </nav>
      </div>
    </header>

    <main class="container main-content">
      <router-view />
    </main>

    <footer class="app-footer">
      <div class="container footer-content">
        <p>Evolusi Perangkat Lunak &bull; Laravel 12 + Vue 3 SPA &bull; GitHub Actions CI/CD</p>
      </div>
    </footer>
  </div>
</template>

<style>
/* Global resets & typography */
:root {
  font-family: Inter, system-ui, Avenir, Helvetica, Arial, sans-serif;
  line-height: 1.5;
  font-weight: 400;
  color-scheme: dark;
  color: #f1f5f9;
  background-color: #0b0f19;
}

body {
  margin: 0;
  min-width: 320px;
  min-height: 100vh;
}

.container {
  width: 100%;
  max-width: 1080px;
  margin: 0 auto;
  padding: 0 1.25rem;
  box-sizing: border-box;
}

.app-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.app-header {
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  background: rgba(11, 15, 25, 0.85);
  backdrop-filter: blur(12px);
  position: sticky;
  top: 0;
  z-index: 50;
}

.nav-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 64px;
}

.brand-logo {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  text-decoration: none;
  color: #f8fafc;
  font-weight: 700;
  font-size: 1.1rem;
}

.logo-icon {
  font-size: 1.4rem;
}

.main-nav {
  display: flex;
  gap: 0.5rem;
}

.nav-link {
  text-decoration: none;
  color: #94a3b8;
  padding: 0.45rem 0.9rem;
  border-radius: 6px;
  font-weight: 500;
  font-size: 0.92rem;
  transition: all 0.2s;
}

.nav-link:hover {
  color: #f8fafc;
  background: rgba(255, 255, 255, 0.06);
}

.nav-link.active {
  color: #818cf8;
  background: rgba(99, 102, 241, 0.12);
}

.main-content {
  flex: 1;
  padding-top: 2rem;
  padding-bottom: 3rem;
}

.app-footer {
  border-top: 1px solid rgba(255, 255, 255, 0.06);
  padding: 1.5rem 0;
  text-align: center;
  color: #64748b;
  font-size: 0.85rem;
}
</style>

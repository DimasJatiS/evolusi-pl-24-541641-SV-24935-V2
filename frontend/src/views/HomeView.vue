<template>
  <div class="home-view">
    <div class="hero">
      <div class="badge">Laravel 12 + Vue 3 SPA</div>
      <h1 class="hero-title">Sistem Manajemen & API Postingan</h1>
      <p class="hero-subtitle">
        Aplikasi antarmuka modern yang terhubung langsung ke backend Laravel melalui RESTful API.
      </p>

      <div class="cta-group">
        <router-link to="/posts" class="btn btn-primary">
          Jelajahi Postingan &rarr;
        </router-link>
        <a
          href="https://github.com"
          target="_blank"
          rel="noopener noreferrer"
          class="btn btn-secondary"
        >
          Dokumentasi
        </a>
      </div>
    </div>

    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon">🚀</div>
        <h3>Vue 3 & Vite</h3>
        <p>Dibangun menggunakan Single Page Application yang cepat dengan Vue Router dan Composition API.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🛡️</div>
        <h3>Laravel REST API</h3>
        <p>Endpoint JSON <code>GET /api/posts</code> aman dengan konfigurasi CORS yang tepat.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🧪</div>
        <h3>CI/CD 4 Jobs</h3>
        <p>Otomatisasi Linting, Vitest Unit Testing, Build Artifact, dan Deploy pada GitHub Actions.</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.home-view {
  display: flex;
  flex-direction: column;
  gap: 3rem;
  padding: 2rem 0;
}

.hero {
  text-align: center;
  max-width: 720px;
  margin: 0 auto;
  padding: 2rem 1rem;
}

.badge {
  display: inline-block;
  background: rgba(99, 102, 241, 0.15);
  color: #818cf8;
  font-size: 0.85rem;
  font-weight: 600;
  padding: 0.35rem 0.85rem;
  border-radius: 9999px;
  margin-bottom: 1.25rem;
  border: 1px solid rgba(99, 102, 241, 0.3);
}

.hero-title {
  font-size: 2.75rem;
  line-height: 1.2;
  font-weight: 800;
  margin-bottom: 1rem;
  background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.hero-subtitle {
  font-size: 1.15rem;
  color: #94a3b8;
  margin-bottom: 2rem;
  line-height: 1.6;
}

.cta-group {
  display: flex;
  gap: 1rem;
  justify-content: center;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s ease;
}

.btn-primary {
  background: #6366f1;
  color: #ffffff;
}

.btn-primary:hover {
  background: #4f46e5;
  transform: translateY(-2px);
}

.btn-secondary {
  background: rgba(255, 255, 255, 0.05);
  color: #e2e8f0;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.btn-secondary:hover {
  background: rgba(255, 255, 255, 0.1);
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 1.5rem;
  margin-top: 1rem;
}

.feature-card {
  background: rgba(30, 41, 59, 0.7);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 12px;
  padding: 1.75rem;
  text-align: left;
  transition: transform 0.2s ease, border-color 0.2s ease;
}

.feature-card:hover {
  transform: translateY(-4px);
  border-color: rgba(99, 102, 241, 0.4);
}

.feature-icon {
  font-size: 2rem;
  margin-bottom: 1rem;
}

.feature-card h3 {
  font-size: 1.25rem;
  margin-bottom: 0.5rem;
  color: #f8fafc;
}

.feature-card p {
  color: #94a3b8;
  font-size: 0.95rem;
  line-height: 1.5;
}

.feature-card code {
  background: rgba(0, 0, 0, 0.3);
  padding: 0.15rem 0.4rem;
  border-radius: 4px;
  font-size: 0.85rem;
  color: #38bdf8;
}
</style>

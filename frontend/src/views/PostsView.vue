<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  filterPosts,
  formatPostDate,
  truncateContent,
  getPostStats,
} from '../utils/postUtils.js'

const posts = ref([])
const loading = ref(true)
const error = ref(null)
const searchQuery = ref('')

// Mengambil URL API dari environment variable VITE_API_URL
const apiUrl = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000/api'

// --- State Form CRUD ---
const form = ref({
  title: '',
  content: '',
})
const isEditing = ref(false)
const editingId = ref(null)
const isSubmitting = ref(false)
const formError = ref(null)

// Reset form ke kondisi awal
const resetForm = () => {
  form.value = { title: '', content: '' }
  isEditing.value = false
  editingId.value = null
  formError.value = null
}

// Mulai mode Edit ketika tombol Edit di kartu diklik
const startEdit = (post) => {
  isEditing.value = true
  editingId.value = post.id
  form.value = {
    title: post.title,
    content: post.content,
  }
  formError.value = null
  // Scroll halus ke posisi form
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
// Submit Form (Menangani Create sekaligus Update)
const handleSubmit = async () => {
  formError.value = null
  if (!form.value.title.trim() || !form.value.content.trim()) {
    formError.value = 'Judul dan isi postingan tidak boleh kosong!'
    return
  }
  isSubmitting.value = true
  try {
    const isEdit = isEditing.value
    const url = isEdit ? `${apiUrl}/posts/${editingId.value}` : `${apiUrl}/posts`
    const method = isEdit ? 'PUT' : 'POST'
    const res = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify(form.value),
    })
    const data = await res.json()
    if (!res.ok) {
      throw new Error(data.message || 'Gagal menyimpan postingan.')
    }
    resetForm()
    await fetchPosts() // Refresh list otomatis
  } catch (err) {
    formError.value = err.message
  } finally {
    isSubmitting.value = false
  }
}
// Handler Hapus Postingan
const deletePost = async (id) => {
  if (!confirm('Apakah kamu yakin ingin menghapus postingan ini?')) return
  try {
    const res = await fetch(`${apiUrl}/posts/${id}`, {
      method: 'DELETE',
      headers: {
        Accept: 'application/json',
      },
    })
    if (!res.ok) throw new Error('Gagal menghapus postingan.')
    // Jika sedang mengedit post yang dihapus, batalkan edit
    if (editingId.value === id) resetForm()
    await fetchPosts()
  } catch (err) {
    alert(err.message)
  }
}

const fetchPosts = async () => {
  loading.value = true
  error.value = null

  try {
    const res = await fetch(`${apiUrl}/posts`, {
      headers: {
        Accept: 'application/json',
      },
    })

    if (!res.ok) {
      throw new Error(`Gagal memuat data dari server (HTTP ${res.status})`)
    }

    const data = await res.json()
    posts.value = Array.isArray(data) ? data : []
  } catch (err) {
    error.value = err.message || 'Terjadi kesalahan saat memanggil API.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchPosts()
})

const filteredPosts = computed(() => {
  return filterPosts(posts.value, searchQuery.value)
})

const stats = computed(() => {
  return getPostStats(posts.value)
})
</script>

<template>
  <div class="posts-view">
    <div class="header-section">
      <div>
        <h1 class="page-title">Daftar Postingan</h1>
        <p class="api-badge">
          Endpoint API: <span>{{ apiUrl }}/posts</span>
        </p>
      </div>

      <button @click="fetchPosts" class="btn-refresh" :disabled="loading">
        <span :class="{ spinning: loading }">🔄</span> Refresh
      </button>
    </div>

        <!-- Form CRUD (Create & Update) -->
    <section class="crud-form-card">
      <div class="form-header">
        <h3>{{ isEditing ? '✏️ Edit Postingan' : '➕ Tambah Postingan Baru' }}</h3>
        <span v-if="isEditing" class="editing-badge">Mode Edit: #{{ editingId }}</span>
      </div>

      <!-- Pesan Error Form -->
      <div v-if="formError" class="form-alert-error">
        {{ formError }}
      </div>

      <form @submit.prevent="handleSubmit" class="form-body">
        <div class="form-group">
          <label for="post-title">Judul Postingan</label>
          <input
            id="post-title"
            v-model="form.title"
            type="text"
            placeholder="Masukkan judul menarik..."
            class="form-input"
            :disabled="isSubmitting"
          />
        </div>

        <div class="form-group">
          <label for="post-content">Isi Konten</label>
          <textarea
            id="post-content"
            v-model="form.content"
            rows="3"
            placeholder="Tuliskan isi atau deskripsi postingan..."
            class="form-input textarea"
            :disabled="isSubmitting"
          ></textarea>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-save" :disabled="isSubmitting">
            {{ isSubmitting ? 'Menyimpan...' : (isEditing ? 'Simpan Perubahan' : 'Terbitkan Post') }}
          </button>
          <button
            v-if="isEditing"
            type="button"
            class="btn btn-cancel"
            @click="resetForm"
            :disabled="isSubmitting"
          >
            Batal
          </button>
        </div>
      </form>
    </section>

    <!-- Toolbar: Search & Stats -->
    <div class="toolbar">
      <div class="search-box">
        <span class="search-icon">🔍</span>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari postingan berdasarkan judul atau isi..."
          class="search-input"
        />
      </div>
      <div class="stats-counter">
        Total: <strong>{{ filteredPosts.length }}</strong> dari {{ stats.total }} post
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="state-container loading-state">
      <div class="spinner"></div>
      <p>Mengambil data postingan dari backend Laravel...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="state-container error-state">
      <div class="error-icon">⚠️</div>
      <h3>Gagal Menghubungi API Laravel</h3>
      <p>{{ error }}</p>
      <p class="error-hint">
        Pastikan server Laravel aktif di terminal dengan perintah:<br />
        <code>php artisan serve</code>
      </p>
      <button @click="fetchPosts" class="btn btn-retry">Coba Lagi</button>
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredPosts.length === 0" class="state-container empty-state">
      <div class="empty-icon">📭</div>
      <h3>Tidak Ada Postingan Ditemukan</h3>
      <p v-if="searchQuery">
        Tidak ada postingan yang sesuai dengan kata kunci "<em>{{ searchQuery }}</em>".
      </p>
      <p v-else>
        Belum ada data postingan di database Laravel.
      </p>
    </div>

    <!-- Post Grid -->
    <div v-else class="posts-grid">
      <article v-for="post in filteredPosts" :key="post.id" class="post-card">
        <div class="post-header">
          <span class="post-id">#{{ post.id }}</span>
          <span class="post-date">{{ formatPostDate(post.created_at) }}</span>
        </div>
        <h2 class="post-title">{{ post.title }}</h2>
        <p class="post-body">{{ truncateContent(post.content, 140) }}</p>
                <!-- Tombol Aksi CRUD -->
        <div class="post-actions">
          <button @click="startEdit(post)" class="action-btn btn-edit" title="Edit Post">
            ✏️ Edit
          </button>
          <button @click="deletePost(post.id)" class="action-btn btn-delete" title="Hapus Post">
            🗑️ Hapus
          </button>
        </div>
      </article>
    </div>
  </div>
</template>

<style scoped>
/* Styling CRUD Form Card */
.crud-form-card {
  background: rgba(30, 41, 59, 0.7);
  border: 1px solid rgba(99, 102, 241, 0.3);
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

.form-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.25rem;
}

.form-header h3 {
  margin: 0;
  font-size: 1.25rem;
  color: #f8fafc;
}

.editing-badge {
  font-size: 0.8rem;
  background: rgba(245, 158, 11, 0.15);
  color: #fbbf24;
  padding: 0.2rem 0.6rem;
  border-radius: 9999px;
  border: 1px solid rgba(245, 158, 11, 0.3);
}

.form-alert-error {
  background: rgba(239, 68, 68, 0.15);
  border: 1px solid rgba(239, 68, 68, 0.3);
  color: #fca5a5;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  font-size: 0.9rem;
  margin-bottom: 1rem;
}

.form-body {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
  text-align: left;
}

.form-group label {
  font-size: 0.88rem;
  font-weight: 500;
  color: #cbd5e1;
}

.form-input {
  width: 100%;
  box-sizing: border-box;
  padding: 0.65rem 0.9rem;
  background: rgba(15, 23, 42, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 8px;
  color: #f8fafc;
  font-size: 0.95rem;
  outline: none;
  font-family: inherit;
  transition: border-color 0.2s;
}

.form-input:focus {
  border-color: #6366f1;
}

.form-input.textarea {
  resize: vertical;
}

.form-actions {
  display: flex;
  gap: 0.75rem;
  margin-top: 0.5rem;
}

.btn-save {
  background: #6366f1;
  color: white;
  border: none;
  padding: 0.6rem 1.4rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-save:hover:not(:disabled) {
  background: #4f46e5;
}

.btn-cancel {
  background: rgba(255, 255, 255, 0.08);
  color: #e2e8f0;
  border: 1px solid rgba(255, 255, 255, 0.15);
  padding: 0.6rem 1.2rem;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-cancel:hover:not(:disabled) {
  background: rgba(255, 255, 255, 0.15);
}

/* Tombol Aksi di Kartu Post */
.post-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
  margin-top: 0.75rem;
  padding-top: 0.75rem;
  border-top: 1px solid rgba(255, 255, 255, 0.06);
}

.action-btn {
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: #cbd5e1;
  padding: 0.35rem 0.75rem;
  border-radius: 6px;
  font-size: 0.82rem;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  transition: all 0.2s;
}

.btn-edit:hover {
  background: rgba(99, 102, 241, 0.2);
  border-color: #6366f1;
  color: #a5b4fc;
}

.btn-delete:hover {
  background: rgba(239, 68, 68, 0.2);
  border-color: #ef4444;
  color: #fca5a5;
}

.posts-view {
  display: flex;
  flex-direction: column;
  gap: 1.75rem;
}

.header-section {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  flex-wrap: wrap;
  gap: 1rem;
  padding-bottom: 1.25rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.page-title {
  font-size: 2rem;
  font-weight: 700;
  color: #f8fafc;
  margin: 0 0 0.5rem 0;
}

.api-badge {
  font-size: 0.85rem;
  color: #94a3b8;
  margin: 0;
}

.api-badge span {
  font-family: monospace;
  background: rgba(99, 102, 241, 0.15);
  color: #a5b4fc;
  padding: 0.2rem 0.5rem;
  border-radius: 4px;
}

.btn-refresh {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: rgba(255, 255, 255, 0.08);
  color: #e2e8f0;
  border: 1px solid rgba(255, 255, 255, 0.15);
  padding: 0.5rem 1rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s;
}

.btn-refresh:hover:not(:disabled) {
  background: rgba(255, 255, 255, 0.15);
}

.btn-refresh:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.spinning {
  display: inline-block;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.search-box {
  position: relative;
  flex: 1;
  min-width: 260px;
}

.search-icon {
  position: absolute;
  left: 0.85rem;
  top: 50%;
  transform: translateY(-50%);
  font-size: 0.9rem;
}

.search-input {
  width: 100%;
  box-sizing: border-box;
  padding: 0.65rem 1rem 0.65rem 2.5rem;
  background: rgba(15, 23, 42, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 8px;
  color: #f8fafc;
  font-size: 0.95rem;
  outline: none;
  transition: border-color 0.2s;
}

.search-input:focus {
  border-color: #6366f1;
}

.stats-counter {
  font-size: 0.9rem;
  color: #94a3b8;
}

.stats-counter strong {
  color: #f1f5f9;
}

.state-container {
  text-align: center;
  padding: 3.5rem 1.5rem;
  background: rgba(30, 41, 59, 0.5);
  border: 1px dashed rgba(255, 255, 255, 0.15);
  border-radius: 12px;
}

.spinner {
  width: 36px;
  height: 36px;
  border: 3px solid rgba(99, 102, 241, 0.2);
  border-top-color: #6366f1;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto 1rem auto;
}

.error-state {
  border-color: rgba(239, 68, 68, 0.3);
  background: rgba(239, 68, 68, 0.05);
}

.error-icon, .empty-icon {
  font-size: 2.5rem;
  margin-bottom: 0.75rem;
}

.error-hint code {
  display: inline-block;
  background: rgba(0, 0, 0, 0.4);
  padding: 0.2rem 0.5rem;
  border-radius: 4px;
  color: #f87171;
  margin-top: 0.5rem;
}

.btn-retry {
  margin-top: 1rem;
  background: #ef4444;
  color: #fff;
  border: none;
  padding: 0.55rem 1.25rem;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
}

.posts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.25rem;
}

.post-card {
  background: rgba(30, 41, 59, 0.7);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 10px;
  padding: 1.35rem;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  transition: transform 0.2s, border-color 0.2s;
}

.post-card:hover {
  transform: translateY(-3px);
  border-color: rgba(99, 102, 241, 0.5);
}

.post-header {
  display: flex;
  justify-content: space-between;
  font-size: 0.8rem;
}

.post-id {
  color: #6366f1;
  font-weight: 700;
}

.post-date {
  color: #64748b;
}

.post-title {
  font-size: 1.15rem;
  font-weight: 600;
  color: #f8fafc;
  margin: 0;
  line-height: 1.4;
}

.post-body {
  color: #94a3b8;
  font-size: 0.92rem;
  line-height: 1.5;
  margin: 0;
}
</style>

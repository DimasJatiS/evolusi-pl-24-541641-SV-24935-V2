<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import {
  filterComments,
  formatCommentDate,
  getCommentStats,
} from '../utils/commentUtils.js'

const props = defineProps({
  postId: {
    type: Number,
    required: true,
  },
})

const comments = ref([])
const loading = ref(true)
const error = ref(null)
const searchQuery = ref('')

const apiUrl = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000/api'

// Form State
const form = ref({
  author_name: '',
  content: '',
})
const isEditing = ref(false)
const editingId = ref(null)
const isSubmitting = ref(false)
const formError = ref(null)

const resetForm = () => {
  form.value = { author_name: '', content: '' }
  isEditing.value = false
  editingId.value = null
  formError.value = null
}

const fetchComments = async () => {
  loading.value = true
  error.value = null
  try {
    const res = await fetch(`${apiUrl}/posts/${props.postId}/comments`, {
      headers: { Accept: 'application/json' },
    })
    if (!res.ok) throw new Error(`Gagal memuat komentar (HTTP ${res.status})`)
    const data = await res.json()
    comments.value = Array.isArray(data) ? data : []
  } catch (err) {
    error.value = err.message || 'Terjadi kesalahan saat memuat komentar.'
  } finally {
    loading.value = false
  }
}

const startEdit = (comment) => {
  isEditing.value = true
  editingId.value = comment.id
  form.value = {
    author_name: comment.author_name,
    content: comment.content,
  }
  formError.value = null
}

const handleSubmit = async () => {
  formError.value = null
  if (!form.value.author_name.trim() || !form.value.content.trim()) {
    formError.value = 'Nama penulis dan isi komentar wajib diisi!'
    return
  }

  isSubmitting.value = true
  try {
    const isEdit = isEditing.value
    const url = isEdit
      ? `${apiUrl}/comments/${editingId.value}`
      : `${apiUrl}/posts/${props.postId}/comments`
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
    if (!res.ok) throw new Error(data.message || 'Gagal menyimpan komentar.')

    resetForm()
    await fetchComments()
  } catch (err) {
    formError.value = err.message
  } finally {
    isSubmitting.value = false
  }
}

const deleteComment = async (id) => {
  if (!confirm('Apakah kamu yakin ingin menghapus komentar ini?')) return
  try {
    const res = await fetch(`${apiUrl}/comments/${id}`, {
      method: 'DELETE',
      headers: { Accept: 'application/json' },
    })
    if (!res.ok) throw new Error('Gagal menghapus komentar.')
    if (editingId.value === id) resetForm()
    await fetchComments()
  } catch (err) {
    alert(err.message)
  }
}

watch(() => props.postId, () => {
  fetchComments()
})

onMounted(() => {
  fetchComments()
})

const filteredComments = computed(() => filterComments(comments.value, searchQuery.value))
const stats = computed(() => getCommentStats(comments.value))
</script>

<template>
  <div class="comment-section">
    <div class="comment-header">
      <h4 class="title">💬 Komentar ({{ stats.total }})</h4>
      <button @click="fetchComments" class="btn-refresh-sm" :disabled="loading" title="Refresh komentar">
        <span :class="{ spinning: loading }">🔄</span>
      </button>
    </div>

    <!-- Form Tambah / Edit Komentar -->
    <div class="comment-form-box">
      <div class="form-title-bar">
        <span>{{ isEditing ? '✏️ Edit Komentar' : '➕ Tulis Komentar' }}</span>
        <button v-if="isEditing" @click="resetForm" class="btn-cancel-sm">Batal Edit</button>
      </div>

      <div v-if="formError" class="form-error-sm">{{ formError }}</div>

      <form @submit.prevent="handleSubmit" class="comment-form">
        <input
          v-model="form.author_name"
          type="text"
          placeholder="Nama Anda..."
          class="comment-input"
          :disabled="isSubmitting"
        />
        <textarea
          v-model="form.content"
          rows="2"
          placeholder="Tulis komentar..."
          class="comment-input comment-textarea"
          :disabled="isSubmitting"
        ></textarea>
        <div class="form-submit-row">
          <button type="submit" class="btn-submit-comment" :disabled="isSubmitting">
            {{ isSubmitting ? 'Menyimpan...' : (isEditing ? 'Simpan Revisi' : 'Kirim Komentar') }}
          </button>
        </div>
      </form>
    </div>

    <!-- Filter Komentar jika > 1 -->
    <div v-if="comments.length > 1" class="comment-search-box">
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Cari komentar..."
        class="search-comment-input"
      />
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="comment-loading">
      <div class="spinner-sm"></div> Memuat komentar...
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="comment-error">
      <span>⚠️ {{ error }}</span>
      <button @click="fetchComments" class="btn-retry-sm">Coba Lagi</button>
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredComments.length === 0" class="comment-empty">
      <span v-if="searchQuery">Tidak ada komentar cocok dengan "{{ searchQuery }}".</span>
      <span v-else>Belum ada komentar pada post ini. Jadilah yang pertama berkomentar!</span>
    </div>

    <!-- List Komentar -->
    <div v-else class="comments-list">
      <div v-for="comment in filteredComments" :key="comment.id" class="comment-item">
        <div class="comment-item-header">
          <span class="comment-author">👤 {{ comment.author_name }}</span>
          <span class="comment-date">{{ formatCommentDate(comment.created_at) }}</span>
        </div>
        <p class="comment-text">{{ comment.content }}</p>
        <div class="comment-item-actions">
          <button @click="startEdit(comment)" class="btn-action-sm edit" title="Edit">✏️</button>
          <button @click="deleteComment(comment.id)" class="btn-action-sm delete" title="Hapus">🗑️</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.comment-section {
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
}

.comment-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
}

.comment-header .title {
  margin: 0;
  font-size: 1rem;
  color: #e2e8f0;
}

.btn-refresh-sm {
  background: transparent;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  font-size: 0.85rem;
}

.comment-form-box {
  background: rgba(15, 23, 42, 0.5);
  border: 1px solid rgba(99, 102, 241, 0.2);
  border-radius: 8px;
  padding: 0.75rem;
  margin-bottom: 1rem;
}

.form-title-bar {
  display: flex;
  justify-content: space-between;
  font-size: 0.8rem;
  color: #a5b4fc;
  margin-bottom: 0.5rem;
}

.btn-cancel-sm {
  background: transparent;
  border: none;
  color: #f87171;
  font-size: 0.75rem;
  cursor: pointer;
}

.form-error-sm {
  color: #fca5a5;
  font-size: 0.75rem;
  margin-bottom: 0.5rem;
}

.comment-form {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.comment-input {
  width: 100%;
  box-sizing: border-box;
  padding: 0.45rem 0.65rem;
  background: rgba(30, 41, 59, 0.7);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 6px;
  color: #f8fafc;
  font-size: 0.85rem;
  outline: none;
}

.comment-textarea {
  resize: vertical;
  font-family: inherit;
}

.form-submit-row {
  display: flex;
  justify-content: flex-end;
}

.btn-submit-comment {
  background: #6366f1;
  color: white;
  border: none;
  padding: 0.4rem 0.9rem;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
}

.btn-submit-comment:hover:not(:disabled) {
  background: #4f46e5;
}

.comment-search-box {
  margin-bottom: 0.75rem;
}

.search-comment-input {
  width: 100%;
  box-sizing: border-box;
  padding: 0.35rem 0.6rem;
  background: rgba(15, 23, 42, 0.4);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 6px;
  color: #cbd5e1;
  font-size: 0.8rem;
}

.comment-loading, .comment-empty, .comment-error {
  font-size: 0.82rem;
  color: #94a3b8;
  padding: 0.75rem;
  text-align: center;
}

.spinner-sm {
  display: inline-block;
  width: 12px;
  height: 12px;
  border: 2px solid rgba(99, 102, 241, 0.3);
  border-top-color: #6366f1;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.btn-retry-sm {
  background: #ef4444;
  color: white;
  border: none;
  padding: 0.2rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  margin-left: 0.5rem;
  cursor: pointer;
}

.comments-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.comment-item {
  background: rgba(15, 23, 42, 0.4);
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 6px;
  padding: 0.65rem 0.75rem;
  position: relative;
}

.comment-item-header {
  display: flex;
  justify-content: space-between;
  font-size: 0.78rem;
  margin-bottom: 0.35rem;
}

.comment-author {
  font-weight: 600;
  color: #818cf8;
}

.comment-date {
  color: #64748b;
  font-size: 0.72rem;
}

.comment-text {
  margin: 0;
  font-size: 0.85rem;
  color: #cbd5e1;
  line-height: 1.4;
  white-space: pre-line;
}

.comment-item-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.25rem;
  margin-top: 0.35rem;
}

.btn-action-sm {
  background: transparent;
  border: none;
  font-size: 0.75rem;
  cursor: pointer;
  opacity: 0.7;
  padding: 0.1rem 0.3rem;
  border-radius: 4px;
}

.btn-action-sm:hover {
  opacity: 1;
  background: rgba(255, 255, 255, 0.1);
}
</style>

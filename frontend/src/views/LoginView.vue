<script setup>
import { ref, reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import apiClient from '../api/axios'

const router = useRouter()
const route = useRoute()

// 1. Reactive state untuk form credentials
const form = reactive({
  email: '',
  password: '',
})

// 2. UI State management
const isLoading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const showPassword = ref(false)
const fieldErrors = reactive({
  email: '',
  password: '',
})

// 3. Helper untuk mereset pesan error
const clearErrors = () => {
  errorMessage.value = ''
  fieldErrors.email = ''
  fieldErrors.password = ''
}

// 4. Validasi client-side sebelum mengirim request
const validateForm = () => {
  clearErrors()
  let isValid = true

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!form.email.trim()) {
    fieldErrors.email = 'Email wajib diisi.'
    isValid = false
  } else if (!emailRegex.test(form.email)) {
    fieldErrors.email = 'Format email tidak valid.'
    isValid = false
  }

  if (!form.password) {
    fieldErrors.password = 'Password wajib diisi.'
    isValid = false
  } else if (form.password.length < 6) {
    fieldErrors.password = 'Password minimal 6 karakter.'
    isValid = false
  }

  return isValid
}

// 5. Handler submit login dengan implementasi best-practice
const handleLogin = async () => {
  if (!validateForm() || isLoading.value) return

  isLoading.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    const response = await apiClient.post('/login', {
      email: form.email.trim(),
      password: form.password,
    })

    const { token, user } = response.data

    // Simpan token & data user ke localStorage
    if (token) {
      localStorage.setItem('auth_token', token)
    }
    if (user) {
      localStorage.setItem('user', JSON.stringify(user))
    }

    successMessage.value = 'Login berhasil! Mengalihkan...'

    // Redirect ke halaman tujuan atau ke /posts setelah delay singkat
    const redirectPath = route.query.redirect || '/posts'
    setTimeout(() => {
      router.push(redirectPath)
    }, 800)
  } catch (err) {
    // Tangani respons error HTTP dengan ramah pengguna
    if (err.response) {
      const status = err.response.status
      const data = err.response.data

      if (status === 422 && data.errors) {
        // Validation error dari Laravel
        fieldErrors.email = data.errors.email ? data.errors.email[0] : ''
        fieldErrors.password = data.errors.password ? data.errors.password[0] : ''
        errorMessage.value = data.message || 'Mohon periksa kembali isian formulir.'
      } else if (status === 401) {
        // Unauthorized (email/password salah)
        errorMessage.value = data.message || 'Email atau password salah.'
      } else {
        errorMessage.value = data.message || `Terjadi kesalahan (Kode: ${status}).`
      }
    } else if (err.request) {
      // Masalah jaringan atau backend tidak aktif
      errorMessage.value = 'Gagal terhubung ke server backend. Pastikan server API aktif.'
    } else {
      errorMessage.value = err.message || 'Terjadi kesalahan sistem yang tidak terduga.'
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="login-wrapper">
    <div class="login-card">
      <div class="login-header">
        <div class="login-icon">🔐</div>
        <h1 class="login-title">Masuk ke Akun</h1>
        <p class="login-subtitle">Masukkan kredensial Anda untuk mengelola postingan &amp; komentar</p>
      </div>

      <!-- Alert Banner: Error -->
      <div v-if="errorMessage" class="alert alert-error" role="alert">
        <span class="alert-icon">⚠️</span>
        <div class="alert-text">{{ errorMessage }}</div>
      </div>

      <!-- Alert Banner: Success -->
      <div v-if="successMessage" class="alert alert-success" role="status">
        <span class="alert-icon">✅</span>
        <div class="alert-text">{{ successMessage }}</div>
      </div>

      <form @submit.prevent="handleLogin" class="login-form" novalidate>
        <!-- Email Input -->
        <div class="form-group">
          <label for="email" class="form-label">Alamat Email</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            class="form-input"
            :class="{ 'has-error': fieldErrors.email }"
            placeholder="nama@email.com"
            autocomplete="email"
            :disabled="isLoading"
            @input="fieldErrors.email = ''"
            required
          />
          <span v-if="fieldErrors.email" class="error-text">
            {{ fieldErrors.email }}
          </span>
        </div>

        <!-- Password Input with Toggle -->
        <div class="form-group">
          <div class="password-label-row">
            <label for="password" class="form-label">Password</label>
          </div>
          <div class="password-input-wrapper">
            <input
              id="password"
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              class="form-input password-input"
              :class="{ 'has-error': fieldErrors.password }"
              placeholder="••••••••"
              autocomplete="current-password"
              :disabled="isLoading"
              @input="fieldErrors.password = ''"
              required
            />
            <button
              type="button"
              class="toggle-password-btn"
              @click="showPassword = !showPassword"
              :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'"
              tabindex="-1"
            >
              {{ showPassword ? '🙈' : '👁️' }}
            </button>
          </div>
          <span v-if="fieldErrors.password" class="error-text">
            {{ fieldErrors.password }}
          </span>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          class="btn-submit"
          :disabled="isLoading"
        >
          <span v-if="isLoading" class="spinner"></span>
          <span>{{ isLoading ? 'Memproses...' : 'Masuk Sekarang' }}</span>
        </button>
      </form>

      <div class="login-footer">
        <p class="footer-note">
          Menggunakan autentikasi aman <strong>Laravel Sanctum Bearer Token</strong>.
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.login-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem 1rem;
}

.login-card {
  width: 100%;
  max-width: 440px;
  background: #141b2d;
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 14px;
  padding: 2.25rem;
  box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5);
}

.login-header {
  text-align: center;
  margin-bottom: 1.75rem;
}

.login-icon {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
}

.login-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #f8fafc;
  margin: 0 0 0.5rem 0;
}

.login-subtitle {
  font-size: 0.88rem;
  color: #94a3b8;
  margin: 0;
  line-height: 1.4;
}

/* Alert Styling */
.alert {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  padding: 0.85rem 1rem;
  border-radius: 8px;
  font-size: 0.88rem;
  margin-bottom: 1.25rem;
  line-height: 1.4;
}

.alert-error {
  background: rgba(239, 68, 68, 0.12);
  border: 1px solid rgba(239, 68, 68, 0.3);
  color: #fca5a5;
}

.alert-success {
  background: rgba(34, 197, 94, 0.12);
  border: 1px solid rgba(34, 197, 94, 0.3);
  color: #86efac;
}

.alert-icon {
  font-size: 1.1rem;
  line-height: 1;
}

/* Form Styling */
.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

.form-label {
  font-size: 0.88rem;
  font-weight: 500;
  color: #cbd5e1;
}

.form-input {
  width: 100%;
  padding: 0.7rem 0.9rem;
  background: #0b0f19;
  border: 1px solid #334155;
  border-radius: 8px;
  color: #f8fafc;
  font-size: 0.95rem;
  box-sizing: border-box;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: #818cf8;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
}

.form-input.has-error {
  border-color: #ef4444;
}

.password-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.password-input {
  padding-right: 2.75rem;
}

.toggle-password-btn {
  position: absolute;
  right: 0.6rem;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.3rem;
  font-size: 1rem;
  color: #94a3b8;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.toggle-password-btn:hover {
  color: #f8fafc;
}

.error-text {
  font-size: 0.8rem;
  color: #f87171;
}

.btn-submit {
  width: 100%;
  padding: 0.8rem;
  background: #6366f1;
  color: #ffffff;
  border: none;
  border-radius: 8px;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
  transition: background-color 0.2s, transform 0.1s;
  margin-top: 0.5rem;
}

.btn-submit:hover:not(:disabled) {
  background: #4f46e5;
}

.btn-submit:active:not(:disabled) {
  transform: scale(0.99);
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Spinner Animation */
.spinner {
  width: 1rem;
  height: 1rem;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #ffffff;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.login-footer {
  margin-top: 1.5rem;
  text-align: center;
  border-top: 1px solid rgba(255, 255, 255, 0.06);
  padding-top: 1rem;
}

.footer-note {
  font-size: 0.78rem;
  color: #64748b;
  margin: 0;
}
</style>

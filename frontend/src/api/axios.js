import axios from 'axios'

// 1. Inisialisasi Axios instance dengan base URL dari environment variable
const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000/api',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
  timeout: 10000, // Timeout 10 detik
})

// 2. Request Interceptor: Otomatis sisipkan Bearer Token di setiap request
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => Promise.reject(error)
)

// 3. Response Interceptor: Penanganan response & error global (misal 401 Unauthorized)
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response && error.response.status === 401) {
      // Hapus token lokal jika server menolak token (sudah kedaluwarsa / invalid)
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      // Catatan: Jika ingin redirect otomatis ke halaman login, bisa trigger event atau router
    }
    return Promise.reject(error)
  }
)

export default apiClient

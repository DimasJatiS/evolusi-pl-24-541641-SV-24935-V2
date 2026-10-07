/**
 * Memfilter daftar postingan berdasarkan kata kunci pencarian (judul atau isi).
 *
 * @param {Array} posts
 * @param {string} query
 * @returns {Array}
 */
export function filterPosts(posts, query) {
  if (!Array.isArray(posts)) return []
  if (!query || typeof query !== 'string' || query.trim() === '') {
    return posts
  }

  const normalizedQuery = query.trim().toLowerCase()
  return posts.filter((post) => {
    const title = (post.title || '').toLowerCase()
    const content = (post.content || '').toLowerCase()
    return title.includes(normalizedQuery) || content.includes(normalizedQuery)
  })
}

/**
 * Format string tanggal ISO menjadi teks yang mudah dibaca.
 *
 * @param {string} dateString
 * @returns {string}
 */
export function formatPostDate(dateString) {
  if (!dateString) return '-'
  const date = new Date(dateString)
  if (isNaN(date.getTime())) return '-'

  return date.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
}

/**
 * Memotong konten teks agar pas untuk tampilan ringkasan kartu.
 *
 * @param {string} text
 * @param {number} maxLength
 * @returns {string}
 */
export function truncateContent(text, maxLength = 120) {
  if (!text || typeof text !== 'string') return ''
  if (text.length <= maxLength) return text
  return text.substring(0, maxLength).trim() + '...'
}

/**
 * Menghitung statistik dasar dari koleksi postingan.
 *
 * @param {Array} posts
 * @returns {{ total: number, hasPosts: boolean }}
 */
export function getPostStats(posts) {
  const list = Array.isArray(posts) ? posts : []
  return {
    total: list.length,
    hasPosts: list.length > 0,
  }
}

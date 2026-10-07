/**
 * Memfilter daftar komentar berdasarkan kata kunci pencarian (nama penulis atau isi).
 *
 * @param {Array} comments
 * @param {string} query
 * @returns {Array}
 */
export function filterComments(comments, query) {
  if (!Array.isArray(comments)) return []
  if (!query || typeof query !== 'string' || query.trim() === '') {
    return comments
  }

  const normalizedQuery = query.trim().toLowerCase()
  return comments.filter((comment) => {
    const author = (comment.author_name || '').toLowerCase()
    const content = (comment.content || '').toLowerCase()
    return author.includes(normalizedQuery) || content.includes(normalizedQuery)
  })
}

/**
 * Format string tanggal ISO komentar menjadi teks tanggal & waktu yang ramah pengguna.
 *
 * @param {string} dateString
 * @returns {string}
 */
export function formatCommentDate(dateString) {
  if (!dateString) return '-'
  const date = new Date(dateString)
  if (isNaN(date.getTime())) return '-'

  return date.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

/**
 * Menghitung statistik komentar (total dan status keberadaan komentar).
 *
 * @param {Array} comments
 * @returns {{ total: number, hasComments: boolean }}
 */
export function getCommentStats(comments) {
  const list = Array.isArray(comments) ? comments : []
  return {
    total: list.length,
    hasComments: list.length > 0,
  }
}

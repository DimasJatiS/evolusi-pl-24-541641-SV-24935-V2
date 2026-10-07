import { describe, it, expect } from 'vitest'
import {
  filterComments,
  formatCommentDate,
  getCommentStats,
} from './commentUtils.js'

describe('commentUtils Unit Tests', () => {
  const sampleComments = [
    { id: 1, author_name: 'Andi', content: 'Sangat bagus artikel ini' },
    { id: 2, author_name: 'Budi', content: 'Terima kasih informasinya' },
    { id: 3, author_name: 'Citra', content: 'Sangat membantu dalam belajar Laravel' },
  ]

  describe('filterComments', () => {
    it('mengembalikan semua komentar jika query kosong', () => {
      const result = filterComments(sampleComments, '')
      expect(result).toHaveLength(3)
    })

    it('memfilter komentar berdasarkan nama penulis (case-insensitive)', () => {
      const result = filterComments(sampleComments, 'andi')
      expect(result).toHaveLength(1)
      expect(result[0].id).toBe(1)
    })

    it('memfilter komentar berdasarkan isi konten', () => {
      const result = filterComments(sampleComments, 'Laravel')
      expect(result).toHaveLength(1)
      expect(result[0].id).toBe(3)
    })

    it('mengembalikan array kosong jika tidak ada yang cocok', () => {
      const result = filterComments(sampleComments, 'NonExistentKey')
      expect(result).toEqual([])
    })

    it('menangani input array null atau bukan array secara aman', () => {
      expect(filterComments(null, 'andi')).toEqual([])
      expect(filterComments(undefined, 'andi')).toEqual([])
    })
  })

  describe('formatCommentDate', () => {
    it('memformat tanggal ISO string dengan benar', () => {
      const formatted = formatCommentDate('2026-09-23T10:00:00Z')
      expect(formatted).toMatch(/2026/)
    })

    it('mengembalikan tanda "-" untuk string null atau tidak valid', () => {
      expect(formatCommentDate(null)).toBe('-')
      expect(formatCommentDate('invalid-date')).toBe('-')
    })
  })

  describe('getCommentStats', () => {
    it('menghitung total komentar dengan benar', () => {
      const stats = getCommentStats(sampleComments)
      expect(stats.total).toBe(3)
      expect(stats.hasComments).toBe(true)
    })

    it('menangani list kosong', () => {
      const stats = getCommentStats([])
      expect(stats.total).toBe(0)
      expect(stats.hasComments).toBe(false)
    })
  })
})

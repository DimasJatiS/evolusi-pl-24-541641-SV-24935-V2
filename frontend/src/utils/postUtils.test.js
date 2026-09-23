import { describe, it, expect } from 'vitest'
import {
  filterPosts,
  formatPostDate,
  truncateContent,
  getPostStats,
} from './postUtils.js'

describe('postUtils Unit Tests', () => {
  const samplePosts = [
    { id: 1, title: 'Belajar Vue 3', content: 'Panduan lengkap Composition API' },
    { id: 2, title: 'Tutorial Laravel 12', content: 'Membuat RESTful API yang aman' },
    { id: 3, title: 'CI/CD dengan GitHub Actions', content: 'Otomatisasi pengujian dan deployment' },
  ]

  describe('filterPosts', () => {
    it('mengembalikan semua post jika query kosong', () => {
      const result = filterPosts(samplePosts, '')
      expect(result).toHaveLength(3)
    })

    it('memfilter post berdasarkan judul (case-insensitive)', () => {
      const result = filterPosts(samplePosts, 'vue')
      expect(result).toHaveLength(1)
      expect(result[0].id).toBe(1)
    })

    it('memfilter post berdasarkan isi konten', () => {
      const result = filterPosts(samplePosts, 'RESTful')
      expect(result).toHaveLength(1)
      expect(result[0].id).toBe(2)
    })

    it('mengembalikan array kosong jika tidak ada yang cocok', () => {
      const result = filterPosts(samplePosts, 'Python Django')
      expect(result).toEqual([])
    })

    it('menangani input array null atau bukan array secara aman', () => {
      expect(filterPosts(null, 'vue')).toEqual([])
      expect(filterPosts(undefined, 'vue')).toEqual([])
    })
  })

  describe('formatPostDate', () => {
    it('memformat tanggal ISO string dengan benar', () => {
      const formatted = formatPostDate('2026-09-23T10:00:00Z')
      expect(formatted).toMatch(/2026/)
    })

    it('mengembalikan tanda "-" untuk string null atau tidak valid', () => {
      expect(formatPostDate(null)).toBe('-')
      expect(formatPostDate('tanggal-tidak-valid')).toBe('-')
    })
  })

  describe('truncateContent', () => {
    it('tidak memotong jika teks lebih pendek dari batas maksimal', () => {
      expect(truncateContent('Halo Dunia', 20)).toBe('Halo Dunia')
    })

    it('memotong teks dan menambahkan tanda elipsis (...) jika melebihi batas', () => {
      const longText = 'Ini adalah kalimat yang sangat panjang sekali melebihi batas'
      const truncated = truncateContent(longText, 20)
      expect(truncated.endsWith('...')).toBe(true)
      expect(truncated.length).toBeLessThanOrEqual(23) // 20 + 3 dots
    })

    it('menangani input string kosong atau null', () => {
      expect(truncateContent(null)).toBe('')
      expect(truncateContent('')).toBe('')
    })
  })

  describe('getPostStats', () => {
    it('menghitung total post dengan benar', () => {
      const stats = getPostStats(samplePosts)
      expect(stats.total).toBe(3)
      expect(stats.hasPosts).toBe(true)
    })

    it('menangani list kosong', () => {
      const stats = getPostStats([])
      expect(stats.total).toBe(0)
      expect(stats.hasPosts).toBe(false)
    })
  })
})

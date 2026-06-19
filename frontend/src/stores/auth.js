import { defineStore } from 'pinia'
import api from '@/api/axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
  },
  actions: {
    async login(email, password) {
      try {
        const response = await api.post('/login', { email, password })
        const { token, user } = response.data
        this.token = token
        this.user = user
        localStorage.setItem('token', token)
        return true
      } catch (error) {
        throw error.response?.data?.message || 'Ошибка входа'
      }
    },
    async logout() {
      try {
        await api.post('/logout')
      } finally {
        this.token = null
        this.user = null
        localStorage.removeItem('token')
      }
    },
    async fetchUser() {
      if (!this.token) return
      try {
        const response = await api.get('/user')
        this.user = response.data
      } catch {
        this.logout()
      }
    },
  },
})
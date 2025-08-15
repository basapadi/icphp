import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: localStorage.getItem('user') || null,
    token: localStorage.getItem('token') || null
  }),
  actions: {
    async login(username, password) {
      try {
        const resp = await axios.post('/auth/login', {
          username,
          password
        })
        this.token = resp.data.access_token
        this.user = resp.data.user || null

        localStorage.setItem('token', this.token)
        localStorage.setItem('user', this.user)
        return true
      } catch (err) {
        throw err
      }
    },
    async fetchUser() {
      if (!this.token) return
      try {
        const resp = await axios.get('/auth/me', {
          headers: {
            Authorization: `Bearer ${this.token}`
          }
        })
          this.user = resp.data
      } catch {
        this.logout()
      }
    },
      async logout() {
        if (!this.token) return
        this.user = null
        this.token = null
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        await axios.post('/auth/logout', {
          headers: {
            Authorization: `Bearer ${this.token}`
          }
        })
    }
  }
})

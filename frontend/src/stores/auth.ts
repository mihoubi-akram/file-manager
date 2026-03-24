import { computed, ref } from 'vue'
import { defineStore } from 'pinia'
import http, { getCsrfCookie } from '@/api/http'
import type { LoginPayload, RegisterPayload, User } from '@/types/auth'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)

  const isAuthenticated = computed(() => user.value !== null)

  async function fetchUser(): Promise<void> {
    try {
      const { data } = await http.get<User>('/api/user')
      user.value = data
    } catch {
      user.value = null
    }
  }

  async function login(payload: LoginPayload): Promise<void> {
    await getCsrfCookie()
    const { data } = await http.post<User>('/api/login', payload)
    user.value = data
  }

  async function register(payload: RegisterPayload): Promise<void> {
    await getCsrfCookie()
    const { data } = await http.post<User>('/api/register', payload)
    user.value = data
  }

  async function logout(): Promise<void> {
    await http.post('/api/logout')
    user.value = null
  }

  return { user, isAuthenticated, fetchUser, login, register, logout }
})

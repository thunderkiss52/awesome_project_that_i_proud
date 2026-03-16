import { computed, ref } from 'vue'
import { defineStore } from 'pinia'
import http, { TOKEN_KEY } from '../api/http'

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem(TOKEN_KEY))
  const user = ref(null)
  const isLoading = ref(false)
  const isReady = ref(false)

  const isAuthenticated = computed(() => Boolean(token.value))

  function setSession(nextToken, nextUser = null) {
    token.value = nextToken
    user.value = nextUser

    if (nextToken) {
      localStorage.setItem(TOKEN_KEY, nextToken)
    } else {
      localStorage.removeItem(TOKEN_KEY)
    }
  }

  function clearSession() {
    setSession(null, null)
  }

  async function restoreSession() {
    if (!token.value) {
      isReady.value = true
      return
    }

    try {
      await fetchMe()
    } catch (error) {
      clearSession()
    } finally {
      isReady.value = true
    }
  }

  async function register(payload) {
    isLoading.value = true

    try {
      const { data } = await http.post('/auth/register', payload)
      setSession(data.token, data.user)
      return data
    } finally {
      isLoading.value = false
    }
  }

  async function login(payload) {
    isLoading.value = true

    try {
      const { data } = await http.post('/auth/login', payload)
      setSession(data.token, data.user)
      return data
    } finally {
      isLoading.value = false
    }
  }

  async function fetchMe() {
    const { data } = await http.get('/auth/me')
    user.value = data.user
    return data.user
  }

  async function logout() {
    try {
      if (token.value) {
        await http.post('/auth/logout')
      }
    } finally {
      clearSession()
    }
  }

  return {
    user,
    token,
    isLoading,
    isReady,
    isAuthenticated,
    register,
    login,
    fetchMe,
    logout,
    restoreSession,
    clearSession,
  }
})

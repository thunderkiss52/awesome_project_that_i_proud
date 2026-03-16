<script setup>
import { reactive, ref } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const errorMessage = ref('')

const form = reactive({
  email: '',
  password: '',
})

async function handleSubmit() {
  errorMessage.value = ''

  try {
    await authStore.login(form)
    router.push({ name: 'dashboard' })
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Unable to sign in.'
  }
}
</script>

<template>
  <div class="auth-page">
    <section class="auth-card auth-card--accent">
      <p class="eyebrow">Portfolio project</p>
      <h1>Secure meeting booking service</h1>
      <p>
        JWT auth, subscription limits, booking conflict checks and a focused Vue dashboard for demo use.
      </p>
    </section>

    <section class="auth-card">
      <p class="eyebrow">Sign in</p>
      <h2>Welcome back</h2>

      <div v-if="errorMessage" class="feedback feedback--error">{{ errorMessage }}</div>

      <label class="field">
        <span>Email</span>
        <input v-model="form.email" type="email" placeholder="mikhail@example.com" />
      </label>

      <label class="field">
        <span>Password</span>
        <input v-model="form.password" type="password" placeholder="secret123" />
      </label>

      <button class="primary-button auth-button" type="button" :disabled="authStore.isLoading" @click="handleSubmit">
        {{ authStore.isLoading ? 'Signing in...' : 'Sign in' }}
      </button>

      <p class="auth-link">
        No account yet?
        <RouterLink to="/register">Create one</RouterLink>
      </p>
    </section>
  </div>
</template>

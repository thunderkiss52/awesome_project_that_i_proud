<script setup>
import { reactive, ref } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const errorMessage = ref('')

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

async function handleSubmit() {
  errorMessage.value = ''

  try {
    await authStore.register(form)
    router.push({ name: 'dashboard' })
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Unable to register.'
  }
}
</script>

<template>
  <div class="auth-page">
    <section class="auth-card auth-card--accent">
      <p class="eyebrow">Subscription-aware API</p>
      <h1>Create a demo account</h1>
      <p>
        Start on the Basic plan and manage meetings with validation, rate limiting and ownership checks.
      </p>
    </section>

    <section class="auth-card">
      <p class="eyebrow">Register</p>
      <h2>Create account</h2>

      <div v-if="errorMessage" class="feedback feedback--error">{{ errorMessage }}</div>

      <label class="field">
        <span>Name</span>
        <input v-model="form.name" type="text" placeholder="Mikhail" />
      </label>

      <label class="field">
        <span>Email</span>
        <input v-model="form.email" type="email" placeholder="mikhail@example.com" />
      </label>

      <label class="field">
        <span>Password</span>
        <input v-model="form.password" type="password" placeholder="secret123" />
      </label>

      <label class="field">
        <span>Confirm password</span>
        <input v-model="form.password_confirmation" type="password" placeholder="secret123" />
      </label>

      <button class="primary-button auth-button" type="button" :disabled="authStore.isLoading" @click="handleSubmit">
        {{ authStore.isLoading ? 'Creating account...' : 'Create account' }}
      </button>

      <p class="auth-link">
        Already have an account?
        <RouterLink to="/login">Sign in</RouterLink>
      </p>
    </section>
  </div>
</template>

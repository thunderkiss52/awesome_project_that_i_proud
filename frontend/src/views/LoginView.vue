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
    <section class="auth-card auth-card--accent auth-showcase">
      <div class="auth-showcase__copy">
        <p class="eyebrow">Portfolio project</p>
        <h1>Сервис бронирования встреч с подачей уровня showcase.</h1>
        <p>
          Backend-архитектура, JWT, бизнес-ограничения, Docker и SPA-интерфейс, который не стыдно положить в портфолио.
        </p>
      </div>

      <div class="auth-metrics">
        <article>
          <strong>Laravel 11</strong>
          <span>REST API, events, validation</span>
        </article>
        <article>
          <strong>Vue 3</strong>
          <span>Pinia, Router, Axios, UX-first forms</span>
        </article>
        <article>
          <strong>JWT + Limits</strong>
          <span>Авторизация, лимиты тарифов, conflict checks</span>
        </article>
      </div>

      <div class="demo-credentials">
        <small>Демо-аккаунты</small>
        <strong>basic@example.com / password123</strong>
        <strong>premium@example.com / password123</strong>
      </div>
    </section>

    <section class="auth-card auth-form-card">
      <p class="eyebrow">Вход</p>
      <h2>Добро пожаловать</h2>

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
        {{ authStore.isLoading ? 'Входим...' : 'Войти' }}
      </button>

      <p class="auth-link">
        Нет аккаунта?
        <RouterLink to="/register">Создать</RouterLink>
      </p>
    </section>
  </div>
</template>

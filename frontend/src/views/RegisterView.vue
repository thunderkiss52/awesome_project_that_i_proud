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
    <section class="auth-card auth-card--accent auth-showcase">
      <div class="auth-showcase__copy">
        <p class="eyebrow">Subscription-aware API</p>
        <h1>Создайте аккаунт и протестируйте полный сценарий продукта.</h1>
        <p>
          Базовый тариф назначается автоматически, а дальше интерфейс показывает лимиты, активные встречи и ограничения по расписанию.
        </p>
      </div>

      <div class="auth-metrics">
        <article>
          <strong>Basic</strong>
          <span>До 3 активных бронирований</span>
        </article>
        <article>
          <strong>Premium</strong>
          <span>До 20 активных бронирований</span>
        </article>
        <article>
          <strong>Conflict-safe</strong>
          <span>Запрет пересечений и чужого доступа</span>
        </article>
      </div>
    </section>

    <section class="auth-card auth-form-card">
      <p class="eyebrow">Регистрация</p>
      <h2>Создать аккаунт</h2>

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
        {{ authStore.isLoading ? 'Создаём аккаунт...' : 'Создать аккаунт' }}
      </button>

      <p class="auth-link">
        Уже есть аккаунт?
        <RouterLink to="/login">Войти</RouterLink>
      </p>
    </section>
  </div>
</template>

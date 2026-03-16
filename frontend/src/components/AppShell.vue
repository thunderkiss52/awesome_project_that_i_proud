<script setup>
defineProps({
  user: {
    type: Object,
    default: null,
  },
  summary: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['logout'])
</script>

<template>
  <div class="shell">
    <aside class="shell__sidebar">
      <div class="sidebar-top">
        <p class="eyebrow">Meeting Booking Service</p>
        <h1>Панель, которая выглядит как продукт, а не как тестовое задание.</h1>
        <p class="shell__copy">
          Laravel API с JWT и доменной логикой под капотом, Vue SPA с ясным UX и аккуратной подачей на уровне портфолио.
        </p>
      </div>

      <div class="sidebar-pills">
        <span class="sidebar-pill">JWT auth</span>
        <span class="sidebar-pill">Rate limiting</span>
        <span class="sidebar-pill">Events</span>
        <span class="sidebar-pill">Vue SPA</span>
      </div>

      <div class="profile-card" v-if="user">
        <span class="profile-card__label">Пользователь</span>
        <strong>{{ user.name }}</strong>
        <span>{{ user.email }}</span>
        <div class="profile-card__meta" v-if="summary?.subscription">
          <div>
            <small>Тариф</small>
            <strong>{{ summary.subscription.name }}</strong>
          </div>
          <div>
            <small>Свободно</small>
            <strong>{{ summary.remaining_slots }}</strong>
          </div>
        </div>
      </div>

      <div class="sidebar-feature-grid">
        <article class="sidebar-feature">
          <small>API-first</small>
          <strong>JSON errors, requests, resources</strong>
        </article>
        <article class="sidebar-feature">
          <small>Business rules</small>
          <strong>Conflict detection and plan limits</strong>
        </article>
      </div>
    </aside>

    <main class="shell__main">
      <header class="shell__header">
        <div>
          <p class="eyebrow">Workspace</p>
          <h2>Управление встречами</h2>
        </div>

        <button class="ghost-button" type="button" @click="emit('logout')">
          Выйти
        </button>
      </header>

      <slot />
    </main>
  </div>
</template>

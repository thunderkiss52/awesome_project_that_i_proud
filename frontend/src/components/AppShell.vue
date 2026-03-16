<script setup>
defineProps({
  user: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['logout'])
</script>

<template>
  <div class="shell">
    <aside class="shell__sidebar">
      <p class="eyebrow">Booking Service</p>
      <h1>Control panel for meetings and subscription limits.</h1>
      <p class="shell__copy">
        Track future meetings, keep within plan constraints and manage every booking from one place.
      </p>

      <div class="profile-card" v-if="user">
        <span class="profile-card__label">Signed in as</span>
        <strong>{{ user.name }}</strong>
        <span>{{ user.email }}</span>
      </div>
    </aside>

    <main class="shell__main">
      <header class="shell__header">
        <div>
          <p class="eyebrow">Workspace</p>
          <h2>Dashboard</h2>
        </div>

        <button class="ghost-button" type="button" @click="emit('logout')">
          Log out
        </button>
      </header>

      <slot />
    </main>
  </div>
</template>

<script setup>
defineProps({
  open: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: '',
  },
  subtitle: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['close'])
</script>

<template>
  <transition name="drawer-fade">
    <div v-if="open" class="drawer-overlay" @click.self="emit('close')">
      <transition name="drawer-slide">
        <aside v-if="open" class="drawer-panel">
          <header class="drawer-panel__header">
            <div>
              <p class="eyebrow">{{ subtitle }}</p>
              <h3>{{ title }}</h3>
            </div>

            <button class="ghost-button" type="button" @click="emit('close')">
              Закрыть
            </button>
          </header>

          <div class="drawer-panel__body">
            <slot />
          </div>
        </aside>
      </transition>
    </div>
  </transition>
</template>

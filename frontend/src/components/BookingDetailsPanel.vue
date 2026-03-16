<script setup>
import { computed } from 'vue'
import { formatDateTime, formatTime } from '../utils/datetime'

const props = defineProps({
  booking: {
    type: Object,
    default: null,
  },
})

const duration = computed(() => {
  if (!props.booking?.starts_at || !props.booking?.ends_at) {
    return ''
  }

  const start = new Date(props.booking.starts_at)
  const end = new Date(props.booking.ends_at)
  const diffMinutes = Math.round((end - start) / 60000)

  return `${diffMinutes} мин`
})
</script>

<template>
  <section class="panel details-panel">
    <div class="panel__header">
      <div>
        <p class="eyebrow">Focus panel</p>
        <h3>Детали встречи</h3>
      </div>
    </div>

    <div v-if="!booking" class="empty-state">
      Выберите встречу из списка, чтобы посмотреть детали и быстро перейти к редактированию.
    </div>

    <div v-else class="details-card">
      <div class="details-card__status-row">
        <span class="chip" :class="booking.status === 'cancelled' ? 'chip--muted' : 'chip--active'">
          {{ booking.status === 'cancelled' ? 'Отменена' : 'Активна' }}
        </span>
        <span class="details-card__number">Booking #{{ booking.id }}</span>
      </div>

      <h4>{{ booking.title }}</h4>
      <p>{{ booking.description || 'Описание не добавлено.' }}</p>

      <dl class="details-grid">
        <div>
          <dt>Дата</dt>
          <dd>{{ formatDateTime(booking.starts_at) }}</dd>
        </div>
        <div>
          <dt>Диапазон</dt>
          <dd>{{ formatTime(booking.starts_at) }} - {{ formatTime(booking.ends_at) }}</dd>
        </div>
        <div>
          <dt>Длительность</dt>
          <dd>{{ duration }}</dd>
        </div>
        <div>
          <dt>Статус</dt>
          <dd>{{ booking.status }}</dd>
        </div>
      </dl>
    </div>
  </section>
</template>

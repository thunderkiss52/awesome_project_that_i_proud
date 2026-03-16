<script setup>
import { formatDateTime } from '../utils/datetime'

defineProps({
  bookings: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
  saving: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['edit', 'cancel', 'select'])

function statusLabel(status) {
  return status === 'cancelled' ? 'Отменена' : 'Активна'
}
</script>

<template>
  <section class="panel">
    <div class="panel__header">
      <div>
        <p class="eyebrow">Лента встреч</p>
        <h3>Ваши бронирования</h3>
      </div>
      <span class="chip">{{ bookings.length }} всего</span>
    </div>

    <div v-if="loading" class="empty-state">
      Загружаем встречи...
    </div>

    <div v-else-if="!bookings.length" class="empty-state">
      <h4>Пока пусто</h4>
      <p>Создайте первую встречу, и она появится в этой ленте.</p>
    </div>

    <div v-else class="booking-list">
      <article class="booking-card" v-for="booking in bookings" :key="booking.id" @click="emit('select', booking)">
        <div class="booking-card__head">
          <div>
            <span class="chip" :class="booking.status === 'cancelled' ? 'chip--muted' : 'chip--active'">
              {{ statusLabel(booking.status) }}
            </span>
            <h4>{{ booking.title }}</h4>
          </div>
          <div class="booking-card__id">#{{ booking.id }}</div>
        </div>

        <p class="booking-card__description">
          {{ booking.description || 'Описание не указано.' }}
        </p>

        <dl class="booking-card__meta">
          <div>
            <dt>Начало</dt>
            <dd>{{ formatDateTime(booking.starts_at) }}</dd>
          </div>
          <div>
            <dt>Окончание</dt>
            <dd>{{ formatDateTime(booking.ends_at) }}</dd>
          </div>
        </dl>

        <div class="booking-card__actions">
          <button
            class="ghost-button"
            type="button"
            :disabled="booking.status === 'cancelled' || saving"
            @click.stop="emit('edit', booking)"
          >
            Изменить
          </button>
          <button
            class="danger-button"
            type="button"
            :disabled="booking.status === 'cancelled' || saving"
            @click.stop="emit('cancel', booking)"
          >
            Отменить
          </button>
        </div>
      </article>
    </div>
  </section>
</template>

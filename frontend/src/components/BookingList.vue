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

const emit = defineEmits(['edit', 'cancel'])
</script>

<template>
  <section class="panel">
    <div class="panel__header">
      <div>
        <p class="eyebrow">Meetings</p>
        <h3>Your bookings</h3>
      </div>
      <span class="chip">{{ bookings.length }} total</span>
    </div>

    <div v-if="loading" class="empty-state">
      Loading bookings...
    </div>

    <div v-else-if="!bookings.length" class="empty-state">
      <h4>No meetings yet</h4>
      <p>Create your first booking to see it here.</p>
    </div>

    <div v-else class="booking-grid">
      <article class="booking-card" v-for="booking in bookings" :key="booking.id">
        <div class="booking-card__head">
          <div>
            <span class="chip" :class="booking.status === 'cancelled' ? 'chip--muted' : 'chip--active'">
              {{ booking.status }}
            </span>
            <h4>{{ booking.title }}</h4>
          </div>
        </div>

        <p class="booking-card__description">
          {{ booking.description || 'No description provided.' }}
        </p>

        <dl class="booking-card__meta">
          <div>
            <dt>Start</dt>
            <dd>{{ formatDateTime(booking.starts_at) }}</dd>
          </div>
          <div>
            <dt>End</dt>
            <dd>{{ formatDateTime(booking.ends_at) }}</dd>
          </div>
        </dl>

        <div class="booking-card__actions">
          <button
            class="ghost-button"
            type="button"
            :disabled="booking.status === 'cancelled' || saving"
            @click="emit('edit', booking)"
          >
            Edit
          </button>
          <button
            class="danger-button"
            type="button"
            :disabled="booking.status === 'cancelled' || saving"
            @click="emit('cancel', booking)"
          >
            Cancel
          </button>
        </div>
      </article>
    </div>
  </section>
</template>

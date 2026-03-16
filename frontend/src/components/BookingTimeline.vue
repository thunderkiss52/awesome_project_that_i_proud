<script setup>
import { computed } from 'vue'
import { formatDayLabel, formatTime } from '../utils/datetime'

const props = defineProps({
  bookings: {
    type: Array,
    default: () => [],
  },
})

const groups = computed(() => {
  const active = props.bookings
    .filter((booking) => booking.status === 'active')
    .slice()
    .sort((a, b) => new Date(a.starts_at) - new Date(b.starts_at))
    .slice(0, 6)

  return active.reduce((acc, booking) => {
    const key = new Date(booking.starts_at).toDateString()

    if (!acc[key]) {
      acc[key] = {
        label: formatDayLabel(booking.starts_at),
        items: [],
      }
    }

    acc[key].items.push(booking)

    return acc
  }, {})
})
</script>

<template>
  <section class="panel timeline-panel">
    <div class="panel__header">
      <div>
        <p class="eyebrow">Agenda view</p>
        <h3>Ближайшее расписание</h3>
      </div>
      <span class="chip">next 6</span>
    </div>

    <div v-if="!Object.keys(groups).length" class="empty-state">
      Нет ближайших активных встреч.
    </div>

    <div v-else class="timeline-groups">
      <div class="timeline-group" v-for="(group, key) in groups" :key="key">
        <div class="timeline-group__label">{{ group.label }}</div>

        <div class="timeline-items">
          <article class="timeline-item" v-for="booking in group.items" :key="booking.id">
            <div class="timeline-item__time">
              <strong>{{ formatTime(booking.starts_at) }}</strong>
              <span>{{ formatTime(booking.ends_at) }}</span>
            </div>

            <div class="timeline-item__content">
              <strong>{{ booking.title }}</strong>
              <span>{{ booking.description || 'Без описания' }}</span>
            </div>
          </article>
        </div>
      </div>
    </div>
  </section>
</template>

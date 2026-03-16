<script setup>
import { computed, ref } from 'vue'
import { formatDayLabel, formatTime } from '../utils/datetime'

const props = defineProps({
  bookings: {
    type: Array,
    default: () => [],
  },
})

const emit = defineEmits(['select'])
const viewMode = ref('week')

const sortedBookings = computed(() =>
  props.bookings.slice().sort((a, b) => new Date(a.starts_at) - new Date(b.starts_at)),
)

const weekDays = computed(() => {
  const start = new Date()
  const day = start.getDay()
  const diff = day === 0 ? -6 : 1 - day
  start.setHours(0, 0, 0, 0)
  start.setDate(start.getDate() + diff)

  return Array.from({ length: 7 }, (_, index) => {
    const date = new Date(start)
    date.setDate(start.getDate() + index)
    const iso = date.toISOString().slice(0, 10)

    return {
      iso,
      label: formatDayLabel(date),
      items: sortedBookings.value.filter((booking) => booking.starts_at.slice(0, 10) === iso),
    }
  })
})

const monthDays = computed(() => {
  const now = new Date()
  const first = new Date(now.getFullYear(), now.getMonth(), 1)
  const last = new Date(now.getFullYear(), now.getMonth() + 1, 0)
  const leading = (first.getDay() + 6) % 7
  const total = leading + last.getDate()
  const cells = Math.ceil(total / 7) * 7

  return Array.from({ length: cells }, (_, index) => {
    const dayNumber = index - leading + 1
    const date = new Date(now.getFullYear(), now.getMonth(), dayNumber)
    const inMonth = dayNumber > 0 && dayNumber <= last.getDate()
    const iso = inMonth ? date.toISOString().slice(0, 10) : null

    return {
      key: `${index}-${iso ?? 'blank'}`,
      dateNumber: inMonth ? dayNumber : '',
      iso,
      inMonth,
      items: inMonth
        ? sortedBookings.value.filter((booking) => booking.starts_at.slice(0, 10) === iso).slice(0, 2)
        : [],
    }
  })
})
</script>

<template>
  <section class="panel calendar-panel">
    <div class="panel__header">
      <div>
        <p class="eyebrow">Calendar view</p>
        <h3>Календарь встреч</h3>
      </div>

      <div class="view-switch">
        <button
          class="ghost-button"
          :class="{ 'ghost-button--active': viewMode === 'week' }"
          type="button"
          @click="viewMode = 'week'"
        >
          Week
        </button>
        <button
          class="ghost-button"
          :class="{ 'ghost-button--active': viewMode === 'month' }"
          type="button"
          @click="viewMode = 'month'"
        >
          Month
        </button>
      </div>
    </div>

    <div v-if="viewMode === 'week'" class="week-calendar">
      <article class="week-day" v-for="day in weekDays" :key="day.iso">
        <header class="week-day__header">
          <strong>{{ day.label }}</strong>
          <span>{{ day.items.length }} встреч</span>
        </header>

        <div v-if="day.items.length" class="week-day__items">
          <button
            v-for="booking in day.items"
            :key="booking.id"
            class="calendar-booking"
            type="button"
            @click="emit('select', booking)"
          >
            <strong>{{ booking.title }}</strong>
            <span>{{ formatTime(booking.starts_at) }} - {{ formatTime(booking.ends_at) }}</span>
          </button>
        </div>

        <div v-else class="week-day__empty">Свободно</div>
      </article>
    </div>

    <div v-else class="month-calendar">
      <div class="month-calendar__weekdays">
        <span>Пн</span>
        <span>Вт</span>
        <span>Ср</span>
        <span>Чт</span>
        <span>Пт</span>
        <span>Сб</span>
        <span>Вс</span>
      </div>

      <div class="month-calendar__grid">
        <article
          v-for="day in monthDays"
          :key="day.key"
          class="month-day"
          :class="{ 'month-day--muted': !day.inMonth }"
        >
          <span class="month-day__number">{{ day.dateNumber }}</span>

          <button
            v-for="booking in day.items"
            :key="booking.id"
            class="month-day__booking"
            type="button"
            @click="emit('select', booking)"
          >
            {{ booking.title }}
          </button>
        </article>
      </div>
    </div>
  </section>
</template>

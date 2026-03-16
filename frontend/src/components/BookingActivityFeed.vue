<script setup>
import { computed } from 'vue'
import { formatDateTime } from '../utils/datetime'

const props = defineProps({
  activity: {
    type: Array,
    default: () => [],
  },
})

const normalizedActivity = computed(() =>
  props.activity.map((entry) => ({
    ...entry,
    title:
      entry.event_type === 'booking.created'
        ? 'Создано бронирование'
        : entry.event_type === 'booking.cancelled'
          ? 'Встреча отменена'
          : 'Встреча перенесена',
  })),
)
</script>

<template>
  <section class="panel activity-panel">
    <div class="panel__header">
      <div>
        <p class="eyebrow">Activity feed</p>
        <h3>События системы</h3>
      </div>
      <span class="chip">{{ normalizedActivity.length }} events</span>
    </div>

    <div v-if="!normalizedActivity.length" class="empty-state">
      События появятся после создания, отмены или переноса встреч.
    </div>

    <div v-else class="activity-list">
      <article class="activity-item" v-for="entry in normalizedActivity" :key="entry.id">
        <div class="activity-item__line"></div>
        <div class="activity-item__content">
          <strong>{{ entry.title }}</strong>
          <span>{{ entry.booking?.title || 'Встреча' }}</span>
          <small>{{ formatDateTime(entry.created_at) }}</small>
        </div>
      </article>
    </div>
  </section>
</template>

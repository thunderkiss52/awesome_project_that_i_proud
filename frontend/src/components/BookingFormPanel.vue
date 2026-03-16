<script setup>
import { reactive, watch } from 'vue'
import { toApiDateTime, toLocalInputValue } from '../utils/datetime'

const props = defineProps({
  mode: {
    type: String,
    default: 'create',
  },
  booking: {
    type: Object,
    default: null,
  },
  loading: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['submit', 'cancel'])

const form = reactive({
  title: '',
  description: '',
  starts_at: '',
  ends_at: '',
})

watch(
  () => props.booking,
  (booking) => {
    form.title = booking?.title ?? ''
    form.description = booking?.description ?? ''
    form.starts_at = toLocalInputValue(booking?.starts_at)
    form.ends_at = toLocalInputValue(booking?.ends_at)
  },
  { immediate: true },
)

function handleSubmit() {
  emit('submit', {
    title: form.title,
    description: form.description || null,
    starts_at: toApiDateTime(form.starts_at),
    ends_at: toApiDateTime(form.ends_at),
  })
}
</script>

<template>
  <section class="panel">
    <div class="panel__header">
      <div>
        <p class="eyebrow">{{ mode === 'create' ? 'New meeting' : 'Reschedule meeting' }}</p>
        <h3>{{ mode === 'create' ? 'Create booking' : 'Edit booking' }}</h3>
      </div>
    </div>

    <div class="form-grid">
      <label class="field">
        <span>Title</span>
        <input v-model="form.title" type="text" maxlength="255" placeholder="Meeting with client" />
      </label>

      <label class="field">
        <span>Description</span>
        <textarea v-model="form.description" rows="4" placeholder="Project discussion"></textarea>
      </label>

      <label class="field">
        <span>Starts at</span>
        <input v-model="form.starts_at" type="datetime-local" />
      </label>

      <label class="field">
        <span>Ends at</span>
        <input v-model="form.ends_at" type="datetime-local" />
      </label>
    </div>

    <div class="form-actions">
      <button class="ghost-button" type="button" @click="emit('cancel')">
        Cancel
      </button>
      <button class="primary-button" type="button" :disabled="loading" @click="handleSubmit">
        {{ loading ? 'Saving...' : mode === 'create' ? 'Create booking' : 'Save changes' }}
      </button>
    </div>
  </section>
</template>

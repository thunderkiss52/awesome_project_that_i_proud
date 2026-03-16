<script setup>
import { computed, reactive, watch } from 'vue'
import {
  addMinutesToInputValue,
  nowInputValue,
  timezoneLabel,
  toApiDateTime,
  toLocalInputValue,
} from '../utils/datetime'

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
  errors: {
    type: Object,
    default: () => ({}),
  },
})

const emit = defineEmits(['submit', 'cancel', 'change'])

const form = reactive({
  title: '',
  description: '',
  starts_at: '',
  ends_at: '',
})

const localErrors = reactive({
  title: '',
  starts_at: '',
  ends_at: '',
})

const minStart = computed(() => nowInputValue())
const minEnd = computed(() => form.starts_at || nowInputValue())
const zone = timezoneLabel()

function resetForm(booking = null) {
  if (booking) {
    form.title = booking.title ?? ''
    form.description = booking.description ?? ''
    form.starts_at = toLocalInputValue(booking.starts_at)
    form.ends_at = toLocalInputValue(booking.ends_at)
    return
  }

  const suggestedStart = addMinutesToInputValue(nowInputValue(), 30)

  form.title = ''
  form.description = ''
  form.starts_at = suggestedStart
  form.ends_at = addMinutesToInputValue(suggestedStart, 60)
}

watch(
  () => props.booking,
  (booking) => {
    resetForm(booking)
  },
  { immediate: true },
)

watch(
  () => ({ ...form }),
  () => {
    emit('change')
  },
  { deep: true },
)

watch(
  () => form.starts_at,
  (value) => {
    if (!form.ends_at || form.ends_at <= value) {
      form.ends_at = addMinutesToInputValue(value, 60)
    }
  },
)

function validate() {
  localErrors.title = ''
  localErrors.starts_at = ''
  localErrors.ends_at = ''

  if (!form.title.trim()) {
    localErrors.title = 'Укажите название встречи.'
  }

  if (!form.starts_at) {
    localErrors.starts_at = 'Выберите дату и время начала.'
  } else if (new Date(form.starts_at) <= new Date()) {
    localErrors.starts_at = 'Встреча должна начинаться в будущем.'
  }

  if (!form.ends_at) {
    localErrors.ends_at = 'Выберите дату и время окончания.'
  } else if (form.starts_at && new Date(form.ends_at) <= new Date(form.starts_at)) {
    localErrors.ends_at = 'Окончание должно быть позже начала.'
  }

  return !localErrors.title && !localErrors.starts_at && !localErrors.ends_at
}

function handleSubmit() {
  if (!validate()) {
    return
  }

  emit('submit', {
    title: form.title.trim(),
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
        <p class="eyebrow">{{ mode === 'create' ? 'Новая встреча' : 'Перенос встречи' }}</p>
        <h3>{{ mode === 'create' ? 'Создать бронирование' : 'Изменить бронирование' }}</h3>
      </div>
    </div>

    <div class="form-hint">
      Время показывается в вашей локальной зоне: <strong>{{ zone }}</strong>.
    </div>

    <div class="form-grid">
      <label class="field">
        <span>Название</span>
        <input
          v-model="form.title"
          type="text"
          maxlength="255"
          placeholder="Встреча с клиентом"
          @input="localErrors.title = ''"
        />
        <small v-if="localErrors.title || errors.title?.[0]" class="field-error">
          {{ localErrors.title || errors.title?.[0] }}
        </small>
      </label>

      <label class="field">
        <span>Описание</span>
        <textarea v-model="form.description" rows="4" placeholder="Обсуждение проекта"></textarea>
      </label>

      <label class="field">
        <span>Начало</span>
        <input
          v-model="form.starts_at"
          type="datetime-local"
          :min="minStart"
          @input="localErrors.starts_at = ''"
        />
        <small class="field-help">Нельзя выбрать время в прошлом.</small>
        <small v-if="localErrors.starts_at || errors.starts_at?.[0]" class="field-error">
          {{ localErrors.starts_at || errors.starts_at?.[0] }}
        </small>
      </label>

      <label class="field">
        <span>Окончание</span>
        <input
          v-model="form.ends_at"
          type="datetime-local"
          :min="minEnd"
          @input="localErrors.ends_at = ''"
        />
        <small class="field-help">Рекомендуемый шаг: минимум 30-60 минут после начала.</small>
        <small v-if="localErrors.ends_at || errors.ends_at?.[0]" class="field-error">
          {{ localErrors.ends_at || errors.ends_at?.[0] }}
        </small>
      </label>
    </div>

    <div class="form-actions">
      <button class="ghost-button" type="button" @click="emit('cancel')">
        Сбросить
      </button>
      <button class="primary-button" type="button" :disabled="loading" @click="handleSubmit">
        {{ loading ? 'Сохраняем...' : mode === 'create' ? 'Создать встречу' : 'Сохранить изменения' }}
      </button>
    </div>
  </section>
</template>

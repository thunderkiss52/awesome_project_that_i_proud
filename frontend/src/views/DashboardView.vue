<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import AppShell from '../components/AppShell.vue'
import BookingFormPanel from '../components/BookingFormPanel.vue'
import BookingList from '../components/BookingList.vue'
import BookingTimeline from '../components/BookingTimeline.vue'
import BookingDetailsPanel from '../components/BookingDetailsPanel.vue'
import BookingCalendar from '../components/BookingCalendar.vue'
import BookingActivityFeed from '../components/BookingActivityFeed.vue'
import BookingDrawer from '../components/BookingDrawer.vue'
import { useAuthStore } from '../stores/auth'
import { useBookingStore } from '../stores/bookings'

const router = useRouter()
const authStore = useAuthStore()
const bookingStore = useBookingStore()

const mode = ref('create')
const activeBooking = ref(null)
const drawerOpen = ref(false)
const feedback = ref({ type: '', text: '' })
const formErrors = ref({})

const summary = computed(() => bookingStore.summary)

async function loadDashboard() {
  try {
    await bookingStore.fetchDashboard()
  } catch (error) {
    feedback.value = {
      type: 'error',
      text: error.response?.data?.message || 'Failed to load dashboard.',
    }
  }
}

function openCreate() {
  mode.value = 'create'
  activeBooking.value = null
  drawerOpen.value = true
  formErrors.value = {}
}

function openEdit(booking) {
  mode.value = 'edit'
  activeBooking.value = booking
  drawerOpen.value = true
  formErrors.value = {}
}

function selectBooking(booking) {
  activeBooking.value = booking
}

async function handleSubmit(payload) {
  feedback.value = { type: '', text: '' }
  formErrors.value = {}

  try {
    if (mode.value === 'create') {
      const response = await bookingStore.createBooking(payload)
      feedback.value = { type: 'success', text: 'Встреча успешно создана.' }
      activeBooking.value = response.booking
      closeDrawer()
    } else if (activeBooking.value) {
      const response = await bookingStore.updateBooking(activeBooking.value.id, payload)
      feedback.value = { type: 'success', text: 'Встреча успешно обновлена.' }
      activeBooking.value = response.booking
      closeDrawer()
    }
  } catch (error) {
    formErrors.value = error.response?.data?.errors || {}
    feedback.value = {
      type: 'error',
      text: error.response?.data?.message || 'Не удалось выполнить действие.',
    }
  }
}

async function handleCancelBooking(booking) {
  feedback.value = { type: '', text: '' }

  try {
    await bookingStore.cancelBooking(booking.id)
    feedback.value = { type: 'success', text: 'Встреча отменена.' }
    if (activeBooking.value?.id === booking.id) {
      openCreate()
    }
  } catch (error) {
    feedback.value = {
      type: 'error',
      text: error.response?.data?.message || 'Не удалось отменить встречу.',
    }
  }
}

function clearFormErrors() {
  if (Object.keys(formErrors.value).length) {
    formErrors.value = {}
  }
}

function closeDrawer() {
  drawerOpen.value = false
  formErrors.value = {}
}

async function handleLogout() {
  await authStore.logout()
  router.push({ name: 'login' })
}

onMounted(() => {
  loadDashboard()
})
</script>

<template>
  <AppShell :user="authStore.user" :summary="summary" @logout="handleLogout">
    <div class="dashboard-grid">
      <section class="hero-strip">
        <div class="hero-strip__content">
          <p class="eyebrow">Product snapshot</p>
          <h3>Чистый fullstack-поток: от JWT-входа до доменных ограничений и событий.</h3>
          <p>
            Эта панель показывает, что проект умеет не только сохранять записи, но и грамотно обрабатывать лимиты, конфликты времени и UX ошибок.
          </p>
        </div>
      </section>

      <section class="stats-grid">
        <article class="stat-card">
          <span class="eyebrow">Текущий тариф</span>
          <strong>{{ summary?.subscription?.name || 'Загрузка...' }}</strong>
          <small>{{ summary?.subscription?.code || '...' }}</small>
        </article>

        <article class="stat-card">
          <span class="eyebrow">Активные встречи</span>
          <strong>{{ summary?.active_bookings_count ?? 0 }}</strong>
          <small>Будущие подтверждённые бронирования</small>
        </article>

        <article class="stat-card">
          <span class="eyebrow">Осталось слотов</span>
          <strong>{{ summary?.remaining_slots ?? 0 }}</strong>
          <small>Сколько встреч ещё можно создать по тарифу</small>
        </article>
      </section>

      <section class="dashboard-secondary-grid">
        <BookingCalendar :bookings="bookingStore.bookings" @select="selectBooking" />
        <BookingTimeline :bookings="bookingStore.bookings" />
        <BookingDetailsPanel :booking="activeBooking" />
        <BookingActivityFeed :activity="bookingStore.activity" />
      </section>

      <div v-if="feedback.text" class="feedback" :class="feedback.type === 'error' ? 'feedback--error' : 'feedback--success'">
        {{ feedback.text }}
      </div>

      <div class="content-grid">
        <div>
          <div class="panel panel--compact">
            <div class="panel__header">
              <div>
                <p class="eyebrow">Workspace actions</p>
                <h3>Управление встречами</h3>
              </div>

              <button class="ghost-button" type="button" @click="openCreate">
                Новая встреча
              </button>
            </div>

            <p class="panel-copy">
              Откройте drawer для создания или редактирования встречи. Клик по карточке открывает фокус-панель справа.
            </p>
          </div>
        </div>

        <BookingList
          :bookings="bookingStore.bookings"
          :loading="bookingStore.isLoading"
          :saving="bookingStore.isSaving"
          @edit="openEdit"
          @cancel="handleCancelBooking"
          @select="selectBooking"
        />
      </div>

      <BookingDrawer
        :open="drawerOpen"
        :title="mode === 'create' ? 'Создать встречу' : 'Редактировать встречу'"
        :subtitle="mode === 'create' ? 'Quick create' : 'Booking editor'"
        @close="closeDrawer"
      >
        <BookingFormPanel
          :mode="mode"
          :booking="mode === 'edit' ? activeBooking : null"
          :loading="bookingStore.isSaving"
          :errors="formErrors"
          @submit="handleSubmit"
          @cancel="closeDrawer"
          @change="clearFormErrors"
        />
      </BookingDrawer>
    </div>
  </AppShell>
</template>

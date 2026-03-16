<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import AppShell from '../components/AppShell.vue'
import BookingFormPanel from '../components/BookingFormPanel.vue'
import BookingList from '../components/BookingList.vue'
import { useAuthStore } from '../stores/auth'
import { useBookingStore } from '../stores/bookings'

const router = useRouter()
const authStore = useAuthStore()
const bookingStore = useBookingStore()

const mode = ref('create')
const activeBooking = ref(null)
const feedback = ref({ type: '', text: '' })

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
}

function openEdit(booking) {
  mode.value = 'edit'
  activeBooking.value = booking
}

async function handleSubmit(payload) {
  feedback.value = { type: '', text: '' }

  try {
    if (mode.value === 'create') {
      await bookingStore.createBooking(payload)
      feedback.value = { type: 'success', text: 'Booking created successfully.' }
      openCreate()
    } else if (activeBooking.value) {
      await bookingStore.updateBooking(activeBooking.value.id, payload)
      feedback.value = { type: 'success', text: 'Booking updated successfully.' }
      openCreate()
    }
  } catch (error) {
    feedback.value = {
      type: 'error',
      text: error.response?.data?.message || 'Action failed.',
    }
  }
}

async function handleCancelBooking(booking) {
  feedback.value = { type: '', text: '' }

  try {
    await bookingStore.cancelBooking(booking.id)
    feedback.value = { type: 'success', text: 'Booking cancelled successfully.' }
    if (activeBooking.value?.id === booking.id) {
      openCreate()
    }
  } catch (error) {
    feedback.value = {
      type: 'error',
      text: error.response?.data?.message || 'Unable to cancel booking.',
    }
  }
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
  <AppShell :user="authStore.user" @logout="handleLogout">
    <div class="dashboard-grid">
      <section class="stats-grid">
        <article class="stat-card">
          <span class="eyebrow">Current plan</span>
          <strong>{{ summary?.subscription?.name || 'Loading...' }}</strong>
          <small>{{ summary?.subscription?.code || '...' }}</small>
        </article>

        <article class="stat-card">
          <span class="eyebrow">Active bookings</span>
          <strong>{{ summary?.active_bookings_count ?? 0 }}</strong>
          <small>Current future meetings</small>
        </article>

        <article class="stat-card">
          <span class="eyebrow">Remaining slots</span>
          <strong>{{ summary?.remaining_slots ?? 0 }}</strong>
          <small>Available before hitting the plan limit</small>
        </article>
      </section>

      <div v-if="feedback.text" class="feedback" :class="feedback.type === 'error' ? 'feedback--error' : 'feedback--success'">
        {{ feedback.text }}
      </div>

      <div class="content-grid">
        <div>
          <div class="panel panel--compact">
            <div class="panel__header">
              <div>
                <p class="eyebrow">Actions</p>
                <h3>{{ mode === 'create' ? 'Create mode' : 'Edit mode' }}</h3>
              </div>

              <button class="ghost-button" type="button" @click="openCreate">
                New booking
              </button>
            </div>
          </div>

          <BookingFormPanel
            :mode="mode"
            :booking="activeBooking"
            :loading="bookingStore.isSaving"
            @submit="handleSubmit"
            @cancel="openCreate"
          />
        </div>

        <BookingList
          :bookings="bookingStore.bookings"
          :loading="bookingStore.isLoading"
          :saving="bookingStore.isSaving"
          @edit="openEdit"
          @cancel="handleCancelBooking"
        />
      </div>
    </div>
  </AppShell>
</template>

import { ref } from 'vue'
import { defineStore } from 'pinia'
import http from '../api/http'

export const useBookingStore = defineStore('bookings', () => {
  const bookings = ref([])
  const activity = ref([])
  const summary = ref(null)
  const isLoading = ref(false)
  const isSaving = ref(false)

  async function fetchDashboard() {
    isLoading.value = true

    try {
      const [summaryResponse, bookingsResponse, activityResponse] = await Promise.all([
        http.get('/subscription'),
        http.get('/bookings'),
        http.get('/activity'),
      ])

      summary.value = summaryResponse.data
      bookings.value = bookingsResponse.data.data ?? []
      activity.value = activityResponse.data.data ?? []
    } finally {
      isLoading.value = false
    }
  }

  async function createBooking(payload) {
    isSaving.value = true

    try {
      const { data } = await http.post('/bookings', payload)
      await fetchDashboard()
      return data
    } finally {
      isSaving.value = false
    }
  }

  async function updateBooking(bookingId, payload) {
    isSaving.value = true

    try {
      const { data } = await http.put(`/bookings/${bookingId}`, payload)
      await fetchDashboard()
      return data
    } finally {
      isSaving.value = false
    }
  }

  async function cancelBooking(bookingId) {
    isSaving.value = true

    try {
      const { data } = await http.delete(`/bookings/${bookingId}`)
      await fetchDashboard()
      return data
    } finally {
      isSaving.value = false
    }
  }

  return {
    bookings,
    activity,
    summary,
    isLoading,
    isSaving,
    fetchDashboard,
    createBooking,
    updateBooking,
    cancelBooking,
  }
})

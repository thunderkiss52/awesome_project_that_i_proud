import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { useAuthStore } from './stores/auth'
import { pinia } from './stores'
import './style.css'

async function bootstrap() {
  const app = createApp(App)

  app.use(pinia)

  const authStore = useAuthStore()
  await authStore.restoreSession()

  app.use(router)
  app.mount('#app')
}

bootstrap()

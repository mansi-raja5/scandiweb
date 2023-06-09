import './assets/css/tailwind.pcss'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { VueQueryPlugin } from '@tanstack/vue-query'

import App from './App.vue'
import router from './router'

const app = createApp(App)

app.use(router)
app.use(createPinia())
app.use(VueQueryPlugin)

app.mount('#app')
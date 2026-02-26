import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import VCalendar from 'v-calendar'
import 'v-calendar/style.css'
window.router = router;

const app = createApp(App)

app.use(router)
app.use(VCalendar)

app.mount('#app')

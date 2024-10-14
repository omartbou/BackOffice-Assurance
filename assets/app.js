import './styles/app.css'

import { createApp } from 'vue'

import App from '../assurance-frontend/src/App.vue'
import router from '../assurance-frontend/src/router'

const app = createApp(App)

app.use(router)

app.mount('#app')

// Import './assets/main.css' if needed
// import './assets/main.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router/index.js';

// Import Bootstrap and BootstrapVue CSS
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue-3/dist/bootstrap-vue-3.css'; // Updated for BootstrapVue 3

// Import BootstrapVue
import { BootstrapVue3 } from 'bootstrap-vue-3';
import * as bootstrap from 'bootstrap';
// Create Vue app
const app = createApp(App);

// Use plugins
app.use(createPinia());
app.use(router);
app.use(BootstrapVue3); // Register BootstrapVue3

// Mount the app
app.mount('#app');

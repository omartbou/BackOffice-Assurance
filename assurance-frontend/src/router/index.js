// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router';
import Client from '../components/Client/List.vue'; // Ensure the path is correct
import Update from '../components/Client/Update.vue'; // Ensure the path is correct
import Add from '../components/Client/Add.vue'; // Ensure the path is correct

const routes = [
  {
    path: '/clients',
    name: 'client',
    component: Client, // This should point to the correct component
  },
  {
    path: '/about',
    name: 'about',
    component: () => import('../views/AboutView.vue'), // Lazy loading the About component
  },
  {
    path: '/client/update/:id', // Route with parameter for client ID
    name: 'Update',
    component: Update, // This should point to the correct component

  },
  {
    path: '/client/add', // Route with parameter for client ID
    name: 'Add',
    component: Add, // This should point to the correct component

  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;

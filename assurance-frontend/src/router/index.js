// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router';
import Client from '../components/Client/List.vue';
import UpdateClient from '../components/Client/Update.vue';
import AddClient from '../components/Client/Add.vue';
import ListDevis from '../components/Devis/List.vue';
import UpdateDevis from '../components/Devis/Update.vue';
import AddDevis from '../components/Devis/Add.vue';

const routes = [
  {
    path: '/clients',
    name: 'client',
    component: Client,
  },
  {
    path: '/about',
    name: 'about',
    component: () => import('../views/AboutView.vue'), // Lazy loading the About component
  },
  {
    path: '/client/update/:id',
    name: 'UpdateClient',
    component: UpdateClient,

  },
  {
    path: '/client/add',
    name: 'AddClient',
    component: AddClient,

  },
  {
    path: '/devis/add',
    name: 'AddDevis',
    component: AddDevis,

  },
  {
    path: '/devis',
    name: 'ListDevis',
    component: ListDevis,

  },
  {
    path: '/devis/update/:id',
    name: 'UpdateDevis',
    component: UpdateDevis,

  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;

// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router';
import Client from '../components/Client/List.vue';
import ListDevis from '../components/Devis/List.vue';
import AddVoiture from '../components/Voiture/Add.vue';
import ListVoiture from '../components/Voiture/List.vue';
import HomeView from '../views/HomeView.vue';

const routes = [
  {
    path: '/',
    name: 'HomeView',
    component: HomeView,
  },
  {
    path: '/clients',
    name: 'Client',
    component: Client,
  },
  {
    path: '/devis',
    name: 'ListDevis',
    component: ListDevis,

  },

  {
    path: '/voiture/add',
    name: 'AddVoiture',
    component: AddVoiture,

  },

  {
    path: '/voitures/',
    name: 'ListVoiture',
    component: ListVoiture,

  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;

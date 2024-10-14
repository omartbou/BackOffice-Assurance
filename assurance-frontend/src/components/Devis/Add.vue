<template>
  <div>
    <h1>Add New Devis</h1>
    <form @submit.prevent="addDevis">
      <div class="mb-3">
        <label for="client" class="form-label">Client</label>
        <select id="client" v-model="devis.client_id" class="form-select" required>
          <option value="" disabled>Select a client</option>
          <option v-for="client in clients" :key="client.id" :value="client.id">
            {{ client.nom }} {{ client.prenom }}
          </option>
        </select>
      </div>
      <div class="mb-3">
        <label for="prix" class="form-label">Prix</label>
        <input type="number" id="prix" v-model="devis.prix" class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="frequence_prix" class="form-label">Frequence Prix</label>
        <input type="text" id="frequence_prix" v-model="devis.frequence_prix" class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="date_effet" class="form-label">Date d'effet</label>
        <input type="date" id="date_effet" v-model="devis.date_effet" class="form-control" required />
      </div>
      <button type="submit" class="btn btn-primary">Ajouter un devis</button>
      <button type="button" class="btn btn-secondary" @click="goBack">Annuler</button>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const clients = ref([]);
const devis = ref({
  date_effet: '',
  client_id: null,
});

// Fetch clients when the component mounts
const getClients = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/clients');
    clients.value = response.data;
  } catch (error) {
    console.error('Error fetching clients:', error);
  }
};
onMounted(() => {
  getClients();
});
// Function to add a new devis
const addDevis = async () => {
  try {
    await axios.post('http://localhost:8000/api/devis/post', devis.value);
    router.push('/devis');
  } catch (error) {
    console.error('Error adding devis:', error.response ? error.response.data : error.message);
  }
};

// Function to navigate back
const goBack = () => {
  router.push('/devis');
};

</script>

<style scoped>
/* Add any additional styling here */
</style>

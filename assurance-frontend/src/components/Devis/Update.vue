<template>
  <div>
    <h1>Update Devis</h1>
    <form @submit.prevent="updateDevis">
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
      <button type="submit" class="btn btn-primary">Update Devis</button>
      <button type="button" class="btn btn-secondary" @click="goBack">Cancel</button>
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
  id: null, // Add the ID of the devis you want to update
  date_effet: '',
  client_id: null,
  prix: '',
  frequence_prix: ''
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
const findClient = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/client/${devis.value.client_id}');
    clients.value = response.data;
  } catch (error) {
    console.error('Error fetching clients:', error);
  }
};

// Fetch the existing devis data
const getDevis = async (id) => {
  try {
    const response = await axios.get(`http://localhost:8000/api/devis/${id}`);
    const fetchedDevis = response.data;

    // Populate the devis object with existing data
    devis.value = {
      ...fetchedDevis,
      client_id: fetchedDevis.client?.id || null,
    };  } catch (error) {
    console.error('Error fetching devis:', error);
  }
};

// Function to update the devis
const updateDevis = async () => {
  try {
    await axios.put(`http://localhost:8000/api/devis/edit/${devis.value.id}`, devis.value);
    router.push('/devis');
  } catch (error) {
    console.error('Error updating devis:', error.response ? error.response.data : error.message);
  }
};

// Function to navigate back
const goBack = () => {
  router.push('/devis');
};

// Call the function to get clients and the devis data when the component mounts
onMounted(() => {
  getClients();
  const devisId = router.currentRoute.value.params.id; // Get the ID from the route parameters
  if (devisId) {
    getDevis(devisId);
  }
});
</script>

<style scoped>
/* Add any additional styling here */
</style>

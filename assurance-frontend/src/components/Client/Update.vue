<template>
  <div>
    <h1>Update Client</h1>
    <form @submit.prevent="updateClient">
      <div class="mb-3">
        <label for="nom" class="form-label">Nom</label>
        <input type="text" id="nom" v-model="client.nom" class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="prenom" class="form-label">Prénom</label>
        <input type="text" id="prenom" v-model="client.prenom" class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="date_naissance" class="form-label">Date de Naissance</label>
        <input type="date" id="date_naissance" v-model="client.date_naissance" class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="est_personne" class="form-label">Type</label>
        <select id="est_personne" v-model="client.est_personne" class="form-select" required>
          <option value="0">Particulier</option>
          <option value="1">Professionnel</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Update Client</button>
      <button type="button" class="btn btn-secondary" @click="goBack">Cancel</button>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const client = ref({
  nom: '',
  prenom: '',
  date_naissance: '',
  est_personne: 0,
});

const clientId = route.params.id; // Get client ID from route params

// Fetch client data when component is mounted
onMounted(async () => {
  const response = await axios.get(`http://localhost:8000/api/client/${clientId}`);
  Object.assign(client.value, response.data); // Assign fetched data to client object
});

// Update client information
const updateClient = async () => {
  try {
    await axios.put(`http://localhost:8000/api/clients/edit/${clientId}`, client.value);
    router.push('/client/list'); // Redirect to client list after successful update
  } catch (error) {
    console.error('Error updating client:', error.response ? error.response.data : error.message);
  }
};

// Function to go back to the client list
const goBack = () => {
  router.push('/clients');
};
</script>

<style scoped>
/* Add any additional styling here */
</style>

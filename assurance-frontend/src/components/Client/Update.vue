<template>
  <div class="container my-5">
    <h1 class="text-white mb-4">Modifier le client</h1>

    <form @submit.prevent="updateClient" class="bg-dark p-4 rounded">
      <div class="mb-3">
        <label for="nom" class="form-label text-white">Nom</label>
        <input type="text" id="nom" v-model="client.nom" class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="prenom" class="form-label text-white">Prénom</label>
        <input type="text" id="prenom" v-model="client.prenom" class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="date_naissance" class="form-label text-white">Date de Naissance</label>
        <input type="date" id="date_naissance" v-model="client.date_naissance" class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="est_personne" class="form-label text-white">Type</label>
        <select id="est_personne" v-model="client.est_personne" class="form-select" required>
          <option value="0">Particulier</option>
          <option value="1">Professionnel</option>
        </select>
      </div>
      <div class="d-flex">
      <button type="submit" class="btn btn-success me-2">Modifier </button>
      <button type="button" class="btn btn-secondary" @click="goBack">Annuler</button>
      </div>
    </form>

    <div v-if="successMessage" class="alert alert-success mt-4" role="alert">
      {{ successMessage }}
    </div>
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
const successMessage = ref(''); // Reactive state for success message
const clientId = route.params.id; // Get client ID from route params

// Fetch client data when component is mounted
onMounted(async () => {
  try {
    const response = await axios.get(`http://localhost:8000/api/client/${clientId}`);
    Object.assign(client.value, response.data); // Assign fetched data to client object
  } catch (error) {
    console.error('Error fetching client data:', error.response ? error.response.data : error.message);
  }
});

// Update client information
const updateClient = async () => {
  try {
    await axios.put(`http://localhost:8000/api/clients/edit/${clientId}`, client.value);
    successMessage.value = 'Client modifié avec success!'; // Set success message

    // Redirect to the home page with the success message
    router.push({ name: 'Client', query: { message: successMessage.value } });
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
.container {
  max-width: 600px; /* Set max width for the form container */
}

.bg-dark {
  background-color: #343a40; /* Dark background for the form */
}

.text-white {
  color: #ffffff; /* White text color for labels */
}

.btn-success {
  background-color: #28a745; /* Green background for the update button */
}

.alert-success {
  background-color: #d4edda; /* Light green background for success message */
  color: #155724;
}
</style>

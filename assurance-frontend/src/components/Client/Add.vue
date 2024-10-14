<template>
  <div>
    <h1>Add New Client</h1>
    <form @submit.prevent="addClient">
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
      <button type="submit" class="btn btn-primary">Add Client</button>
      <button type="button" class="btn btn-secondary" @click="goBack">Cancel</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const client = ref({
  nom: '',
  prenom: '',
  date_naissance: '',
  est_personne: 0,
});

// Function to add a new client
const addClient = async () => {
  try {
    await axios.post('http://localhost:8000/api/clients/post', client.value);
    router.push('/clients');
  } catch (error) {
    console.error('Error adding client:', error.response ? error.response.data : error.message);
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

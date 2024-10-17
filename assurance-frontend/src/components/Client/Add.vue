<template>
  <div class="container my-5">
    <h1 class="text-white mb-4">Ajouter un client</h1>

    <form @submit.prevent="addClient" class="bg-dark p-4 rounded">
      <div class="mb-3">
        <label for="nom" class="form-label text-white" >Nom</label>
        <input type="text" id="nom" v-model="client.nom"
               :class="client.nom.length ? validClass : errorClass"
               class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="prenom" class="form-label text-white">Prénom</label>
        <input type="text" id="prenom" v-model="client.prenom"
               :class="client.prenom.length ? validClass : errorClass"
               class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="date_naissance" class="form-label text-white">Date de Naissance</label>
        <input type="date" id="date_naissance" v-model="client.date_naissance"
               :class="client.date_naissance.length ? validClass : errorClass"
               class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="est_personne" class="form-label text-white">Type</label>
        <select id="est_personne" v-model="client.est_personne"
                :class="validClass"
                class="form-select" required>
          <option value="0">Particulier</option>
          <option value="1">Professionnel</option>
        </select>
      </div>
      <div class="d-flex">
        <button type="submit" class="btn btn-success me-2">Ajouter </button>
        <button type="button" class="btn btn-secondary" @click="goBack">Annuler</button>
      </div>
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
const validClass=ref("form-control is-valid");
const errorClass=ref("form-control is-invalid");
const successMessage = ref(''); // Reactive state for success message

// Function to add a new client
const addClient = async () => {
  try {
    await axios.post('http://localhost:8000/api/clients/post', client.value);
    successMessage.value = 'Client ajouté avec success!'; // Set success message

    // Redirect to the home page with the success message
    router.push({ name: 'Client', query: { message: successMessage.value } });
  } catch (error) {
    console.error('Error adding client:', error.response ? error.response.data : error.message);
  }
};

const goBack = () => {
  router.push('/clients');
};
</script>

<style scoped>
.container {
  max-width: 600px;
}

.bg-dark {
  background-color: #343a40;
}

.text-white {
  color: #ffffff;
}

.btn-success {
  background-color: #28a745;
}
</style>

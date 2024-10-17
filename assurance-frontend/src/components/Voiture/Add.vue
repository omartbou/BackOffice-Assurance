<template>
  <div class="container my-5">
    <h1 class="text-white mb-4">Ajouter une nouvelle voiture</h1>

    <form @submit.prevent="addVoiture" class="bg-dark p-4 rounded">
      <div class="mb-3">
        <label for="client" class="form-label text-white">Client</label>
        <select id="client" v-model="voiture.client_id"
                :class="voiture.client_id !== null ? validClass:errorClass"
                class="form-select" required>
          <option value="" disabled>Sélectionner un client</option>
          <option v-for="client in clients" :key="client.id" :value="client.id">
            {{ client.nom }} {{ client.prenom }}
          </option>
        </select>
      </div>
      <div class="mb-3">
        <label for="usage" class="form-label text-white">Usage</label>
        <input type="text" id="usage" v-model="voiture.voiture_usage"
               :class="voiture.voiture_usage.length ? validClass:errorClass"
               class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="emplacement" class="form-label text-white">Emplacement</label>
        <input type="text" id="emplacement" v-model="voiture.emplacement"
               :class="voiture.emplacement.length ? validClass:errorClass"
               class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="numero_immatriculation"
               class="form-label text-white">Numéro Immatriculation</label>
        <input type="text" id="numero_immatriculation"
               :class="voiture.numero_immatriculation.length ? validClass:errorClass"
               v-model="voiture.numero_immatriculation" class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="date_achat" class="form-label text-white">Date d'achat</label>
        <input type="date" id="date_achat" v-model="voiture.date_achat"
               :class="voiture.date_achat.length ? validClass:errorClass"
               class="form-control" required />
      </div>

      <div class="d-flex">
        <button type="submit" class="btn btn-success me-2">Ajouter</button>
        <button type="button" class="btn btn-secondary" @click="goBack">Annuler</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const clients = ref([]);
const voiture = ref({
  date_achat: '',
  voiture_usage: '',
  numero_immatriculation: '',
  emplacement: '',
  client_id: null,
});
const successMessage = ref('');
const validClass=ref("form-control is-valid");
const errorClass=ref("form-control is-invalid");
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

const addVoiture = async () => {
  try {
    await axios.post('http://localhost:8000/api/voiture/post', voiture.value);
    successMessage.value = 'Voiture ajoutée avec success!'; // Set success message

    // Redirect to the home page with the success message
    router.push({ name: 'ListVoiture', query: { message: successMessage.value } });  } catch (error) {
    console.error('Error adding voiture:', error.response ? error.response.data : error.message);
  }
};

const goBack = () => {
  router.push('/voitures');
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

.btn-secondary {
  background-color: #6c757d;
}

.form-control,
.form-select {
  background-color: #495057;
  color: #ffffff;
}

.form-control:focus,
.form-select:focus {
  background-color: #495057;
  color: #ffffff;
  border-color: #28a745;
}

h1 {
  color: #ffffff;
}
</style>

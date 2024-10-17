<template>
  <div class="container my-5">
    <h1 class="text-white mb-4">Modifier la voiture</h1>

    <form @submit.prevent="updateVoiture" class="bg-dark p-4 rounded">
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
               :class="voiture.voiture_usage !== null ? validClass:errorClass"
               class="form-control" required />
      </div>

      <div class="mb-3">
        <label for="emplacement" class="form-label text-white">Emplacement</label>
        <input type="text" id="emplacement" v-model="voiture.emplacement"
               :class="voiture.emplacement !== null ? validClass:errorClass"
               class="form-control" required />
      </div>

      <div class="mb-3">
        <label for="date_achat" class="form-label text-white">Date d'achat</label>
        <input type="date" id="date_achat" v-model="voiture.date_achat"
               :class="voiture.date_achat !== null ? validClass:errorClass"
               class="form-control" required />
      </div>

      <div class="d-flex">
        <button type="submit" class="btn btn-success me-2">Modifier</button>
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
  const voitureId = router.currentRoute.value.params.id; // Get the ID from the route parameters
  if (voitureId) {
    getVoiture(voitureId);
  }
});
const successMessage = ref('');
const getVoiture = async (id) => {
  try {
    const response = await axios.get(`http://localhost:8000/api/voiture/${id}`);
    const fetchedVoiture = response.data;

    voiture.value = {
      ...fetchedVoiture,
      client_id: fetchedVoiture.client?.id || null,
      date_achat: fetchedVoiture.date_achat ? new Date(fetchedVoiture.date_achat).toISOString().split('T')[0] : ''

    };
  } catch (error) {
    console.error('Error fetching voiture:', error);
  }
};

const updateVoiture = async () => {
  try {
    await axios.put(`http://localhost:8000/api/voiture/edit/${voiture.value.id}`, voiture.value);
    successMessage.value = 'Voiture modifée avec success!'; // Set success message

    // Redirect to the home page with the success message
    router.push({ name: 'ListVoiture', query: { message: successMessage.value } });
  }
  catch (error) {
    console.error('Error updating voiture:', error.response ? error.response.data : error.message);
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

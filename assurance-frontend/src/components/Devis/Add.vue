<template>
  <div class="container my-5">
    <h1 class="text-white mb-4">Ajouter un nouveau Devis</h1>

    <form @submit.prevent="addDevis" class="bg-dark p-4 rounded">
      <div class="mb-3">
        <label for="client" class="form-label text-white">Client</label>
        <select id="client" v-model="selectedClientId" @change="fetchVoitures" class="form-select" required>
          <option value="" disabled>Sélectionner un client</option>
          <option v-for="client in clients" :key="client.id" :value="client.id">
            {{ client.nom }} {{ client.prenom }}
          </option>
        </select>
      </div>

      <div class="mb-3" v-if="voitures.length > 0">
        <label for="voiture" class="form-label text-white">Voiture</label>
        <select id="voiture" v-model="devis.voitures" class="form-select" multiple required>
          <option value="" disabled>Sélectionner une voiture</option>
          <option v-for="voiture in voitures" :key="voiture.id" :value="voiture.id">
            {{ voiture.numero_immatriculation }}
          </option>
        </select>
      </div>

      <div class="mb-3">
        <label for="prix" class="form-label text-white">Prix</label>
        <input type="number" id="prix" v-model="devis.prix" class="form-control" required />
      </div>

      <div class="mb-3">
        <label for="frequence_prix" class="form-label text-white">Fréquence Prix</label>
        <input type="text" id="frequence_prix" v-model="devis.frequence_prix" class="form-control" required />
      </div>

      <div class="mb-3">
        <label for="date_effet" class="form-label text-white">Date d'effet</label>
        <input type="date" id="date_effet" v-model="devis.date_effet" class="form-control" required />
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
const voitures = ref([]);
const selectedClientId = ref(null);

const devis = ref({
  date_effet: '',
  prix: null,
  frequence_prix: '',
  voitures: null,
});

const getClients = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/clients/');
    clients.value = response.data;
  } catch (error) {
    console.error('Error fetching clients:', error);
  }
};

const fetchVoitures = async () => {
  if (selectedClientId.value) {
    try {
      const response = await axios.get(`http://localhost:8000/api/client/${selectedClientId.value}`);
      voitures.value = response.data.voitures || [];
      devis.value.voitures = null;
    } catch (error) {
      console.error('Error fetching voitures:', error);
    }
  } else {
    voitures.value = [];
    devis.value.voitures = null;
  }
};

onMounted(() => {
  getClients();
});

const addDevis = async () => {
  devis.value.client_id = selectedClientId.value;
  try {
    console.log('Sending data:', {
      ...devis.value,
      voitures: devis.value.voitures,
    });

    await axios.post('http://localhost:8000/api/devis/post', {
      ...devis.value,
    });

    router.push('/devis');
  } catch (error) {
    console.error('Error adding devis:', error.response ? error.response.data : error.message);
  }
};

const goBack = () => {
  router.push('/devis');
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

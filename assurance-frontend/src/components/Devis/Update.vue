<template>
  <div class="container my-5">
    <h1 class="text-white mb-4">Modifier le Devis</h1>

    <form @submit.prevent="updateDevis" class="bg-dark p-4 rounded">
      <div class="mb-3">
        <label for="client" class="form-label text-white">Client</label>
        <select id="client" v-model="devis.client_id" class="form-select" required>
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
const voitures = ref([]);
const devis = ref({
  id: null,
  date_effet: '',
  client_id: null,
  prix: '',
  frequence_prix: '',
  voitures: [], // Include voiture IDs here
});

// Fetch clients
const getClients = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/clients');
    clients.value = response.data;
  } catch (error) {
    console.error('Error fetching clients:', error);
  }
};

// Fetch voitures
const getVoitures = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/voitures');
    voitures.value = response.data;
  } catch (error) {
    console.error('Error fetching voitures:', error);
  }
};

// Fetch existing devis data
const getDevis = async (id) => {
  try {
    const response = await axios.get(`http://localhost:8000/api/devis/${id}`);
    const fetchedDevis = response.data;

    // Populate the devis object with existing data
    devis.value = {
      ...fetchedDevis,
      client_id: fetchedDevis.client?.id || null,
      voitures: fetchedDevis.voitures.map(voiture => voiture.id), // Fetch voiture IDs
    };
  } catch (error) {
    console.error('Error fetching devis:', error);
  }
};

// Update the devis
const updateDevis = async () => {
  try {
    await axios.put(`http://localhost:8000/api/devis/edit/${devis.value.id}`, devis.value);
    router.push('/devis');
  } catch (error) {
    console.error('Error updating devis:', error.response ? error.response.data : error.message);
  }
};

// Navigate back
const goBack = () => {
  router.push('/devis');
};

// Fetch clients, voitures, and the devis data when the component mounts
onMounted(() => {
  getClients();
  getVoitures();
  const devisId = router.currentRoute.value.params.id;
  if (devisId) {
    getDevis(devisId);
  }
});
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

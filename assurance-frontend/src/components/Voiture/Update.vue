<template>
  <div>
    <h1>Modifier la voiture</h1>
    <form @submit.prevent="updateVoiture">
      <div class="mb-3">
        <label for="client" class="form-label">Client</label>
        <select id="client" v-model="voiture.client_id" class="form-select" required>
          <option value="" disabled>Select a client</option>
          <option v-for="client in clients" :key="client.id" :value="client.id">
            {{ client.nom }} {{ client.prenom }}
          </option>
        </select>
      </div>
      <div class="mb-3">
        <label for="usage" class="form-label">Usage</label>
        <input type="text" id="usage" v-model="voiture.voiture_usage" class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="emplacement" class="form-label">Emplacement</label>
        <input type="text" id="emplacement" v-model="voiture.emplacement" class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="date_achat" class="form-label">Date d'achat</label>
        <input type="date" id="date_achat" v-model="voiture.date_achat" class="form-control" required />
      </div>
      <button type="submit" class="btn btn-primary">Modifier la voiture</button>
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
const voiture = ref({
  date_achat: '',
  voiture_usage: '',
  numero_immatriculation: '',
  emplacement: '',
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
// const getDevis = async () => {
//   try {
//     const response = await axios.get('http://localhost:8000/api/devis');
//     devis.value = response.data;
//   } catch (error) {
//     console.error('Error fetching devis:', error);
//   }
// };
onMounted(() => {
  getClients();
  const voitureId = router.currentRoute.value.params.id; // Get the ID from the route parameters
  if (voitureId) {
    getVoiture(voitureId);
  }
});

const getVoiture = async (id) => {
  try {
    const response = await axios.get(`http://localhost:8000/api/voiture/${id}`);
    const fetchedVoiture = response.data;

    voiture.value = {
      ...fetchedVoiture,
      client_id: fetchedVoiture.client?.id || null,
    };  } catch (error) {
    console.error('Error fetching voiture:', error);
  }
};

// Function to update the devis
const updateVoiture = async () => {
  try {
    await axios.put(`http://localhost:8000/api/voiture/edit/${voiture.value.id}`, voiture.value);
    router.push('/voitures');
  } catch (error) {
    console.error('Error updating devis:', error.response ? error.response.data : error.message);
  }
};

const goBack = () => {
  router.push('/voitures');
};

</script>

<style scoped>
</style>

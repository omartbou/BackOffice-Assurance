<template>
  <NavBar/>

  <div class="container-fluid">
    <div class="row my-5">
      <div class="col-md-12">
        <!-- Add Button -->
        <div class="d-flex justify-content-between mb-3">
          <h2 class="text-white mb-3">Liste des devis :</h2>
          <button class="btn btn-add" @click="showAddDevisModal" data-bs-toggle="modal" data-bs-target="#addDevisModal">
            <i class="fas fa-plus"></i>
          </button>
        </div>

        <div v-if="loading" class="d-flex justify-content-center">
          <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Chargement...</span>
          </div>
        </div>

        <!-- Error Message -->
        <div v-if="errorMessage" class="alert alert-danger" role="alert">
          {{ errorMessage }}
        </div>

        <!-- Devis List -->
        <div v-for="d in devis" :key="d.id" class="card mb-3 text-white" v-if="!loading">
          <div class="card-body d-flex justify-content-between align-items-center">
            <div v-if="d.voitures && d.voitures.length > 0">
              <strong v-for="voiture in d.voitures" :key="voiture.id">
                {{ voiture.numero_immatriculation }}
              </strong>
            </div>
            <div v-else><strong> N/A </strong></div>
            <div>
              <p class="mb-0">{{ d.client?.nom || 'N/A' }} {{ d.client?.prenom || 'N/A' }}</p>
            </div>
            <div>
              <p class="mb-0">{{ d.prix }} € / {{ d.frequence_prix }}</p>
            </div>
            <div>
              <button class="btn btn-view me-2" @click="showDetail(d.id)" data-bs-toggle="modal" data-bs-target="#devisDetailModal">
                <i class="fas fa-eye"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <Detail ref="detail" />
    <AddDevisModal @devisAdded="getDevis" />
  </div>
</template>
<script setup>
import axios from 'axios';
import { ref, onMounted } from 'vue';
import Detail from './Detail.vue';
import NavBar from '../Navbar/NavBar.vue';
import AddDevisModal from './Add.vue';

const devis = ref([]);
const loading = ref(true);
const errorMessage = ref(''); // New ref for error messages
const detail = ref(null);

const showDetail = async (id) => {
  if (detail.value) {
    await detail.value.showDevisDetails(id); // Access the method of Detail component
  }
};

const showAddDevisModal = () => {
  const modal = new bootstrap.Modal(document.getElementById('addDevisModal'));
  modal.show();
};

const getDevis = async () => {
  loading.value = true;
  errorMessage.value = ''; // Clear previous error message

  try {
    const response = await axios.get('http://localhost:8000/api/devis');
    devis.value = response.data;
    console.log('API Response:', response.data); // Log the entire response
    console.log('Fetched devis:', devis.value); // Log the fetched data
  } catch (error) {
    console.error('Error fetching devis:', error);
    errorMessage.value = 'Erreur lors du chargement des devis. Veuillez réessayer.'; // Set error message
  } finally {
    loading.value = false; // End loading
  }
};

const formatDate = (date) => {
  if (!date) return ''; // Handle cases where date might be null or undefined
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(date).toLocaleDateString('fr-FR', options);
};

onMounted(() => {
  getDevis();
});
</script>
<style scoped>
.container-fluid {
  background-color: #191634; /* Dark purple background */
  padding: 20px;
  border-radius: 15px;
}

h2 {
  font-weight: bold;
}
.col-md-12{
  background-color: #302c55;
  border-radius: 15px;
  padding: 15px;

}
.card{
  border:none;
  background-color: #130434;
  border-radius: 20px;

}
.card-body {
  padding: 30px;
}

.btn-add {
  background-color: #05c46b;
  border-radius: 50%;
  color: black;
  width: 32px;
  height: 32px;
  padding: 0;
  text-align: center;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
}
.btn-view {
  background-color: #0080FF;
  border-radius: 50%;
  color: black;
  width: 32px;
  height: 32px;
  padding: 0;
  text-align: center;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
}


.text-white {
  color: #ffffff;
}
</style>

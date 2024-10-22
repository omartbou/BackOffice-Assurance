<template>
  <NavBar/>

  <div class="container-fluid">
    <div class="row my-5">
      <div class="col-md-12">
        <div class="d-flex justify-content-between mb-3">
          <h2 class="text-white mb-3">Liste des voitures :</h2>
          <button class="btn btn-add" @click="showAddVoitureModal" data-bs-toggle="modal" data-bs-target="#addVoitureModal">
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

        <!-- Success Message -->
        <div v-if="successMessage" class="alert alert-success" role="alert">
          {{ successMessage }}
        </div>

        <!-- Voiture List -->
        <div v-for="voiture in voitures" :key="voiture.id" class="card mb-3 text-white" v-if="!loading">
          <div class="card-body d-flex justify-content-between align-items-center">
            <div>
              <strong>{{ voiture.numero_immatriculation || 'N/A' }}</strong>
            </div>
            <div>
              <p class="mb-0">{{ voiture.voiture_usage }}</p>
            </div>
            <div>
              <p class="mb-0">{{ formatDate(voiture.date_achat) }}</p>
            </div>
            <div>
              <div class="d-flex">
                <button class="btn btn-view me-2" @click="showDetail(voiture.id)" data-bs-toggle="modal" data-bs-target="#voitureDetailModal">
                  <i class="fas fa-eye"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Detail ref="detail" />
    <Add @voitureAdded="getVoiture" />

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmer la suppression</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Êtes-vous sûr de vouloir supprimer cette voiture ?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="button" class="btn btn-danger" @click="deleteVoiture(voitureIdToDelete)" data-bs-dismiss="modal">Supprimer</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios';
import { ref, onMounted, computed } from 'vue';
import Detail from './Detail.vue';
import NavBar from '../Navbar/NavBar.vue';
import { useRoute } from 'vue-router';
import Add from './Add.vue';

const voitures = ref([]);
const detail = ref(null);
const route = useRoute();
const errorMessage = ref(''); // New ref for error messages
const successMessage = ref(''); // New ref for success messages
const voitureIdToDelete = ref(null);
const itemsPerPage = ref(5);
const currentPage = ref(1);
const loading = ref(true); // Track loading state

const showDetail = async (id) => {
  if (detail.value) {
    await detail.value.showVoitureDetails(id);
  }
};

const showAddVoitureModal = () => {
  const modal = new bootstrap.Modal(document.getElementById('addVoitureModal'));
  modal.show();
};

// Fetch cars on component mount
const getVoiture = async () => {
  loading.value = true; // Start loading
  errorMessage.value = ''; // Clear previous error message
  successMessage.value = ''; // Clear previous success message

  try {
    const response = await axios.get('http://localhost:8000/api/voitures');
    voitures.value = response.data;
  } catch (error) {
    console.error('Error fetching voitures:', error);
    errorMessage.value = 'Erreur lors du chargement des voitures.'; // Set error message
  } finally {
    loading.value = false; // End loading
  }
};

const confirmDelete = (id) => {
  voitureIdToDelete.value = id;
};

const deleteVoiture = async (id) => {
  errorMessage.value = ''; // Clear previous error message
  successMessage.value = ''; // Clear previous success message

  try {
    await axios.delete(`http://localhost:8000/api/voiture/delete/${id}`);
    successMessage.value = "Voiture supprimée avec succès."; // Set success message
    await getVoiture();
  } catch (error) {
    console.error('Error deleting voiture:', error);
    errorMessage.value = "Erreur lors de la suppression."; // Set error message
  }
};

// Utility Functions
const formatDate = (date) => {
  if (!date) return '';
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(date).toLocaleDateString('fr-FR', options);
};

// Calculate total number of pages
const totalPages = computed(() => {
  return Math.ceil(voitures.value.length / itemsPerPage.value);
});

// Handle page navigation
const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
  }
};

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};

onMounted(() => {
  getVoiture(); // Fetch the list of cars on component mount
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

.col-md-12 {
  background-color: #302c55;
  border-radius: 15px;
  padding: 15px;
}

.card {
  border: none;
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

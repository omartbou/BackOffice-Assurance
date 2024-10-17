<template>
  <NavBar/>

  <div class="container my-5">
    <div class="row">
      <div class="col-md-12">
        <h2 class="text-white mb-4">Liste des voitures :</h2>
      </div>
      <div class="col-md-12">
        <div class="card h-100 bg-dark text-white">
          <div class="d-flex justify-content-end">
            <router-link :to="{ name: 'AddVoiture' }" class="btn btn-success mb-3">
              <i class="fas fa-plus"></i>
            </router-link>
          </div>
          <div class="d-flex justify-content-center">
            <div v-if="message" class="alert alert-success col-md-5 d-flex justify-content-center" role="alert">
              {{ message }}
            </div>
          </div>
          <div class="card-body">
            <!-- Loading Spinner -->
            <div v-if="loading" class="d-flex justify-content-center">
              <div class="spinner-border text-light" role="status">
                <span class="visually-hidden">Chargement...</span>
              </div>
            </div>

            <!-- Table (Hidden during loading) -->
            <div v-else>
              <table class="table table-hover table-dark">
                <thead>
                <tr>
                  <th scope="col">Numéro Immatriculation</th>
                  <th scope="col">Usage</th>
                  <th scope="col">Date d'Achat</th>
                  <th scope="col">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="voiture in paginatedVoitures" :key="voiture.id">
                  <td>{{ voiture.numero_immatriculation || 'N/A' }}</td>
                  <td>{{ voiture.voiture_usage }}</td>
                  <td>{{ formatDate(voiture.date_achat) }}</td>
                  <td>
                    <div class="d-flex">
                      <button class="btn btn-info me-2" @click="showDetail(voiture.id)" data-bs-toggle="modal" data-bs-target="#voitureDetailModal">
                        <i class="fas fa-eye"></i>
                      </button>
                      <router-link :to="{ name: 'UpdateVoiture', params: { id: voiture.id } }" class="btn btn-warning me-2">
                        <i class="fas fa-pencil"></i>
                      </router-link>
                      <button class="btn btn-danger" @click.prevent="confirmDelete(voiture.id)" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                </tbody>
              </table>

              <!-- Pagination Controls -->
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  Afficher
                  <select v-model="itemsPerPage" @change="currentPage = 1" class="form-select form-select-sm d-inline" style="width: auto;">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                  </select>
                  voitures par page
                </div>
              </div>

              <div class="pagination justify-content-center">
                <button @click="prevPage" :disabled="currentPage === 1" class="btn btn-secondary">Précédent</button>
                <span class="mx-2">Page {{ currentPage }} sur {{ totalPages }}</span>
                <button @click="nextPage" :disabled="currentPage === totalPages" class="btn btn-secondary">Suivant</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <Detail ref="detail" />

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

const voitures = ref([]);
const detail = ref(null);
const route = useRoute();
const message = ref(route.query.message || '');
const voitureIdToDelete = ref(null);
const itemsPerPage = ref(5);
const currentPage = ref(1);
const loading = ref(true); // Track loading state

const showDetail = async (id) => {
  if (detail.value) {
    await detail.value.showVoitureDetails(id);
  }
};

setTimeout(() => {
  message.value = '';
}, 5000);

// Fetch cars on component mount
const getVoiture = async () => {
  loading.value = true; // Start loading
  try {
    const response = await axios.get('http://localhost:8000/api/voitures');
    voitures.value = response.data;
  } catch (error) {
    console.error('Error fetching voitures:', error);
  } finally {
    loading.value = false; // End loading
  }
};

const confirmDelete = (id) => {
  voitureIdToDelete.value = id;
};

const deleteVoiture = async (id) => {
  try {
    await axios.delete(`http://localhost:8000/api/voiture/delete/${id}`);
    message.value = "Voiture supprimée avec succès."; // Set success message
    await getVoiture();
  } catch (error) {
    console.error('Error deleting voiture:', error);
    message.value = "Erreur lors de la suppression.";
  }
};

// Utility Functions
const formatDate = (date) => {
  if (!date) return '';
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(date).toLocaleDateString('fr-FR', options);
};

const paginatedVoitures = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return voitures.value.slice(start, start + itemsPerPage.value);
});

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
.table {
  border-collapse: separate;
  border-spacing: 0 15px; /* Space between table rows */
}

.table-dark {
  background-color: #343a40; /* Dark background for the table */
}

.card {
  border-radius: 10px; /* Rounded corners for the card */
}

.btn-warning {
  background-color: #ffc107; /* Custom warning button color */
  color: #212529; /* Dark text color */
}

.btn-info {
  background-color: #17a2b8; /* Custom info button color */
}

.btn-danger {
  background-color: #dc3545; /* Custom danger button color */
}

.text-white {
  color: #ffffff; /* White text color */
}

.spinner-border {
  width: 3rem;
  height: 3rem;
}

.visually-hidden {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  border: 0;
}
</style>

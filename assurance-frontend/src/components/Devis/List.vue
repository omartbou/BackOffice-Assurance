<template>
  <NavBar />

  <div class="container">
    <div class="row my-5">
      <div class="col-md-12">
        <h2 class="text-white mb-3">Liste des devis :</h2>
      </div>
      <div class="col-md-12">
        <div class="card h-100 bg-dark text-white">

          <div class="d-flex justify-content-end">
            <router-link class="btn btn-success" :to="{ name: 'AddDevis' }">
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
                  <th scope="col">Auto</th>
                  <th scope="col">Client</th>
                  <th scope="col">Date d'effet</th>
                  <th scope="col">Prix & Fréquence de prix</th>
                  <th scope="col">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="d in paginatedDevis" :key="d.id">
                  <td>
                    <div v-if="d.voitures && d.voitures.length > 0">
                      <strong v-for="voiture in d.voitures" :key="voiture.id">
                        {{ voiture.numero_immatriculation || 'N/A' }}
                      </strong>
                    </div>
                    <div v-else>
                      N/A
                    </div>
                  </td>
                  <td>{{ d.client?.nom || 'N/A' }} {{ d.client?.prenom || 'N/A' }}</td>
                  <td>{{ formatDate(d.date_effet) }}</td>
                  <td>{{ d.prix }}/ {{ d.frequence_prix }}</td>
                  <td>
                    <div class="d-flex">
                      <button class="btn btn-info me-2" @click="showDetail(d.id)" data-bs-toggle="modal" data-bs-target="#devisDetailModal">
                        <i class="fas fa-eye"></i>
                      </button>
                      <router-link :to="{ name: 'UpdateDevis', params: { id: d.id } }" class="btn btn-warning me-2">
                        <i class="fas fa-pencil"></i>
                      </router-link>
                      <button class="btn btn-danger" @click.prevent="openConfirmationModal(d.id)">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                </tbody>
              </table>

              <div class="d-flex justify-content-between align-items-center">
                <div>
                  Afficher
                  <select v-model="itemsPerPage" @change="currentPage = 1" class="form-select form-select-sm d-inline" style="width: auto;">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                  </select>
                  devis par page
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

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmationModalLabel">Confirmer la suppression</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Êtes-vous sûr de vouloir supprimer ce devis ?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="button" class="btn btn-danger" @click="deleteDevis(confirmedDevisId)" data-bs-dismiss="modal">Supprimer</button>
          </div>
        </div>
      </div>
    </div>

    <Detail ref="detail" />
  </div>
</template>

<script setup>
import axios from 'axios';
import { ref, onMounted, computed } from 'vue';
import Detail from './Detail.vue';
import NavBar from '../Navbar/NavBar.vue';
import { useRoute } from 'vue-router';

const devis = ref([]);
const message = ref(useRoute().query.message || '');
const detail = ref(null);
const confirmedDevisId = ref(null); // To hold the ID of the devis to delete
const loading = ref(true); // Track loading state

// Pagination
const itemsPerPage = ref(5); // Number of items to display per page
const currentPage = ref(1); // Current page

const clearMessage = () => {
  setTimeout(() => {
    message.value = '';
  }, 5000);
};

clearMessage();

const showDetail = async (id) => {
  if (detail.value) {
    await detail.value.showDevisDetails(id); // Access the method of Detail component
  }
};

const getDevis = async () => {
  loading.value = true; // Start loading
  try {
    const response = await axios.get('http://localhost:8000/api/devis');
    devis.value = response.data;
    console.log('API Response:', response.data); // Log the entire response
    console.log('Fetched devis:', devis.value); // Log the fetched data
  } catch (error) {
    console.error('Error fetching devis:', error);
  } finally {
    loading.value = false; // End loading
  }
};

const openConfirmationModal = (id) => {
  confirmedDevisId.value = id; // Set the ID to confirm
  const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
  modal.show(); // Show the modal
};

const deleteDevis = async (id) => {
  try {
    await axios.delete(`http://localhost:8000/api/devis/delete/${id}`);
    message.value = "Action effectuée avec succès."; // Set success message
    await getDevis(); // Refresh the list after deletion
  } catch (error) {
    console.error('Error deleting devis:', error);
    message.value = "Erreur lors de la suppression."; // Set error message if deletion fails
  }
};

const formatDate = (date) => {
  if (!date) return ''; // Handle cases where date might be null or undefined
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(date).toLocaleDateString('fr-FR', options);
};

// Computed property for paginated devis
const paginatedDevis = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return devis.value.slice(start, start + itemsPerPage.value);
});

// Calculate total number of pages
const totalPages = computed(() => {
  return Math.ceil(devis.value.length / itemsPerPage.value);
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
  getDevis();
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

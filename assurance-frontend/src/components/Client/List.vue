<template>
  <NavBar />
  <div class="container">
    <div class="row my-5">
      <div class="col-md-12">
        <h2 class="text-white">Liste des clients :</h2>
      </div>
      <div class="col-md-12">
        <div class="card h-100 bg-dark text-white">
          <div class="d-flex justify-content-end">
            <router-link :to="{ name: 'AddClient' }" class="btn btn-success mb-3">
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
                  <th scope="col">Type</th>
                  <th scope="col">Nom Complet</th>
                  <th scope="col">Date de naissance</th>
                  <th scope="col">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="client in paginatedClients" :key="client.id">
                  <td>{{ getPersonType(client.est_personne) }}</td>
                  <td>{{ client.nom }} {{ client.prenom }}</td>
                  <td>{{ formatDate(client.date_naissance) }}</td>
                  <td>
                    <div class="d-flex">
                      <button class="btn btn-info me-2" @click="showDetail(client.id)" data-bs-toggle="modal" data-bs-target="#clientDetailModal">
                        <i class="fas fa-eye"></i>
                      </button>
                      <router-link :to="{ name: 'UpdateClient', params: { id: client.id } }" class="btn btn-warning me-2">
                        <i class="fas fa-pencil"></i>
                      </router-link>
                      <button @click.prevent="openConfirmationModal(client.id)" class="btn btn-danger">
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
                  clients par page
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
            Êtes-vous sûr de vouloir supprimer ce client ?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="button" class="btn btn-danger" @click="deleteClient(confirmedClientId)" data-bs-dismiss="modal">Supprimer</button>
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

// Variables and States
const route = useRoute();
const message = ref(route.query.message || '');
const confirmedClientId = ref(null); // To hold the ID of the client to delete
const clients = ref([]);
const detail = ref(null);
const loading = ref(true); // Track loading state

// Pagination States
const currentPage = ref(1);
const itemsPerPage = ref(5); // Default clients per page

// Auto-dismiss success message after 5 seconds
setTimeout(() => {
  message.value = '';
}, 5000);

// Fetch clients on mount
onMounted(() => {
  getClients();
});

// API calls
const getClients = async () => {
  loading.value = true; // Start loading
  try {
    const response = await axios.get('http://localhost:8000/api/clients');
    clients.value = response.data;
  } catch (error) {
    console.error('Error fetching clients:', error);
  } finally {
    loading.value = false; // End loading
  }
};

// Delete client function
const deleteClient = async (id) => {
  try {
    await axios.delete(`http://localhost:8000/api/clients/${id}`);
    message.value = "Action effectuée avec succès.";
    await getClients();
  } catch (error) {
    console.error('Error deleting client:', error);
    message.value = "Erreur lors de la suppression.";
  }
};

// Utility Functions
const formatDate = (date) => {
  if (!date) return ''; // Handle cases where date might be null or undefined
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(date).toLocaleDateString('fr-FR', options);
};

const getPersonType = (estPersonne) => {
  return estPersonne ? 'Professionnel' : 'Particulier';
};

// Modal handling
const openConfirmationModal = (id) => {
  confirmedClientId.value = id; // Set the ID to confirm
  const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
  modal.show(); // Show the modal
};

// Detail view function
const showDetail = async (id) => {
  if (detail.value) {
    await detail.value.showClientDetails(id);
  }
};

// Pagination logic
const paginatedClients = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return clients.value.slice(start, start + itemsPerPage.value);
});

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

// Total pages for pagination
const totalPages = computed(() => {
  return Math.ceil(clients.value.length / itemsPerPage.value);
});

// Change page function
const changePage = (page) => {
  if (page < 1 || page > totalPages.value) return; // Validate page number
  currentPage.value = page;
};
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

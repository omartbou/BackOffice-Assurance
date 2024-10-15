<template>
  <div>
    <!-- Bootstrap Modal -->
    <div class="modal fade" id="clientDetailModal" tabindex="-1" aria-labelledby="clientDetailModalLabel" aria-hidden="true" @hidden.bs.modal="resetClientDetails">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="clientDetailModalLabel">Client Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p><strong>Nom:</strong> {{ client.nom }}</p>
            <p><strong>Prénom:</strong> {{ client.prenom }}</p>
            <p><strong>Date de Naissance:</strong> {{ formatDate(client.date_naissance) }}</p>
            <p><strong>Type:</strong> {{ getPersonType(client.est_personne) }}</p>

            <h6>Devis:</h6>
            <div v-if="devis.length">
              <ul class="list-group">
                <li v-for="d in devis" :key="d.id" class="list-group-item">
                  <strong>Numéro:</strong> {{ d.numero }} -
                  <strong>Prix:</strong> {{ d.prix }} / {{ d.frequence_prix }}
                </li>
              </ul>
            </div>
            <div v-else>
              <p>Aucun devis associé.</p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const client = ref({
  nom: '',
  prenom: '',
  date_naissance: '',
  est_personne: 0,
});
const devis = ref([]); // Reactive state for devis

// Function to format the date
const formatDate = (dateString) => {
  if (!dateString) return 'N/A'; // Handle null values
  const date = new Date(dateString);
  return date.toLocaleDateString('fr-FR'); // Format date to 'DD/MM/YYYY' for French locale
};

// Function to get the client type
const getPersonType = (estPersonne) => {
  return estPersonne ? 'Professionnel' : 'Particulier';
};

// Function to fetch client details by ID
const fetchClientDetails = async (id) => {
  try {
    const response = await axios.get(`http://localhost:8000/api/client/${id}`);
    Object.assign(client.value, response.data); // Assign fetched data to client object
    devis.value = response.data.devis || [];
    devis.value.devis = null;

    // Fetch associated devis
  } catch (error) {
    console.error('Error fetching client data:', error.response ? error.response.data : error.message);
  }
};

// Expose the function to the parent component
const showClientDetails = async (id) => {
  await fetchClientDetails(id);
  const modal = new bootstrap.Modal(document.getElementById('clientDetailModal'));
  modal.show(); // Show the modal
};

// Function to reset client details when modal is closed
const resetClientDetails = () => {
  client.value = { nom: '', prenom: '', date_naissance: '', est_personne: 0 }; // Reset client data
  devis.value = []; // Reset devis data
};

defineExpose({
  showClientDetails,
});
</script>

<style scoped>
/* Add any additional styling here */
</style>

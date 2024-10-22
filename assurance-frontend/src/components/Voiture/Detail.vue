<template>
  <div>
    <!-- Bootstrap Modal for Voiture Details -->
    <div class="modal fade" id="voitureDetailModal" tabindex="-1" aria-hidden="true" @hidden.bs.modal="resetVoitureDetails">
      <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-white">
          <!-- Modal Header -->
          <div class="modal-header">
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" @click="hideModal" aria-label="Close"></button>
          </div>

          <!-- Modal Body -->
          <div class="modal-body">
            <h3 class="d-flex justify-content-center">Détails de la voiture</h3>
            <p class="text-center">Ci-dessous, vous trouverez les détails complets d'une voiture.</p>
            <div v-if="successMessage" class="alert alert-success" role="alert">
              {{ successMessage }}
            </div>
            <div v-if="errorMessage" class="alert alert-danger" role="alert">
              {{ errorMessage }}
            </div>
            <div class="mb-3 text-end">
              <a @click="toggleEditMode" class="me-2">
                <i v-if="isEditing" class="fas fa-save btn-save"></i>
                <i v-else class="fas fa-pencil btn-edit"></i>
              </a>

              <a @click.prevent="confirmDelete(voiture.id)">
                <i class="fas fa-times btn-delete"></i>
              </a>
            </div>

            <!-- Main Details -->
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label"><strong>Emplacement :</strong></label>
                  <input type="text" class="form-control bg-dark text-white" v-model="voiture.emplacement" :readonly="!isEditing" required />
                  <div v-if="validationErrors.emplacement" class="text-danger">{{ validationErrors.emplacement }}</div>
                </div>
                <div class="mb-3">
                  <label class="form-label"><strong>Numero Immatriculation :</strong></label>
                  <input type="text" class="form-control bg-dark text-white" v-model="voiture.numero_immatriculation" :readonly="!isEditing" required />
                  <div v-if="validationErrors.numero_immatriculation" class="text-danger">{{ validationErrors.numero_immatriculation }}</div>
                </div>

                <div class="mb-3">
                  <label class="form-label"><strong>Date d'achat :</strong></label>
                  <input type="date" class="form-control bg-dark text-white" v-model="voiture.date_achat" :readonly="!isEditing" required />
                  <div v-if="validationErrors.date_achat" class="text-danger">{{ validationErrors.date_achat }}</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label"><strong>Client :</strong></label>
                  <select class="form-control bg-dark text-white" v-model="voiture.client_id" :disabled="!isEditing" required>
                    <option value="" disabled>Sélectionner un client</option>
                    <option v-for="client in clients" :key="client.id" :value="client.id">
                      {{ client.nom }} {{ client.prenom }}
                    </option>
                  </select>
                  <div v-if="validationErrors.client_id" class="text-danger">{{ validationErrors.client_id }}</div>
                </div>

                <div class="mb-3">
                  <label class="form-label"><strong>Usage :</strong></label>
                  <input type="text" class="form-control bg-dark text-white" v-model="voiture.voiture_usage" :readonly="!isEditing" required />
                  <div v-if="validationErrors.voiture_usage" class="text-danger">{{ validationErrors.voiture_usage }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmation de suppression</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Êtes-vous sûr de vouloir supprimer cette voiture ?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="button" class="btn btn-danger" @click="deleteVoiture">Supprimer</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';

// Reactive voiture object
const voiture = ref({
  client_id: null,
  voiture_usage: '',
  date_achat: '',
  numero_immatriculation: '',
  emplacement: '',
});
const voitureId = ref(null);
const isEditing = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const clients = ref([]);
const validationErrors = ref({});

// Format date function
const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleDateString('fr-FR');
};

// Fetch clients for dropdown
const getClients = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/clients');
    clients.value = response.data;
  } catch (error) {
    console.error('Error fetching clients:', error);
    errorMessage.value = 'Erreur lors de la récupération des clients.';
  }
};

// Fetch voiture details
const fetchVoitureDetails = async (id) => {
  try {
    const response = await axios.get(`http://localhost:8000/api/voiture/${id}`);
    Object.assign(voiture.value, response.data);
    voiture.value.client_id = response.data.client?.id || null;
    voiture.value.date_achat = response.data.date_achat ? new Date(response.data.date_achat).toISOString().split('T')[0] : '';

  } catch (error) {
    console.error('Error fetching voiture data:', error.response ? error.response.data : error.message);
    errorMessage.value = 'Erreur lors de la récupération des détails de la voiture.';
  }
};

// Toggle edit mode
const toggleEditMode = () => {
  if (isEditing.value) {
    if (validateVoiture()) {
      const voiturePlayload = {
        date_achat: voiture.value.date_achat,
        numero_immatriculation: voiture.value.numero_immatriculation,
        client_id: voiture.value.client_id,
        voiture_usage:voiture.value.voiture_usage ,
        emplacement:voiture.value.emplacement ,
      };      axios.put(`http://localhost:8000/api/voiture/edit/${voiture.value.id}`,voiturePlayload)
          .then(response => {
            console.log('Changes saved!');
            successMessage.value = 'Voiture mis à jour avec succès!'; // Set success message
            errorMessage.value = '';
            validationErrors.value = {}; // Clear validation errors
          })
          .catch(error => {
            console.error('Failed to save changes:', error);
            successMessage.value = ''; // Clear success message on error
            errorMessage.value = error.response?.data?.message || 'Échec de la mise à jour de la voiture. Veuillez réessayer.';
          });
    }
  } else {
    successMessage.value = ''; // Clear message when entering edit mode
    errorMessage.value = ''; // Clear message when entering edit mode
    validationErrors.value = {}; // Clear validation errors when entering edit mode
  }
  isEditing.value = !isEditing.value;
};

// Validate voiture fields
const validateVoiture = () => {
  validationErrors.value = {}; // Reset validation errors
  let isValid = true;

  if (!voiture.value.emplacement) {
    validationErrors.value.emplacement = 'L\'emplacement est requis.';
    isValid = false;
  }

  if (!voiture.value.date_achat) {
    validationErrors.value.date_achat = 'La date d\'achat est requise.';
    isValid = false;
  }

  if (!voiture.value.client_id) {
    validationErrors.value.client_id = 'Veuillez sélectionner un client.';
    isValid = false;
  }

  if (!voiture.value.voiture_usage) {
    validationErrors.value.voiture_usage = 'L\'usage de la voiture est requis.';
    isValid = false;
  }
  if (!voiture.value.numero_immatriculation) {
    validationErrors.value.numero_immatriculation = 'Le numero_immatriculation de la voiture est requis.';
    isValid = false;
  }

  return isValid;
};

const confirmDelete = (id) => {
  voitureId.value = id;
  console.log(voitureId.value);
  const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
  modal.show();
};
const deleteVoiture = async () => {
  try {
    await axios.delete(`http://localhost:8000/api/voiture/delete/${voitureId.value}`);
    location.reload(); // Refresh the list
  } catch (error) {
    console.error('Error deleting client:', error);
    errorMessage.value = 'Erreur lors de la suppression du client.';
  }
};
// Show modal with voiture details
const showVoitureDetails = async (id) => {
  await fetchVoitureDetails(id);
  const modal = new bootstrap.Modal(document.getElementById('voitureDetailModal'));
  modal.show();
};

// Reset voiture details when modal is closed
const resetVoitureDetails = () => {
  voiture.value = {
    client_id: null,
    voiture_usage: '',
    date_achat: '',
    numero_immatriculation: '',
    emplacement: '',
  };
  successMessage.value = '';
  errorMessage.value = '';
  validationErrors.value = {}; // Reset validation errors
};
const hideModal = () => {
  resetVoitureDetails();
  cleanBackdrop();
};
const cleanBackdrop = () => {
  setTimeout(() => {
    const backdrop = document.querySelector('.modal-backdrop');
    if (backdrop) {
      backdrop.remove();
    }
    // Remove the modal-open class from the body to allow scrolling again
    document.body.classList.remove('modal-open');
  }, 150);
};
onMounted(() => {
  getClients();
});

defineExpose({
  showVoitureDetails,
});
</script>

<style scoped>
.modal-content {
  background-color: #302c55;
  border-radius: 15px;
  color: #ffffff;
}

.modal-header {
  border-bottom: none;
}

.btn-save, .btn-delete, .btn-edit {
  border-radius: 50%;
  color: white;
  width: 32px;
  height: 32px;
  padding: 0;
  text-align: center;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  cursor: pointer;
}

.btn-save {
  background-color: #0B0B61;
}

.btn-delete {
  background-color: #f1007b;
}

.btn-edit {
  background-color: #ffff6a;
}

.form-label {
  font-weight: bold;
}

.form-control {
  background-color: #2c2248;
  color: #ffffff;
  border: 1px solid #605c84;
}

.text-danger {
  font-size: 0.8rem;
}

p {
  margin-bottom: 5px;
}

strong {
  color: #ffffff;
}

.text-end {
  color: #9d9dff;
  font-size: 0.9rem;
}

.text-center {
  color: #9d9dff;
}

.row {
  background-color: #302c55;
  padding: 30px;
  border-radius: 20px;
}
</style>

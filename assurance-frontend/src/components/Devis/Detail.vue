
<template>
  <div>
    <!-- Bootstrap Modal for Devis Details -->
    <div class="modal fade" id="devisDetailModal" tabindex="-1" aria-hidden="true" >
      <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-white">
          <!-- Modal Header -->
          <div class="modal-header">
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" @click="hideModal" aria-label="Close"></button>
          </div>

          <!-- Modal Body -->
          <div class="modal-body">
            <h3 class="d-flex justify-content-center">Détails du devis</h3>

            <p class="text-center">Ci-dessous, vous trouverez les détails complets des devis d'un client.</p>

            <!-- Success Message -->
            <div v-if="successMessage" class="alert alert-success" role="alert">
              {{ successMessage }}
            </div>

            <!-- Error Messages -->
            <div v-if="errorMessages.length" class="alert alert-danger" role="alert">
              <ul>
                <li v-for="(error, index) in errorMessages" :key="index">{{ error }}</li>
              </ul>
            </div>

            <!-- Edit Toggle Button -->
            <div class="mb-3 text-end">
              <a @click="toggleEditMode" class="me-2">
                <i v-if="isEditing" class="fas fa-save btn-save"></i>
                <i v-else class="fas fa-pencil btn-edit"></i>
              </a>

              <a @click.prevent="confirmDelete(devis.id)">
                <i class="fas fa-times btn-delete"></i>
              </a>
            </div>

            <!-- Main Details -->
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label"><strong>Date d'effet :</strong></label>
                  <input type="date" class="form-control bg-dark text-white" v-model="devis.date_effet" :readonly="!isEditing" />
                  <div v-if="validationErrors.date_effet" class="text-danger">{{ validationErrors.date_effet }}</div>
                </div>

                <div class="mb-3">
                  <label class="form-label"><strong>Prix :</strong></label>
                  <input type="text" class="form-control bg-dark text-white" v-model="devis.prix" :readonly="!isEditing" />
                  <div v-if="validationErrors.prix" class="text-danger">{{ validationErrors.prix }}</div>
                </div>

                <div class="mb-3">
                  <label class="form-label"><strong>Client :</strong></label>
                  <select class="form-control bg-dark text-white" v-model="devis.client_id" @change="fetchVoitures"  :disabled="!isEditing">
                    <option value="" disabled>Sélectionner un client</option>
                    <option v-for="client in clients" :key="client.id" :value="client.id">
                      {{ client.nom }} {{ client.prenom }}
                    </option>
                  </select>
                  <div v-if="validationErrors.client_id" class="text-danger">{{ validationErrors.client_id }}</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label"><strong>N° devis de la compagnie :</strong></label>
                  <input type="text" class="form-control bg-dark text-white" v-model="devis.numero" :readonly="!isEditing" />
                  <div v-if="validationErrors.numero" class="text-danger">{{ validationErrors.numero }}</div>
                </div>

                <div class="mb-3">
                  <label class="form-label"><strong>Fréquence du prix :</strong></label>
                  <select class="form-control bg-dark text-white" v-model="devis.frequence_prix" :disabled="!isEditing">
                    <option>Annuel</option>
                    <option>Mensuel</option>
                  </select>
                </div>
                <div class="mb-3" v-if="voitures.length > 0">
                  <label for="voiture" class="form-label text-white">Voiture</label>
                  <select id="voiture" v-model="devis.voitures"
                          :class="devis.voitures && devis.voitures.length ? validClass : errorClass"
                          class="form-select bg-dark text-white" multiple >
                    <option value="" disabled>Sélectionner une voiture</option>
                    <option v-for="voiture in voitures" :key="voiture.id" :value="voiture.id">
                      {{ voiture.numero_immatriculation }}
                    </option>
                  </select>
                  <div v-if="validationErrors.voitures" class="text-danger">{{ validationErrors.voitures }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirmation Modal for Deletion -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmation de suppression</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Êtes-vous sûr de vouloir supprimer ce devis ?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="button" class="btn btn-danger" @click="deleteDevis">Supprimer</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, nextTick } from 'vue';
import axios from 'axios';

const isEditing = ref(false);
const successMessage = ref('');
const errorMessages = ref([]);
const validationErrors = ref({});
const clients = ref([]);
const selectedClientId = ref(null);
const voitures = ref([]);
const devis = ref({
  numero: '',
  date_effet: '',
  frequence_prix: '',
  prix: '',
  voitures: [],
  client_id: null,
});
const devisIdToDelete = ref(null);
const devisList = ref([]);

const getClients = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/clients');
    clients.value = response.data;
  } catch (error) {
    console.error('Error fetching clients:', error);
  }
};

const fetchVoitures = async () => {
  if (devis.value.client_id) {
    try {
      const response = await axios.get(`http://localhost:8000/api/client/${devis.value.client_id}`);
      voitures.value = response.data.voitures || [];
      devis.value.voitures = [];
    } catch (error) {
      console.error('Error fetching voitures:', error);
    }
  } else {
    voitures.value = [];
    devis.value.voitures = [];
  }
};

// Validation function
const validateDevis = () => {
  validationErrors.value = {}; // Reset validation errors
  let isValid = true;

  if (!devis.value.prix || isNaN(devis.value.prix)) {
    validationErrors.value.prix = "Le prix doit être un nombre valide.";
    isValid = false;
  }

  if (!devis.value.client_id) {
    validationErrors.value.client_id = "Veuillez sélectionner un client.";
    isValid = false;
  }

  if (!devis.value.numero) {
    validationErrors.value.numero = "Le numéro du devis est requis.";
    isValid = false;
  }

  return isValid;
};

const toggleEditMode = () => {
  if (isEditing.value) {
    // Validate before saving
    if (validateDevis()) {
      axios.put(`http://localhost:8000/api/devis/edit/${devis.value.id}`, devis.value)
          .then(response => {
            console.log('Changes saved!');
            successMessage.value = 'Devis mis à jour avec succès!';
            errorMessages.value = []; // Clear error messages
          })
          .catch(error => {
            console.error('Failed to save changes:', error);
            errorMessages.value = error.response?.data.errors || ['Erreur lors de la mise à jour du devis.']; // Handle server-side errors
            successMessage.value = ''; // Clear message on error
          });
    }
  } else {
    successMessage.value = '';
    errorMessages.value = []; // Clear error messages when entering edit mode
  }
  isEditing.value = !isEditing.value;
};

const fetchDevisDetails = async (id) => {
  const response = await axios.get(`http://localhost:8000/api/devis/${id}`);
  const fetchedDevis = response.data;
  devis.value = {
    ...fetchedDevis,
    client_id: fetchedDevis.client?.id || null,
    voitures: fetchedDevis.voitures.map(voiture => voiture.id),
    date_effet: fetchedDevis.date_effet ? new Date(fetchedDevis.date_effet).toISOString().split('T')[0] : ''
  };
};

const showDevisDetails = async (id) => {
  await fetchDevisDetails(id);
  nextTick(() => {
    const modalElement = document.getElementById('devisDetailModal');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
  });
};

const refreshDevisList = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/devis');
    devisList.value = response.data;
    console.log("Devis list refreshed:", devisList.value);
  } catch (error) {
    console.error('Error fetching devis list:', error);
  }
};

const confirmDelete = (id) => {
  devisIdToDelete.value = id;
  const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
  modal.show();
};

const deleteDevis = async () => {
  try {
    await axios.delete(`http://localhost:8000/api/devis/delete/${devisIdToDelete.value}`);
    console.log('Devis deleted successfully!');
    location.reload(); // Refresh the list
  } catch (error) {
    console.error('Error deleting devis:', error);
  }
};

const hideModal = () => {
  resetDevisDetails();
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

const resetDevisDetails = () => {
  devis.value = {
    numero: '',
    date_effet: '',
    prix: '',
    frequence_prix: '',
    client_id: null,
    client: {
      nom: '',
      prenom: '',
    },
  };
  successMessage.value = '';
  errorMessages.value = []; // Reset error messages on modal close
};

onMounted(() => {
  getClients();
});

defineExpose({
  showDevisDetails,
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

.btn-save {
  background-color: #0B0B61;
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

.btn-delete {
  background-color: #f1007b;
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
  cursor: pointer;
}

.btn-edit {
  background-color: #ffff6a;
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
  cursor: pointer;
}

.form-label {
  font-weight: bold;
}

.form-control {
  background-color: #2c2248;
  color: #ffffff;
  border: 1px solid #605c84;
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

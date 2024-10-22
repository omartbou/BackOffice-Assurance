<template>
  <div>
    <!-- Bootstrap Modal for Client Details -->
    <div class="modal fade" id="clientDetailModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-white">
          <!-- Modal Header -->
          <div class="modal-header">
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" @click="hideModal" aria-label="Close"></button>
          </div>

          <!-- Modal Body -->
          <div class="modal-body">
            <h3 class="d-flex justify-content-center">Détails du client</h3>
            <p class="text-center">Ci-dessous, vous trouverez les détails complets d'un client.</p>

            <!-- Success Message -->
            <div v-if="successMessage" class="alert alert-success" role="alert">
              {{ successMessage }}
            </div>
            <div v-if="errorMessage" class="alert alert-danger" role="alert">
              {{ errorMessage }}
            </div>

            <div class="mb-3 text-end">
              <a @click="toggleEditMode" class="me-2">
                <i v-if="isEditing && !Object.keys(validationErrors).length" class="fas fa-save btn-save"></i>
                <i v-else class="fas fa-pencil btn-edit"></i>
              </a>
              <a @click.prevent="confirmDelete(client.id)">
                <i class="fas fa-times btn-delete"></i>
              </a>
            </div>

            <!-- Main Details -->
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label"><strong>Nom :</strong></label>
                  <input type="text" class="form-control bg-dark text-white" v-model="client.nom" :readonly="!isEditing" required />
                  <div v-if="validationErrors.nom" class="text-danger">{{ validationErrors.nom }}</div>
                </div>

                <div class="mb-3">
                  <label class="form-label"><strong>Prénom :</strong></label>
                  <input type="text" class="form-control bg-dark text-white" v-model="client.prenom" :readonly="!isEditing" required />
                  <div v-if="validationErrors.prenom" class="text-danger">{{ validationErrors.prenom }}</div>
                </div>

                <div class="mb-3">
                  <label class="form-label"><strong>Date de Naissance :</strong></label>
                  <input type="date" class="form-control bg-dark text-white" v-model="client.date_naissance" :readonly="!isEditing" required />
                  <div v-if="validationErrors.date_naissance" class="text-danger">{{ validationErrors.date_naissance }}</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label"><strong>Type :</strong></label>
                  <select class="form-control bg-dark text-white" v-model="client.est_personne" :disabled="!isEditing" required>
                    <option value="" disabled>Type de client : </option>
                    <option :value="false">Particulier</option>
                    <option :value="true">Professionnel</option>
                  </select>
                  <div v-if="validationErrors.est_personne" class="text-danger">{{ validationErrors.est_personne }}</div>
                </div>

                <div class="mb-3" v-if="!isEditing">
                  <label class="form-label"><strong>Devis :</strong></label>
                  <ul class="list-group">
                    <li v-for="d in devis" :key="d.id" class="list-group-item bg-dark text-white">
                      <strong>Numéro:</strong> {{ d.numero }} - <strong>Prix:</strong> {{ d.prix }} / {{ d.frequence_prix }}
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirmation Delete Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmation de suppression</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" @click="hideModal"></button>
          </div>
          <div class="modal-body">
            Êtes-vous sûr de vouloir supprimer ce client ?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="hideModal">Annuler</button>
            <button type="button" class="btn btn-danger" @click="deleteClient">Supprimer</button>
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
  est_personne: false,
});
const isEditing = ref(false);
const devis = ref([]);
const successMessage = ref('');
const errorMessage = ref('');
const validationErrors = ref({}); // For storing validation errors

// Fetch client details by ID
const fetchClientDetails = async (id) => {
  try {
    const response = await axios.get(`http://localhost:8000/api/client/${id}`);
    Object.assign(client.value, response.data);
    devis.value = response.data.devis || [];
    client.value.date_naissance = response.data.date_naissance ? new Date(response.data.date_naissance).toISOString().split('T')[0] : '';
  } catch (error) {
    console.error('Error fetching client data:', error.response ? error.response.data : error.message);
    errorMessage.value = 'Erreur lors de la récupération des détails du client.';
  }
};

// Show client details in the modal
const showClientDetails = async (id) => {
  await fetchClientDetails(id);
  const modal = new bootstrap.Modal(document.getElementById('clientDetailModal'));
  modal.show();
};

// Confirm delete action
const confirmDelete = (id) => {
  clientId.value = id;
  const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
  modal.show();
};

// Delete client
const deleteClient = async () => {
  try {
    await axios.delete(`http://localhost:8000/api/clients/${clientId.value}`);
    location.reload(); // Refresh the list
  } catch (error) {
    console.error('Error deleting client:', error);
    errorMessage.value = 'Erreur lors de la suppression du client.';
  }
};

// Toggle edit mode
const toggleEditMode = () => {
  if (isEditing.value) {
    // Validate input before saving
    if (validateClient()) {
      const clientPayload = {
        nom: client.value.nom,
        prenom: client.value.prenom,
        date_naissance: client.value.date_naissance,
        est_personne: client.value.est_personne
      };
      axios.put(`http://localhost:8000/api/clients/edit/${client.value.id}`, clientPayload)
          .then(response => {
            console.log('Changes saved!');
            successMessage.value = 'Client mis à jour avec succès!';
            errorMessage.value = '';
            validationErrors.value = {}; // Reset validation errors
          })
          .catch(error => {
            console.error('Failed to save changes:', error);
            successMessage.value = '';
            errorMessage.value = 'Échec de la mise à jour du client. Veuillez réessayer.';
          });
    }
  } else {
    successMessage.value = '';
    errorMessage.value = '';
    validationErrors.value = {}; // Clear validation errors
  }
  isEditing.value = !isEditing.value;
};

// Frontend validation for the client form
const validateClient = () => {
  validationErrors.value = {}; // Reset validation errors
  let isValid = true;

  if (!client.value.nom) {
    validationErrors.value.nom = 'Le nom est requis.';
    isValid = false;
  }

  if (!client.value.prenom) {
    validationErrors.value.prenom = 'Le prénom est requis.';
    isValid = false;
  }

  if (!client.value.date_naissance) {
    validationErrors.value.date_naissance = 'La date de naissance est requise.';
    isValid = false;
  }

  if (client.value.est_personne === '') {
    validationErrors.value.est_personne = 'Veuillez sélectionner un type de client.';
    isValid = false;
  }

  return isValid;
};
const hideModal = () => {
  resetClientDetails();
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
// Reset client details when modal is closed
const resetClientDetails = () => {
  client.value = { nom: '', prenom: '', date_naissance: '', est_personne: false };
  devis.value = [];
  successMessage.value = '';
  errorMessage.value = '';
  validationErrors.value = {}; // Reset validation errors
};

defineExpose({
  showClientDetails,
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

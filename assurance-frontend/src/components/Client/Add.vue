<template>
  <!-- Bootstrap Modal for Adding Client -->
  <div class="modal fade" id="addClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content bg-dark text-white">
        <!-- Modal Header -->
        <div class="modal-header">
          <h5 class="modal-title">Ajouter un client</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" @click="hideModal" aria-label="Close"></button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
          <form @submit.prevent="validateAndSubmit" class="bg-dark p-4 rounded">
            <div class="mb-3">
              <label for="nom" class="form-label text-white">Nom</label>
              <input type="text" id="nom" v-model="client.nom"
                     :class="errors.nom ? errorClass : client.nom.length ? validClass : ''"
                     class="form-control bg-dark text-white"  />
              <div v-if="errors.nom" class="text-danger">{{ errors.nom }}</div>
            </div>
            <div class="mb-3">
              <label for="prenom" class="form-label text-white">Prénom</label>
              <input type="text" id="prenom" v-model="client.prenom"
                     :class="errors.prenom ? errorClass : client.prenom.length ? validClass : ''"
                     class="form-control bg-dark text-white"  />
              <div v-if="errors.prenom" class="text-danger">{{ errors.prenom }}</div>
            </div>
            <div class="mb-3">
              <label for="date_naissance" class="form-label text-white">Date de Naissance</label>
              <input type="date" id="date_naissance" v-model="client.date_naissance"
                     :class="errors.date_naissance ? errorClass : client.date_naissance ? validClass : ''"
                     class="form-control bg-dark text-white"  />
              <div v-if="errors.date_naissance" class="text-danger">{{ errors.date_naissance }}</div>
            </div>
            <div class="mb-3">
              <label for="est_personne" class="form-label text-white">Type</label>
              <select id="est_personne" v-model="client.est_personne"
                      :class="validClass"
                      class="form-select bg-dark text-white" >
                <option value="0">Particulier</option>
                <option value="1">Professionnel</option>
              </select>
            </div>
            <div class="d-flex justify-content-end">
              <button type="submit" class="btn btn-success me-2">Ajouter</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="hideModal">Annuler</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from "axios";

const client = ref({
  nom: '',
  prenom: '',
  date_naissance: '',
  est_personne: false,
});

const errors = ref({});
const validClass = "form-control is-valid";
const errorClass = "form-control is-invalid";
const emit = defineEmits();

// Validate the client data
const validateInputs = () => {
  errors.value = {};
  let isValid = true;

  if (!client.value.nom) {
    errors.value.nom = 'Le nom est requis.';
    isValid = false;
  }
  if (!client.value.prenom) {
    errors.value.prenom = 'Le prénom est requis.';
    isValid = false;
  }
  if (!client.value.date_naissance) {
    errors.value.date_naissance = 'La date de naissance est requise.';
    isValid = false;
  }

  return isValid;
};

// Emit to parent after adding client
const validateAndSubmit = async () => {
  if (validateInputs()) {
    try {
      // Add client via API
      await axios.post('http://localhost:8000/api/clients/post', client.value);
      // Emit event to parent to update clients list
      emit('clientAdded');
      const modal = bootstrap.Modal.getInstance(document.getElementById('addClientModal'));
      modal.hide();
      location.reload();
    } catch (error) {
      if (error.response && error.response.data) {
        // Handle server validation errors
        errors.value = error.response.data.errors || {};
      } else {
        console.error('Error adding client:', error.message);
      }
    }
  }
};

const hideModal = () => {
  resetForm();
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
// Reset form fields
const resetForm = () => {
  client.value = { nom: '', prenom: '', date_naissance: '', est_personne: 0 };
  errors.value = {};
};


</script>

<style scoped>
.modal-content {
  background-color: #302c55;
  border-radius: 15px;
}

.form-control {
  background-color: #2c2248;
  color: #ffffff;
  border: 1px solid #605c84;
}

.form-select {
  background-color: #2c2248;
  color: #ffffff;
  border: 1px solid #605c84;
}

body.modal-open {
  overflow: hidden;
}

.text-danger {
  font-size: 0.875rem;
}
</style>

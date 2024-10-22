<template>
  <!-- Bootstrap Modal for Adding Devis -->
  <div class="modal fade" id="addDevisModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content bg-dark text-white">
        <!-- Modal Header -->
        <div class="modal-header">
          <h5 class="modal-title">Ajouter un nouveau Devis</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" @click="hideModal" aria-label="Close"></button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
          <form @submit.prevent="validateAndSubmit" class="bg-dark p-4 rounded">
            <div class="mb-3">
              <label for="client" class="form-label text-white">Client</label>
              <select id="client" v-model="selectedClientId"
                      :class="selectedClientId ? validClass : errorClass"
                      @change="fetchVoitures" class="form-select bg-dark text-white" >
                <option value="" disabled>Sélectionner un client</option>
                <option v-for="client in clients" :key="client.id" :value="client.id">
                  {{ client.nom }} {{ client.prenom }}
                </option>
              </select>
              <div v-if="errors.client_id" class="text-danger">{{ errors.client_id }}</div>
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
              <div v-if="errors.voitures" class="text-danger">{{ errors.voitures }}</div>
            </div>

            <div class="mb-3">
              <label for="prix" class="form-label text-white">Prix</label>
              <input type="number" id="prix" v-model="devis.prix"
                     :class="devis.prix !== null ? validClass : errorClass"
                     class="form-control bg-dark text-white"  />
              <div v-if="errors.prix" class="text-danger">{{ errors.prix }}</div>
            </div>

            <div class="mb-3">
              <label for="frequence_prix" class="form-label text-white">Fréquence Prix</label>
              <input type="text" id="frequence_prix"
                     v-model="devis.frequence_prix"
                     :class="devis.frequence_prix.length ? validClass : errorClass"
                     class="form-control bg-dark text-white"  />
              <div v-if="errors.frequence_prix" class="text-danger">{{ errors.frequence_prix }}</div>
            </div>

            <div class="mb-3">
              <label for="date_effet" class="form-label text-white">Date d'effet</label>
              <input type="date" id="date_effet" v-model="devis.date_effet"
                     :class="devis.date_effet.length ? validClass : errorClass"
                     class="form-control bg-dark text-white"  />
              <div v-if="errors.date_effet" class="text-danger">{{ errors.date_effet }}</div>
            </div>

            <div class="d-flex">
              <button type="submit" class="btn btn-success me-2">Ajouter</button>
              <button type="button" class="btn btn-secondary" @click="hideModal" data-bs-dismiss="modal">Annuler</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const clients = ref([]);
const voitures = ref([]);
const selectedClientId = ref(null);
const devis = ref({
  date_effet: '',
  prix: null,
  frequence_prix: '',
  voitures: [],
});
const errors = ref({});
const validClass = "form-control is-valid";
const errorClass = "form-control is-invalid";

const getClients = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/clients/');
    clients.value = response.data;
  } catch (error) {
    console.error('Error fetching clients:', error);
  }
};

const fetchVoitures = async () => {
  if (selectedClientId.value) {
    try {
      const response = await axios.get(`http://localhost:8000/api/client/${selectedClientId.value}`);
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

onMounted(() => {
  getClients();
});

const validateInputs = () => {
  errors.value = {};
  let isValid = true;

  if (!selectedClientId.value) {
    errors.value.client_id = 'Le client est requis.';
    isValid = false;
  }
  if (!devis.value.voitures.length) {
    errors.value.voitures = 'Au moins une voiture est requise.';
    isValid = false;
  }
  if (devis.value.prix === null || devis.value.prix < 0) {
    errors.value.prix = 'Le prix est requis et doit être positif.';
    isValid = false;
  }
  if (!devis.value.frequence_prix) {
    errors.value.frequence_prix = "La fréquence de prix est requise.";
    isValid = false;
  }
  if (!devis.value.date_effet) {
    errors.value.date_effet = "La date d'effet est requise.";
    isValid = false;
  }

  return isValid;
};

const validateAndSubmit = async () => {
  if (validateInputs()) {
    devis.value.client_id = selectedClientId.value;
    try {
      await axios.post('http://localhost:8000/api/devis/post', {
        ...devis.value,
      });
      const modal = bootstrap.Modal.getInstance(document.getElementById('addDevisModal'));
      modal.hide();
      location.reload();
      // Optionally show success message here
    } catch (error) {
      if (error.response && error.response.data) {
        // Handle server validation errors
        errors.value = error.response.data.errors || {};
      } else {
        console.error('Error adding devis:', error.message);
      }
    }
  }
};

const hideModal = () => {
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
</script>

<style scoped>
.modal-content {
  background-color: #302c55;
  border-radius: 15px;
}

.form-control,
.form-select {
  background-color: #2c2248;
  color: #ffffff;
  border: 1px solid #605c84;
}

.form-control:focus,
.form-select:focus {
  background-color: #2c2248;
  color: #ffffff;
  border-color: #28a745; /* or other color for focus state */
}

h1 {
  color: #ffffff;
}
.text-danger {
  font-size: 0.875rem;
}
</style>

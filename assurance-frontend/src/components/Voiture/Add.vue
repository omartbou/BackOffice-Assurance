<template>
  <!-- Bootstrap Modal for Adding Voiture -->
  <div class="modal fade" id="addVoitureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content bg-dark text-white">
        <!-- Modal Header -->
        <div class="modal-header">
          <h5 class="modal-title">Ajouter une nouvelle voiture</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" @click="hideModal" aria-label="Close"></button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
          <form @submit.prevent="validateAndSubmit" class="bg-dark p-4 rounded">
            <div class="mb-3">
              <label for="client" class="form-label text-white">Client</label>
              <select id="client" v-model="voiture.client_id"
                      :class="voiture.client_id ? validClass : errorClass"
                      class="form-select" >
                <option value="" disabled>Sélectionner un client</option>
                <option v-for="client in clients" :key="client.id" :value="client.id">
                  {{ client.nom }} {{ client.prenom }}
                </option>
              </select>
              <div v-if="errors.client_id" class="text-danger">{{ errors.client_id }}</div>
            </div>
            <div class="mb-3">
              <label for="usage" class="form-label text-white">Usage</label>
              <input type="text" id="usage" v-model="voiture.voiture_usage"
                     :class="voiture.voiture_usage ? validClass : errorClass"
                     class="form-control"  />
              <div v-if="errors.voiture_usage" class="text-danger">{{ errors.voiture_usage }}</div>
            </div>
            <div class="mb-3">
              <label for="emplacement" class="form-label text-white">Emplacement</label>
              <input type="text" id="emplacement" v-model="voiture.emplacement"
                     :class="voiture.emplacement ? validClass : errorClass"
                     class="form-control"  />
              <div v-if="errors.emplacement" class="text-danger">{{ errors.emplacement }}</div>
            </div>
            <div class="mb-3">
              <label for="numero_immatriculation" class="form-label text-white">Numéro Immatriculation</label>
              <input type="text" id="numero_immatriculation"
                     :class="voiture.numero_immatriculation ? validClass : errorClass"
                     v-model="voiture.numero_immatriculation" class="form-control"  />
              <div v-if="errors.numero_immatriculation" class="text-danger">{{ errors.numero_immatriculation }}</div>
            </div>
            <div class="mb-3">
              <label for="date_achat" class="form-label text-white">Date d'achat</label>
              <input type="date" id="date_achat" v-model="voiture.date_achat"
                     :class="voiture.date_achat ? validClass : errorClass"
                     class="form-control"  />
              <div v-if="errors.date_achat" class="text-danger">{{ errors.date_achat }}</div>
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
const voiture = ref({
  date_achat: '',
  voiture_usage: '',
  numero_immatriculation: '',
  emplacement: '',
  client_id: null,
});
const successMessage = ref('');
const errors = ref({});
const validClass = "form-control is-valid";
const errorClass = "form-control is-invalid";

// Fetch clients from the API
const getClients = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/clients');
    clients.value = response.data;
  } catch (error) {
    console.error('Error fetching clients:', error);
  }
};

onMounted(() => {
  getClients();
});

// Validate form inputs
const validateInputs = () => {
  errors.value = {};
  let isValid = true;

  if (!voiture.value.client_id) {
    errors.value.client_id = 'Le client est requis.';
    isValid = false;
  }
  if (!voiture.value.voiture_usage) {
    errors.value.voiture_usage = "L'usage est requis.";
    isValid = false;
  }
  if (!voiture.value.emplacement) {
    errors.value.emplacement = "L'emplacement est requis.";
    isValid = false;
  }
  if (!voiture.value.numero_immatriculation) {
    errors.value.numero_immatriculation = "Le numéro d'immatriculation est requis.";
    isValid = false;
  }
  if (!voiture.value.date_achat) {
    errors.value.date_achat = "La date d'achat est requise.";
    isValid = false;
  }

  return isValid;
};

// Validate and submit the form
const validateAndSubmit = async () => {
  if (validateInputs()) {
    try {
      await axios.post('http://localhost:8000/api/voiture/post', voiture.value);
      successMessage.value = 'Voiture ajoutée avec succès!'; // Set success message
      router.push({ name: 'ListVoiture', query: { message: successMessage.value } });
    } catch (error) {
      if (error.response && error.response.data) {
        // Handle server validation errors
        errors.value = error.response.data.errors || {};
      } else {
        console.error('Error adding voiture:', error.message);
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
  background-color: #495057;
  color: #ffffff;
}

.form-control:focus,
.form-select:focus {
  background-color: #495057;
  color: #ffffff;
  border-color: #28a745;
}

h1 {
  color: #ffffff;
}
</style>

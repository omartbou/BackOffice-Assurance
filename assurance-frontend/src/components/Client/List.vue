<template>
  <NavBar/>
  <div class="container">
    <div class="row my-5">
      <div class="col-md-12">
        <h2 class="text-white">Liste des clients :</h2>

      </div>
      <div class="col-md-12">
        <div class="card h-100 bg-dark text-white">
          <div class="d-flex justify-content-end">

            <router-link :to="{ name: 'AddClient' }" class="btn btn-success mb-3"><i class="fas fa-plus"></i></router-link>
          </div>
          <div v-if="message" class="alert alert-success" role="alert">
            {{ message }}
          </div>
          <div class="card-body">
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
              <tr v-for="client in clients" :key="client.id">
                <td>{{ getPersonType(client.est_personne) }}</td>
                <td>{{ client.nom }} {{ client.prenom }}</td>
                <td>{{ formatDate(client.date_naissance) }}</td>
                <td>
                  <div class="d-flex">

                  <button class="btn btn-info me-2" @click="showDetail(client.id)" data-bs-toggle="modal" data-bs-target="#clientDetailModal"><i class="fas fa-eye"></i></button>
                  <router-link :to="{ name: 'UpdateClient', params: { id: client.id } }" class="btn btn-warning me-2"><i class="fas fa-pencil"></i></router-link>
                  <button @click.prevent="deleteClient(client.id)" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                  </div>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <Detail ref="detail" />
  </div>
</template>

<script setup>
import axios from 'axios';
import { ref, onMounted } from 'vue';
import Detail from './Detail.vue';
import NavBar from '../Navbar/NavBar.vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const message = ref(route.query.message || '');

setTimeout(() => {
  message.value = '';
}, 5000);
const clients = ref([]);
const detail = ref(null);

const showDetail = async (id) => {
  if (detail.value) {
    await detail.value.showClientDetails(id);
  }
};

const getPersonType = (estPersonne) => {
  return estPersonne ? 'Professionnel' : 'Particulier';
};

const getStatusClass = (status) => {
  switch (status) {
    case 'ACTIF':
      return 'text-success';
    case 'PROSPECT':
      return 'text-warning';
    case 'RÉSOLU':
      return 'text-danger';
    default:
      return 'text-secondary';
  }
};

const getClients = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/clients');
    clients.value = response.data;
    console.log('API Response:', response.data);
  } catch (error) {
    console.error('Error fetching clients:', error);
  }
};

const deleteClient = async (id) => {
  await axios.delete(`http://localhost:8000/api/clients/${id}`);
  getClients();
};
const formatDate = (date) => {
  if (!date) return ''; // Handle cases where date might be null or undefined
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(date).toLocaleDateString('fr-FR', options);
};
onMounted(() => {
  getClients();
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

.text-success {
  color: #28a745; /* Green for active */
}

.text-warning {
  color: #ffc107; /* Yellow for prospect */
}

.text-danger {
  color: #dc3545; /* Red for resolved */
}
</style>

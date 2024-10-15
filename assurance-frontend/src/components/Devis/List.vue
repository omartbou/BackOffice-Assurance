<template>
  <NavBar/>

  <div class="container">
    <div class="row my-5">
      <div class="col-md-12">
        <h2 class="text-white mb-3">Liste des devis :</h2>
      </div>
      <div class="col-md-12">
        <div class="card h-100 bg-dark text-white">
          <div class="d-flex justify-content-end">
            <router-link class="btn btn-success" :to="{ name: 'AddDevis' }"><i class="fas fa-plus"></i></router-link>
          </div>
          <div class="card-body">

            <table class="table table-hover table-dark">
              <tbody>

              <tr v-for="d in devis" :key="d.id">
                <td>
                  <div v-if="d.voitures && d.voitures.length > 0">
                      <strong v-for="voiture in d.voitures" :key="voiture.id">
                        {{ voiture.numero_immatriculation || 'N/A' }}
                      </strong>
                  </div>
                  <div v-else>
                    N/A
                  </div>
                </td>                <td>{{ d.client?.nom || 'N/A' }} {{ d.client?.prenom || 'N/A' }}</td>
                <td>{{ formatDate(d.date_effet) }}</td>
                <td>{{ d.prix }}/ {{ d.frequence_prix }}</td>
                <td>
                  <div class="d-flex">
                    <button class="btn btn-info me-2" @click="showDetail(d.id)" data-bs-toggle="modal" data-bs-target="#devisDetailModal"><i class="fas fa-eye"></i></button>
                    <router-link :to="{ name: 'UpdateDevis', params: { id: d.id } }" class="btn btn-warning me-2"><i class="fas fa-pencil"></i></router-link>
                    <button class="btn btn-danger" @click.prevent="deleteDevis(d.id)"><i class="fas fa-trash"></i></button>
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
const devis = ref([]);
import Detail from './Detail.vue';
import NavBar from '../Navbar/NavBar.vue';

const detail = ref(null);

const showDetail = async (id) => {
  if (detail.value) {
    await detail.value.showDevisDetails(id); // Access the method of Detail component
  }
};

const getDevis = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/devis');
    devis.value = response.data;
    console.log('API Response:', response.data); // Log the entire response
    console.log('Fetched devis:', devis.value); // Log the fetched data
  } catch (error) {
    console.error('Error fetching devis:', error);
  }
};

const deleteDevis = async (id) => {
  await axios.delete(`http://localhost:8000/api/devis/delete/${id}`);
  getDevis();
};

const formatDate = (date) => {
  if (!date) return ''; // Handle cases where date might be null or undefined
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(date).toLocaleDateString('fr-FR', options);
};

onMounted(() => {
  getDevis();
});
</script>

<style scoped>
.container {
  /* Add any specific styles for the container */
}

.card {
  border-radius: 10px; /* Rounded corners for the card */
}

.table {
  border-collapse: separate;
  border-spacing: 0 15px; /* Space between table rows */
}

.table-dark {
  background-color: #343a40; /* Dark background for the table */
}

.text-white {
  color: #ffffff; /* White text color */
}

.btn-info {
  background-color: #17a2b8; /* Custom info button color */
}

.btn-warning {
  background-color: #ffc107; /* Custom warning button color */
  color: #212529; /* Dark text color */
}

.btn-danger {
  background-color: #dc3545; /* Custom danger button color */
}

.d-flex {
  display: flex;
  justify-content: space-between;
}
</style>

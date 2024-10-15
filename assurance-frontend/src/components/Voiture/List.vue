<template>
  <NavBar/>

  <div class="container my-5">
    <div class="row">
      <div class="col-md-12">
        <h2 class="text-white mb-4">Liste des voitures :</h2>
      </div>
      <div class="col-md-12">
        <div class="card h-100 bg-dark text-white">
          <div class="d-flex justify-content-end">

            <router-link :to="{ name: 'AddVoiture' }" class="btn btn-success mb-3"><i class="fas fa-plus"></i></router-link>
          </div>
          <div class="card-body">

            <table class="table table-hover table-dark">
              <thead>
              <tr>
                <th scope="col">Numéro Immatriculation</th>
                <th scope="col">Usage</th>
                <th scope="col">Date d'Achat</th>
                <th scope="col">Actions</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="voiture in voitures" :key="voiture.id">
                <td>{{ voiture.numero_immatriculation || 'N/A' }}</td>
                <td>{{ voiture.voiture_usage }}</td>
                <td>{{ formatDate(voiture.date_achat) }}</td>
                <td>
                  <div class="d-flex">
                    <button class="btn btn-info me-2" @click="showDetail(voiture.id)" data-bs-toggle="modal" data-bs-target="#voitureDetailModal"><i class="fas fa-eye"></i></button>
                    <router-link :to="{ name: 'UpdateVoiture', params: { id: voiture.id } }" class="btn btn-warning me-2"><i class="fas fa-pencil"></i></router-link>
                    <button class="btn btn-danger" @click.prevent="deleteVoiture(voiture.id)"><i class="fas fa-trash"></i></button>
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
const voitures = ref([]);
import Detail from './Detail.vue';
import NavBar from '../Navbar/NavBar.vue';

const detail = ref(null);

const showDetail = async (id) => {
  if (detail.value) {
    await detail.value.showVoitureDetails(id);
  }
};

const getVoiture = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/voitures');
    voitures.value = response.data;
  } catch (error) {
    console.error('Error fetching voitures:', error);
  }
};

const deleteVoiture = async (id) => {
  await axios.delete(`http://localhost:8000/api/voiture/delete/${id}`);
  getVoiture();
};

const formatDate = (date) => {
  if (!date) return '';
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(date).toLocaleDateString('fr-FR', options);
};

onMounted(() => {
  getVoiture();
});
</script>

<style scoped>
.container {
  max-width: 800px;
}

.bg-dark {
  background-color: #343a40;
}

.text-white {
  color: #ffffff;
}

.btn-success {
  background-color: #28a745;
}

.table-dark {
  background-color: #343a40;
}

.table {
  border-collapse: separate;
  border-spacing: 0 15px;
}

.table-hover tbody tr:hover {
  background-color: #495057;
}
</style>

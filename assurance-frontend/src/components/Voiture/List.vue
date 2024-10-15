<template>
  <div class="container">
    <div class="row my-5">
      <div class="col-md-6">
        <div class="col-md-12">
          <h2>Liste des voitures : </h2>

        </div>
        <div class="card h-100">
          <div class="card-body">
            <router-link class="btn btn-info" :to="{ name: 'AddVoiture' }" >Add</router-link>

            <table class="table">
              <tbody>
              <tr v-for="voiture in voitures" :key="voiture.id">
                <td>{{ voiture.numero_immatriculation || 'N/A' }} </td>
                <td>{{ voiture.voiture_usage }}</td>
                <td>{{ formatDate(voiture.date_achat) }}</td>

                <td>
                  <button class="btn btn-info" @click="showDetail(voiture.id)" data-bs-toggle="modal" data-bs-target="#voitureDetailModal">View</button>
                  <router-link :to="{ name: 'UpdateVoiture', params: { id: voiture.id } }" class="btn btn-warning">Edit</router-link>
                  <button @click.prevent="deleteVoiture(voiture.id)">Delete</button>
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
    console.log('API Response:', response.data);

    console.log('Fetched voitures:', voitures.value);
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

<template>
  <div class="container">
    <div class="row my-5">
      <div class="col-md-6">
        <div class="col-md-12">
          <h2>Liste des devis : </h2>

        </div>
        <div class="card h-100">
          <div class="card-body">
            <router-link class="btn btn-info" :to="{ name: 'AddDevis' }" >Add</router-link>

            <table class="table">
              <tbody>
              <tr v-for="d in devis" :key="d.id">
<!--                <td>{{ d.voitures.map(v => v.id).join(', ') }}</td>-->
                <td>{{ d.client?.nom || 'N/A' }} {{ d.client?.prenom || 'N/A' }}</td>
                <td>{{ formatDate(d.date_effet) }}</td>
                <td>{{ d.prix }}/ {{ d.frequence_prix }}</td>

                <td>
                  <button class="btn btn-info" @click="showDetail(d.id)" data-bs-toggle="modal" data-bs-target="#devisDetailModal">View</button>
                  <router-link :to="{ name: 'UpdateDevis', params: { id: d.id } }" class="btn btn-warning">Edit</router-link>
                  <button @click.prevent="deleteDevis(d.id)">Delete</button>
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

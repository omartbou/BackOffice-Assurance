<template>
  <div class="container">
    <div class="row my-5">
      <div class="col-md-6">
      <div class="col-md-12">
        <h2>Liste des clients : </h2>

      </div>
    <div class="card h-100">
      <div class="card-body">
        <router-link :to="{ name: 'AddClient'}" class="btn btn-warning">Add</router-link>

        <table class="table">
      <tbody>
      <tr v-for="client in clients" :key="client.id">
       <td> {{ client.nom }} {{client.prenom}} </td>
        <td>{{ formatDate(client.dateNaissance) }}</td>
        <td>{{ getPersonType(client.est_personne) }}</td>

        <td>
          <button class="btn btn-info" @click="showDetail(client.id)" data-bs-toggle="modal" data-bs-target="#clientDetailModal">View</button>
          <router-link :to="{ name: 'UpdateClient', params: { id: client.id } }" class="btn btn-warning">Edit</router-link>
          <button @click.prevent="deleteClient(client.id)">Delete</button>
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
  const clients = ref([]);
  import Detail from './Detail.vue';
  const detail = ref(null);

  const showDetail = async (id) => {
    if (detail.value) {
      await detail.value.showClientDetails(id); // Access the method of Detail component
    }
  };


  const getPersonType = (estPersonne) => {
    return estPersonne ? 'Professionnel' : 'Particulier';
  };

  const getClients = async () => {
    try {
      const response = await axios.get('http://localhost:8000/api/clients');
      clients.value = response.data;
      console.log('API Response:', response.data); // Log the entire response

      console.log('Fetched clients:', clients.value); // Log the fetched data
    } catch (error) {
      console.error('Error fetching clients:', error);
    }
    console.log('Client date_naissance:', clients.value.date_naissance);

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
console.log(getClients);
console.log(clients.value);
  onMounted(() => {
    getClients();
  });

</script>

<template>
  <div class="container my-5">
    <h1 class="text-center text-white mb-4">Assurance Courtage</h1>

    <div class="d-flex justify-content-center">
      <router-link :to="{ name: 'Client' }" class="btn btn-primary mx-2">Clients</router-link>
      <router-link :to="{ name: 'ListDevis' }" class="btn btn-primary mx-2">Devis</router-link>
      <router-link :to="{ name: 'ListVoiture' }" class="btn btn-primary mx-2">Voitures</router-link>
    </div>
  </div>
</template>

<script setup>
import { RouterLink } from 'vue-router'
</script>

<style scoped>
.container {
  max-width: 800px; /* Center the content in the middle */
}

h1 {
  color: #ffffff; /* White text for the heading */
}

.btn {
  background-color: #28a745; /* Green background for buttons */
  color: #ffffff; /* White text for buttons */
  border-radius: 5px; /* Slightly rounded corners */
}

.btn:hover {
  background-color: #218838; /* Darker green on hover */
}

.d-flex {
  justify-content: space-evenly; /* Space buttons evenly across the center */
}

body {
  background-color: #343a40; /* Dark background */
}

.text-white {
  color: #ffffff;
}
</style>

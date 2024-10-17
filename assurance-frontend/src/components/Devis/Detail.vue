<template>
  <div>
    <!-- Bootstrap Modal -->
    <div class="modal fade" id="devisDetailModal" tabindex="-1" aria-labelledby="devisDetailModalLabel" aria-hidden="true" @hidden.bs.modal="resetDevisDetails">
      <div class="modal-dialog">
        <div class="modal-content">

          <div class="modal-body">
<!--            <p><strong>Auto:</strong> {{ devis.nom }}</p>-->
            <p><strong>Client:</strong> {{ devis.client.nom }} {{ devis.client.prenom }}</p>
            <p><strong>Date d'effet:</strong> {{ formatDate(devis.date_effet) }}</p>
            <p><strong>Prix et Frequence de prix:</strong>
              {{ devis.prix }}/
              {{ devis.frequence_prix }}</p>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const devis = ref({
  client: {
    nom: '',
    prenom: '',
  },
  prix: '',
  date_effet: '',
  frequence_prix: '',
});

const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleDateString('fr-FR');
};



const fetchDevisDetails = async (id) => {
  const response = await axios.get(`http://localhost:8000/api/devis/${id}`);
  Object.assign(devis.value, response.data);
};

const showDevisDetails = async (id) => {
  await fetchDevisDetails(id);
  const modal = new bootstrap.Modal(document.getElementById('devisDetailModal'));
  modal.show();
};

const resetDevisDetails = () => {
  devis.value = {
    client: {
      nom: '',
      prenom: '',
    },
    prix: '',
    dateEffet: '',
    frequencePrix: '',
  };
};

defineExpose({
  showDevisDetails,
});
</script>

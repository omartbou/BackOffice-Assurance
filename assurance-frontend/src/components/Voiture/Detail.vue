
<template>
  <div>
    <!-- Bootstrap Modal -->
    <div class="modal fade" id="voitureDetailModal" tabindex="-1" aria-labelledby="voitureDetailModalLabel" aria-hidden="true" @hidden.bs.modal="resetVoitureDetails">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="voitureDetailModalLabel">Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p><strong>Emplacement:</strong> {{ voiture.emplacement }}</p>
            <p><strong>Client:</strong> {{ voiture.client.nom }} {{ voiture.client.prenom }}</p>
            <p><strong>Date d'achat:</strong> {{ formatDate(voiture.date_achat) }}</p>
            <p><strong>Usage :</strong>
              {{ voiture.voiture_usage }}</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const voiture = ref({
  client: {
    nom: '',
    prenom: '',
  },
  voiture_usage: '',
  date_achat: '',
  numero_immatriculation: '',
  emplacement: '',
});

const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleDateString('fr-FR');
};



const fetchVoitureDetails = async (id) => {
  const response = await axios.get(`http://localhost:8000/api/voiture/${id}`);
  Object.assign(voiture.value, response.data);
};

const showVoitureDetails = async (id) => {
  await fetchVoitureDetails(id);
  const modal = new bootstrap.Modal(document.getElementById('voitureDetailModal'));
  modal.show();
};

const resetVoitureDetails = () => {
  voiture.value = {
    client: {
      nom: '',
      prenom: '',
    },
    voiture_usage: '',
    dateAchat: '',
    numeroImmatriculation: '',
    emplacement: '',
  };
};

defineExpose({
  showVoitureDetails,
});
</script>

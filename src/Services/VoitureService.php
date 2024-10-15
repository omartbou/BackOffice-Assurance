<?php

namespace App\Services;

use App\Entity\Devis;
use App\Entity\Voiture;
use App\Repository\ClientRepository;
use App\Repository\DevisRepository;
use App\Repository\VoitureRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VoitureService
{
    private $devisRepository;
    private $clientRepository;
    private $voitureRepository;
    public function  __construct(DevisRepository $devisRepository,
                                 ClientRepository $clientRepository,
                                 VoitureRepository $voitureRepository )
    {
        $this->devisRepository = $devisRepository;
        $this->clientRepository = $clientRepository;
        $this->voitureRepository = $voitureRepository;

    }
    public function voitureList(){
        return $this->voitureRepository->findall();
    }
    public function getVoitureById($id){
        return $this->voitureRepository->find($id);
    }
    public function createVoiture(array $data): Voiture
    {
        $voiture = new Voiture();
        $voiture->setNumeroImmatriculation($data['numero_immatriculation']);
        $voiture->setVoitureUsage($data['voiture_usage']);
        $voiture->setEmplacement($data['emplacement']);
        $voiture->setDateAchat(new \DateTime($data['date_achat']));

        $clientId = $data['client_id'];
        $client = $this->clientRepository->find($clientId);
        if (!$client) {
            throw new NotFoundHttpException('Client not found');
        }
        $voiture->setClient($client);


        if (isset($data['devis'])) {
            foreach ($data['devis'] as $devisId) {
                $devis = $this->devisRepository->find($devisId);
                if ($devis) {
                    $voiture->add($devis);
                } else {
                    throw new NotFoundHttpException('Devis with ID ' . $devisId . ' not found');
                }
            }
        }

        $this->voitureRepository->saveVoiture($voiture);

        return $voiture;
    }
    public function updateVoiture(Voiture $voiture,array $data): Voiture{
        $voiture->setNumeroImmatriculation($data['numero_immatriculation']);
        $voiture->setVoitureUsage($data['voiture_usage']);
        $voiture->setDateAchat(new \DateTime($data['date_achat']));
        $clientId = $data['client_id'];
        $client = $this->clientRepository->find($clientId);
        if (!$client) {
            throw new NotFoundHttpException('Client not found');
        }
        $voiture->setClient($client);

        if (isset($data['devis'])) {
            foreach ($data['devis'] as $devisId) {
                $devis = $this->devisRepository->find($devisId);
                if ($devis) {
                    $voiture->add($devis);
                } else {
                    throw new NotFoundHttpException('Devis with ID ' . $devisId . ' not found');
                }
            }
        }
        $this->voitureRepository->saveVoiture($voiture);

        return $voiture;
    }

    public function deleteVoiture(Voiture $voiture): void
    {
        $this->voitureRepository->deleteVoiture($voiture);
    }
}
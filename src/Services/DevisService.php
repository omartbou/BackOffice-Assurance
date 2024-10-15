<?php

namespace App\Services;

use App\Entity\Devis;
use App\Repository\ClientRepository;
use App\Repository\DevisRepository;
use App\Repository\VoitureRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

class DevisService
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
    public function devisList(){
        return $this->devisRepository->findall();
    }

    public function getDevisById($id){
        return $this->devisRepository->find($id);
    }


    public function createDevis(array $data): Devis
    {
        $devis = new Devis();
        $devis->setNumero(Uuid::v4()->toString());
        $devis->setPrix($data['prix']);
        $devis->setFrequencePrix($data['frequence_prix']);
        $devis->setDateEffet(new \DateTime($data['date_effet']));

        $clientId = $data['client_id'];
        $client = $this->clientRepository->find($clientId);
        if (!$client) {
            throw new NotFoundHttpException('Client not found');
        }
        $devis->setClient($client);

        if (isset($data['voitures'])) {

            foreach ($data['voitures'] as $voitureId) {
                $voiture = $this->voitureRepository->find($voitureId);
                if ($voiture) {
                    $devis->addVoiture($voiture);
                } else {
                    throw new NotFoundHttpException('Voiture with ID ' . $voitureId . ' not found');
                }
            }
        }

        $this->devisRepository->saveDevis($devis);

        return $devis;
    }
    public function updateDevis(Devis $devis,array $data): Devis{
        if (isset($data['numero'])) $devis->setNumero($data['numero']);
        if (isset($data['prix'])) $devis->setPrix($data['prix']);
        if (isset($data['date_effet'])) $devis->setDateEffet(new \DateTime($data['date_effet']));
        if (isset($data['frequence_prix'])) $devis->setFrequencePrix($data['frequence_prix']);
        if (isset($data['client_id'])) {
            $client =$this->clientRepository->find($data['client_id']);
            if ($client) {
                $devis->setClient($client);
            } else {
                return $this->json(['error' => 'Client not found'], Response::HTTP_NOT_FOUND);
            }
        }
        if (isset($data['voitures'])) {
            $devis->getVoitures()->clear();
            foreach ($data['voitures'] as $voitureId) {
                $voiture = $this->voitureRepository->find($voitureId);
                if ($voiture) {
                    $devis->addVoiture($voiture);
                } else {
                    return $this->json(['error' => 'Voiture with ID ' . $voitureId . ' not found'],
                        Response::HTTP_NOT_FOUND);
                }
            }
        }
        $this->devisRepository->saveDevis($devis);

        return $devis;
    }

    public function deleteDevis(Devis $devis): void
    {
        $this->devisRepository->deleteDevis($devis);
    }
}
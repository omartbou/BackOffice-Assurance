<?php

namespace App\Services;

use App\Entity\Client;
use App\Repository\ClientRepository;

class ClientService
{
    private $clientRepository;
    public function  __construct(ClientRepository $clientRepository){
             $this->clientRepository =$clientRepository;

    }
    public function clientList(){
        return $this->clientRepository->findAllClients();
    }
    public function getClientById($id){
        return $this->clientRepository->findClientById($id);
    }
    public function createClient(array $data): Client
    {
        $client =new Client();
        $client->setNom($data['nom']);
        $client->setPrenom($data['prenom']);
        $client->setDateNaissance(new \DateTime($data['date_naissance']));
        $client->setEstPersonne($data['est_personne']);
        $this->clientRepository->saveClient($client);
        return $client;
    }
    public function updateClient(Client $client,array $data): Client{
        if (isset($data['nom'])) $client->setNom($data['nom']);
        if (isset($data['prenom'])) $client->setPrenom($data['prenom']);
        if (isset($data['date_naissance'])) $client->setDateNaissance(new \DateTime($data['date_naissance']));
        if (isset($data['est_personne'])) $client->setEstPersonne($data['est_personne']);

        $this->clientRepository->saveClient($client);

        return $client;
    }
    public function deleteClient(Client $client): void
    {
        $this->clientRepository->deleteClient($client);
    }
}
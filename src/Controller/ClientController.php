<?php

namespace App\Controller;

use App\Services\ClientService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/api')]

class ClientController extends AbstractController
{
    private $clientService;
    public function __construct(ClientService $clientService){
        $this->clientService = $clientService;
    }
    //Afficher les clients
    #[Route('/clients', name: 'app_client')]
    public function getClients(): JsonResponse
    {
        $clients = $this->clientService->clientList();
        return $this->json($clients, Response::HTTP_CREATED, [], ['groups' => ['client:read']]);
    }

    //Afficher les details du client
    #[Route('/client/{id}', name: 'get_client', methods: ['GET'])]
    public function getClient($id): JsonResponse
    {
        $client = $this->clientService->getClientById($id);
        if (!$client) {
            return $this->json(['error' => 'Client not found'], 404);
        }
        return $this->json($client, Response::HTTP_CREATED, [], ['groups' => ['client:read']]);
    }

    //Sauvegarder le client
    #[Route('/clients/post', name: 'create_client', methods: ['POST'])]
    public function createClient(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'Invalid JSON'], 400);
        }

        $client = $this->clientService->createClient($data);

        return $this->json($client, Response::HTTP_CREATED, [], ['groups' => ['client:read']]);
    }
    //Modifier le client
    #[Route('/clients/edit/{id}', name: 'update_client', methods: ['PUT'])]
    public function updateClient($id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $client = $this->clientService->getClientById($id);

        if (!$client) {
            return $this->json(['error' => 'Client not found'], 404);
        }

        $updatedClient = $this->clientService->updateClient($client, $data);
        return $this->json($updatedClient, Response::HTTP_CREATED, [], ['groups' => ['client:read']]);
    }

    //Supprimer le client
    #[Route('/clients/{id}', name: 'delete_client', methods: ['DELETE'])]
    public function deleteClient($id): JsonResponse
    {
        $client = $this->clientService->getClientById($id);
        if (!$client) {
            return $this->json(['error' => 'Client not found'], 404);
        }

        $this->clientService->deleteClient($client);
        return $this->json(['message' => 'Client deleted successfully']);
    }

}

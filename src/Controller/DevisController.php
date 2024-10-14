<?php

namespace App\Controller;

use App\Services\DevisService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]

class DevisController extends AbstractController
{
    private $devisService;
    public function __construct(DevisService $devisService){
        $this->devisService = $devisService;
    }
    //Afficher les devis
    #[Route('/devis', name: 'app_devis')]
    public function index(): Response
    {
        $devis = $this->devisService->devisList();
        return $this->json($devis, Response::HTTP_CREATED, [], ['groups' => ['devis:read']]);
    }
    //Afficher un devis
    #[Route('/devis/{id}', name: 'get_devis', methods: ['GET'])]
    public function getDevis($id,SerializerInterface $serializer): JsonResponse
    {
        $devis = $this->devisService->getDevisById($id);
        if (!$devis) {
            return $this->json(['error' => 'Devis not found'], 404);
        }
        $jsonDevis = $serializer->serialize($devis, 'json', ['groups' => ['read']]);
        return $this->json($devis, 200, [], ['groups' => 'devis:read']);
    }
    //Ajouter un devis
    #[Route('/devis/post', name: 'create_devis', methods: ['POST'])]
    public function createDevis(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON'], 400);
        }

        $devis = $this->devisService->createDevis($data);

        return $this->json($devis, Response::HTTP_CREATED, [], ['groups' => ['devis:read']]);
    }

    //Modifier le devis
    #[Route('/devis/edit/{id}', name: 'update_devis', methods: ['PUT'])]
    public function updateDevis($id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $devis = $this->devisService->getDevisById($id);

        if (!$devis) {
            return $this->json(['error' => 'Devis not found'], 404);
        }

        $updatedDevis = $this->devisService->updateDevis($devis, $data);
        return $this->json($updatedDevis, Response::HTTP_CREATED, [], ['groups' => ['devis:read']]);
    }
    //Supprimer le devis
    #[Route('/devis/delete/{id}', name: 'delete_devis', methods: ['DELETE'])]
    public function deleteDevis($id): JsonResponse
    {
        $devis = $this->devisService->getDevisById($id);
        if (!$devis) {
            return $this->json(['error' => 'Devis not found'], 404);
        }

        $this->devisService->deleteDevis($devis);
        return $this->json(['message' => 'Devis deleted successfully']);
    }
}

<?php

namespace App\Controller;

use App\Services\VoitureService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]

class VoitureController extends AbstractController
{
    private $voitureService;
    public function __construct(VoitureService $voitureService){
        $this->voitureService = $voitureService;
    }
    //Afficher les voitures
    #[Route('/voitures', name: 'app_voitures')]
    public function index(): Response
    {
        $voiture = $this->voitureService->voitureList();
        return $this->json($voiture, Response::HTTP_CREATED, [], ['groups' => ['voiture:read']]);
    }
    //Afficher une voiture
    #[Route('/voiture/{id}', name: 'get_voiture', methods: ['GET'])]
    public function getVoiture($id,SerializerInterface $serializer): JsonResponse
    {
        $voiture = $this->voitureService->getVoitureById($id);
        if (!$voiture) {
            return $this->json(['error' => 'Voiture not found'], 404);
        }
        $jsonVoiture = $serializer->serialize($voiture, 'json', ['groups' => ['read']]);
        return $this->json($voiture, 200, [], ['groups' => 'voiture:read']);
    }
    //Ajouter une voiture
    #[Route('/voiture/post', name: 'create_voiture', methods: ['POST'])]
    public function createVoiture(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON'], 400);
        }

        $voiture = $this->voitureService->createVoiture($data);

        return $this->json($voiture, Response::HTTP_CREATED, [], ['groups' => ['voiture:read']]);
    }

    //Modifier la voiture
    #[Route('/voiture/edit/{id}', name: 'update_voiture', methods: ['PUT'])]
    public function updateVoiture($id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $voiture = $this->voitureService->getVoitureById($id);

        if (!$voiture) {
            return $this->json(['error' => 'Voiture not found'], 404);
        }

        $updatedVoiture= $this->voitureService->updateVoiture($voiture, $data);
        return $this->json($updatedVoiture, Response::HTTP_CREATED, [], ['groups' => ['devis:read']]);
    }
    //Supprimer la voiture
    #[Route('/voiture/delete/{id}', name: 'delete_voiture', methods: ['DELETE'])]
    public function deleteVoiture($id): JsonResponse
    {
        $voiture = $this->voitureService->getVoitureById($id);
        if (!$voiture) {
            return $this->json(['error' => 'Voiture not found'], 404);
        }

        $this->voitureService->deleteVoiture($voiture);
        return $this->json(['message' => 'Voiture deleted successfully']);
    }
}

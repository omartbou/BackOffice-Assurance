<?php

namespace App\Controller;

use App\Services\VoitureService;
use App\Validator\VoitureValidatorRequest;
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
    private $voitureValidator;
    public function __construct(VoitureService $voitureService,VoitureValidatorRequest $voitureValidator){
        $this->voitureService = $voitureService;
        $this->voitureValidator = $voitureValidator; // Initialize the validator

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
        $validationResult = $this->voitureValidator->validate($data);
        if ($validationResult instanceof JsonResponse) {
            return $validationResult; // Return validation errors
        }
        $voiture = $this->voitureService->createVoiture( $validationResult);

        return $this->json($voiture, Response::HTTP_CREATED, [], ['groups' => ['voiture:read']]);
    }

    //Modifier la voiture
    #[Route('/voiture/edit/{id}', name: 'update_voiture', methods: ['PUT'])]
    public function updateVoiture($id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $voiture = $this->voitureService->getVoitureById($id);
        $validationResult = $this->voitureValidator->validate($data);
        if ($validationResult instanceof JsonResponse) {
            return $validationResult; // Return validation errors
        }
        if (!$voiture) {
            return $this->json(['error' => 'Voiture not found'], 404);
        }

        $updatedVoiture= $this->voitureService->updateVoiture($voiture, $validationResult);
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

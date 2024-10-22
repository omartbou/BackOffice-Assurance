<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class VoitureValidatorRequest
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Validate voiture request data.
     *
     * @param array $data
     * @return array|JsonResponse
     */
    public function validate(array $data)
    {
        // Define the validation constraints
        $constraints = new Assert\Collection([
            'numero_immatriculation' => [
                new Assert\NotBlank(['message' => 'Le numéro d\'immatriculation ne doit pas être vide']),
            ],
            'date_achat' => [
                new Assert\NotBlank(['message' => 'La date d\'achat ne doit pas être vide']),
                new Assert\Date(['message' => 'La date d\'achat doit être une date valide']),
            ],
            'voiture_usage' => [
                new Assert\NotBlank(['message' => 'L\'usage ne doit pas être vide']),
                new Assert\Length([
                    'min' => 3,
                    'max' => 255,
                    'minMessage' => 'L\'usage doit contenir au moins {{ limit }} caractères',
                    'maxMessage' => 'L\'usage ne peut pas dépasser {{ limit }} caractères',
                ]),
            ],
            'emplacement' => [
                new Assert\NotBlank(['message' => 'L\'emplacement ne doit pas être vide']),
                new Assert\Length([
                    'max' => 255,
                    'maxMessage' => 'L\'emplacement ne peut pas dépasser {{ limit }} caractères',
                ]),
            ],
            'client_id' => [
                new Assert\NotBlank(['message' => 'Le client ne doit pas être vide']),
                new Assert\Type(['type' => 'integer', 'message' => 'L\'ID du client doit être un entier.']),
            ],
            // Add other fields as necessary
        ]);

        // Validate the data
        $violations = $this->validator->validate($data, $constraints);

        // If there are validation errors, return a JSON response with error messages
        if (count($violations) > 0) {
            $errorMessages = [];
            foreach ($violations as $violation) {
                $errorMessages[] = $violation->getMessage();
            }

            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Return validated data
        return $data;
    }
}

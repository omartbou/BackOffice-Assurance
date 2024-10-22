<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class DevisValidatorRequest
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Validate devis request data.
     *
     * @param array $data
     * @return array|JsonResponse
     */
    public function validate(array $data)
    {
        // Define the validation constraints
        $constraints = new Assert\Collection([
            'client_id'=>[
                new Assert\NotBlank(['message' => 'Le client ne doit pas être vide']),

            ],
            'prix' => [
                new Assert\NotBlank(['message' => 'Le prix ne doit pas être vide']),
            ],
            'frequence_prix' => [
                new Assert\NotBlank(['message' => 'Le frequence prix ne doit pas être vide']),
            ],
            'date_effet' => [
                new Assert\NotBlank(['message' => 'La date ne doit pas être vide']),
                new Assert\Date(['message' => 'La date doit être une date valide']),
            ],
            'voitures' => [
                new Assert\Optional(
                    new Assert\All([
                        new Assert\Positive(['message' => 'Chaque ID de voiture doit être un nombre positif.']),
                    ])
                ),
            ],
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

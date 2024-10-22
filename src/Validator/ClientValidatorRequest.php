<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientValidatorRequest
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;

    }

    /**
     * Validate client request data.
     *
     * @param array $data
     * @return array|JsonResponse
     */
    public function validate(array $data)
    {
        // Define the validation constraints
        $constraints = new Assert\Collection([
            'nom' => [
                new Assert\NotBlank(['message' => 'Le nom ne doit pas être vide']),
                new Assert\Length(['max' => 255, 'maxMessage' => 'Le nom ne doit pas dépasser 255 caractères']),
            ],
            'prenom' => [
                new Assert\NotBlank(['message' => 'Le prénom ne doit pas être vide']),
                new Assert\Length(['max' => 255, 'maxMessage' => 'Le prénom ne doit pas dépasser 255 caractères']),
            ],
            'date_naissance' => [
                new Assert\NotBlank(['message' => 'La date de naissance ne doit pas être vide']),
                new Assert\Date(['message' => 'La date de naissance doit être une date valide']),
            ],
            'est_personne' => [
                new Assert\NotNull(['message' => 'Le champ "estPersonne" ne doit pas être nul']),
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

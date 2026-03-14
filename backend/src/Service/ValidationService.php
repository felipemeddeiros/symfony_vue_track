<?php

namespace App\Service;

use App\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationService
{
    public function __construct(
        private ValidatorInterface $validator
    ) {
    }

    /**
     * Validate an entity and throw exception if invalid
     * @throws ValidationException
     */
    public function validate(object $entity): void
    {
        $violations = $this->validator->validate($entity);
        
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $propertyPath = $violation->getPropertyPath();
                $errors[$propertyPath][] = $violation->getMessage();
            }
            throw new ValidationException($errors);
        }
    }
}

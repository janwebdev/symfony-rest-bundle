<?php
declare(strict_types=1);

namespace Janwebdev\RestBundle\Validation;


use Janwebdev\RestBundle\Exception\DTO\ValidationError;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationExceptionFactory
{
    public function buildFromViolationsList(ConstraintViolationListInterface $violationList): ValidationException
    {
        return new ValidationException(array_map(
            fn(ConstraintViolationInterface $violation) => new ValidationError(
                $violation->getPropertyPath(),
                $violation->getMessageTemplate()??$violation->getMessage(),
                $violation->getCode(),
                array_combine(
                    array_map(fn(string $key) => trim($key, '{} '), array_keys($violation->getParameters())),
                    array_values($violation->getParameters())
                )
            ),
            iterator_to_array($violationList)
        ));
    }
}

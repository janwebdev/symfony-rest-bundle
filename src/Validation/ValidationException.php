<?php
declare(strict_types=1);

namespace Janwebdev\RestBundle\Validation;


use Janwebdev\RestBundle\Exception\DTO\ValidationError;
use RuntimeException;

class ValidationException extends RuntimeException
{
    /**
     * @var array|ValidationError[]
     */
    private array $validationErrors;

    /**
     * ValidationException constructor.
     * @param array|ValidationError[] $validationErrors
     */
    public function __construct(array $validationErrors)
    {
        parent::__construct('Validation error');
        $this->validationErrors = $validationErrors;
    }

    /**
     * @return array
     */
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }
}

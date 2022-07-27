<?php
declare(strict_types=1);

namespace Janwebdev\RestBundle\Exception\DTO;

use Janwebdev\RestBundle\Validation\ValidationException;
use Throwable;

interface ExceptionDTOFactoryInterface
{
    public function buildExceptionDTO(Throwable $exception): ExceptionDTO;

    public function buildExceptionDTOFromValidationException(ValidationException $exception): ExceptionDTO;
}

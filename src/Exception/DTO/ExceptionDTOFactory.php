<?php
declare(strict_types=1);

namespace Janwebdev\RestBundle\Exception\DTO;


use Janwebdev\RestBundle\Validation\ValidationException;
use Throwable;

class ExceptionDTOFactory implements ExceptionDTOFactoryInterface
{
    public function buildExceptionDTO(Throwable $exception): ExceptionDTO
    {
        $exceptionDTO = new ExceptionDTO();
        $exceptionDTO->errors[] = new Error($exception->getMessage());
        return $exceptionDTO;
    }

    public function buildExceptionDTOFromTranslatableException(TranslatableException $exception): ExceptionDTO
    {
        $exceptionDTO = new ExceptionDTO();
        $exceptionDTO->errors[] = new Error($exception->getMessage(), $exception->getGroup(), $exception->getKey(), $exception->getValues());
        return $exceptionDTO;
    }

    public function buildExceptionDTOFromValidationException(ValidationException $exception): ExceptionDTO
    {
        $dto = new ExceptionDTO();
        $errors = $exception->getValidationErrors();
        /** @var ValidationError $error */
        foreach($errors as $error) {
            if(str_contains($error->message, '|')) {
                $error->message = explode('|', $error->message)[1];
            }
        }
        $dto->errors = $errors;
        return $dto;
    }
}

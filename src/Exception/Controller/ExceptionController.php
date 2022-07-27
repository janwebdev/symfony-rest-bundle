<?php
declare(strict_types=1);

namespace Janwebdev\RestBundle\Exception\Controller;


use Janwebdev\RestBundle\Exception\DTO\ExceptionDTOFactoryInterface;
use Janwebdev\RestBundle\Validation\ValidationException;
use FOS\RestBundle\View\View;
use InvalidArgumentException;
use Throwable;

class ExceptionController
{
    private ExceptionDTOFactoryInterface $exceptionDTOFactory;

    /**
     * ExceptionController constructor.
     * @param ExceptionDTOFactoryInterface $exceptionDTOFactory
     */
    public function __construct(ExceptionDTOFactoryInterface $exceptionDTOFactory)
    {
        $this->exceptionDTOFactory = $exceptionDTOFactory;
    }

    public function showException($exception): View
    {
        if (!$exception instanceof Throwable) {
            throw new InvalidArgumentException('Provided argument is not throwable: ' . get_class($exception));
        }
        if ($exception instanceof ValidationException) {
            return new View($this->exceptionDTOFactory->buildExceptionDTOFromValidationException($exception), 400);
        }
        if ($exception instanceof TranslatableException) {
            return new View($this->exceptionDTOFactory->buildExceptionDTOFromTranslatableException($exception));
        }
        return new View($this->exceptionDTOFactory->buildExceptionDTO($exception));
    }
}

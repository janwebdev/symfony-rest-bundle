<?php

declare(strict_types=1);

namespace Janwebdev\RestBundle\EventListener;

use Doctrine\DBAL\Exception\DriverException;
use Throwable;
use Janwebdev\RestBundle\Exception\DTO\ExceptionDTOFactoryInterface;
use Janwebdev\RestBundle\Validation\ValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Response;

class ExceptionListener  implements EventSubscriberInterface
{

    private ExceptionDTOFactoryInterface $exceptionDTOFactory;
    private string $appEnv;

    public function __construct(ExceptionDTOFactoryInterface $exceptionDTOFactory, string $appEnv)
    {
        $this->exceptionDTOFactory = $exceptionDTOFactory;
        $this->appEnv = $appEnv;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof Throwable) {
            throw new InvalidArgumentException('Provided argument is not throwable: ' . get_class($exception));
        }

        if ($exception instanceof DriverException && $this->appEnv === 'prod') {
            throw new \LogicException('Bad request');
        }

	    $response = $this->getResponse($exception);

	    $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }

    private function getResponse(Throwable $exception): Response
    {
        if ($exception instanceof ValidationException) {
            $response = new JsonResponse($this->exceptionDTOFactory->buildExceptionDTOFromValidationException($exception));
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        } else {
            $response = new JsonResponse($this->exceptionDTOFactory->buildExceptionDTO($exception));
        }
        return $response;
    }
}

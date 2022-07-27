<?php
declare(strict_types=1);

namespace Janwebdev\RestBundle\EventListener;


use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use FOS\RestBundle\FOSRestBundle;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ViewListener implements EventSubscriberInterface
{
    private ViewHandlerInterface $viewHandler;

    /**
     * Constructor.
     *
     * @param ViewHandlerInterface $viewHandler
     */
    public function __construct(ViewHandlerInterface $viewHandler)
    {
        $this->viewHandler = $viewHandler;
    }

    /**
     * Guesses the template name to render and its variables and adds them to
     * the request object.
     * @param ControllerEvent $event
     */
    public function onKernelController(ControllerEvent $event): void
    {
        $request = $event->getRequest();
        $view = $request->attributes->get('_template');

        if (!$view instanceof ViewAnnotation) {
            return;
        }

        $controller = $event->getController();
        if (!is_array($controller) && method_exists($controller, '__invoke')) {
            $controller = [$controller, '__invoke'];
        }
        $view->setOwner($controller);
    }

    /**
     * @param ViewEvent $event
     * @noinspection PhpUnused
     */
    public function onKernelView(ViewEvent $event): void
    {
        $request = $event->getRequest();

        if (!$request->attributes->get(FOSRestBundle::ZONE_ATTRIBUTE, true)) {
            return;
        }

        $configuration = $request->attributes->get('_template');

        $view = $event->getControllerResult();
        if (!$view instanceof View) {
            $view = new View($view);
        }

        if ($configuration instanceof ViewAnnotation) {

            if (null !== $configuration->getStatusCode() && (null === $view->getStatusCode() || Response::HTTP_OK === $view->getStatusCode())) {
                $view->setStatusCode($configuration->getStatusCode());
            }

            $context = $view->getContext();
            if ($configuration->getSerializerGroups()) {
                if (null === $context->getGroups()) {
                    $context->setGroups($configuration->getSerializerGroups());
                } else {
                    $context->setGroups(array_merge($context->getGroups(), $configuration->getSerializerGroups()));
                }
            }
            if (true === $configuration->getSerializerEnableMaxDepthChecks()) {
                $context->enableMaxDepth();
            } elseif (false === $configuration->getSerializerEnableMaxDepthChecks()) {
                $context->disableMaxDepth();
            }

        }

        if (null === $view->getFormat()) {
            $view->setFormat($request->getRequestFormat());
        }

        $response = $this->viewHandler->handle($view, $request);

        $event->setResponse($response);
    }


    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController', -128],
            KernelEvents::VIEW => ['onKernelView', 30],
        ];
    }
}

<?php
declare(strict_types=1);

namespace Janwebdev\RestBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use LogicException;
use ReflectionClass;
use ReflectionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use UnexpectedValueException;
use function array_key_exists;
use function get_class;
use function is_array;

class ControllerListener implements EventSubscriberInterface
{
    /**
     * @var Reader
     */
    private Reader $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Modifies the Request object to apply configuration information found in
     * controllers annotations like the template to render or HTTP caching
     * configuration.
     * @param ControllerEvent $event
     * @throws ReflectionException
     */
    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();

        if (!is_array($controller) && method_exists($controller, '__invoke')) {
            $controller = [$controller, '__invoke'];
        }

        if (!is_array($controller)) {
            return;
        }

        $className = get_class($controller[0]);
        $object = new ReflectionClass($className);
        $method = $object->getMethod($controller[1]);

        $classConfigurations = $this->getConfigurations($this->reader->getClassAnnotations($object));
        $methodConfigurations = $this->getConfigurations($this->reader->getMethodAnnotations($method));

        $configurations = [];
        foreach (array_merge(array_keys($classConfigurations), array_keys($methodConfigurations)) as $key) {
            if (!array_key_exists($key, $classConfigurations)) {
                $configurations[$key] = $methodConfigurations[$key];
            } elseif (!array_key_exists($key, $methodConfigurations)) {
                $configurations[$key] = $classConfigurations[$key];
            } else if (is_array($classConfigurations[$key])) {
                if (!is_array($methodConfigurations[$key])) {
                    throw new UnexpectedValueException('Configurations should both be an array or both not be an array');
                }
                $configurations[$key] = array_merge($classConfigurations[$key], $methodConfigurations[$key]);
            } else {
                // method configuration overrides class configuration
                $configurations[$key] = $methodConfigurations[$key];
            }
        }

        $request = $event->getRequest();
        foreach ($configurations as $key => $attributes) {
            $request->attributes->set($key, $attributes);
        }
    }

    private function getConfigurations(array $annotations): array
    {
        $configurations = [];
        foreach ($annotations as $configuration) {
            if ($configuration instanceof ConfigurationInterface) {
                $index = '_' . $configuration->getAliasName();
                if ($configuration->allowArray()) {
                    $configurations[$index][] = $configuration;
                } elseif (!isset($configurations[$index])) {
                    $configurations[$index] = $configuration;
                } else {
                    throw new LogicException(sprintf('Multiple "%s" annotations are not allowed.', $configuration->getAliasName()));
                }
            }
        }

        return $configurations;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => [
                ['onKernelController', 10],
            ],
        ];
    }
}

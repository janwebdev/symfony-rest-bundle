<?php
declare(strict_types=1);

namespace Janwebdev\RestBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader as ConfigYamlFileLoader;

class RestExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new ConfigYamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');

    }

    /**
     * @param ContainerBuilder $container
     * @throws Exception
     */
    public function prepend(ContainerBuilder $container)
    {
        $loader = new ConfigYamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('prepend.yaml');
    }

}

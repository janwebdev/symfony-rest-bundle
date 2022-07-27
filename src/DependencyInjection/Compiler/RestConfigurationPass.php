<?php
declare(strict_types=1);

namespace Janwebdev\RestBundle\DependencyInjection\Compiler;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader as ConfigYamlFileLoader;

class RestConfigurationPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $loader = new ConfigYamlFileLoader($container, new FileLocator(__DIR__ . '/../../../config'));
        $loader->load('override.yaml');
    }
}

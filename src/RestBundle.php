<?php

declare(strict_types=1);

namespace Janwebdev\RestBundle;

use Janwebdev\RestBundle\DependencyInjection\Compiler\RestConfigurationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RestBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RestConfigurationPass());
    }
}

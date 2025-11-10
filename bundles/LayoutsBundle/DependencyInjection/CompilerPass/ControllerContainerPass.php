<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ControllerContainerPass implements CompilerPassInterface
{
    private const string SERVICE_NAME = 'netgen_layouts.controller.base';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $container->findDefinition(self::SERVICE_NAME)
            ->removeMethodCall('setContainer')
            ->addMethodCall('setContainer', [new Reference(ContainerInterface::class)]);
    }
}

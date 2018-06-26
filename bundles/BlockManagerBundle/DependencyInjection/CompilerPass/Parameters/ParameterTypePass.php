<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ParameterTypePass implements CompilerPassInterface
{
    private const SERVICE_NAME = 'netgen_block_manager.parameters.registry.parameter_type';
    private const TAG_NAME = 'netgen_block_manager.parameters.parameter_type';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $registry = $container->findDefinition(self::SERVICE_NAME);
        $parameterTypeServices = array_keys($container->findTaggedServiceIds(self::TAG_NAME));

        foreach ($parameterTypeServices as $parameterTypeService) {
            $registry->addMethodCall(
                'addParameterType',
                [new Reference($parameterTypeService)]
            );
        }
    }
}

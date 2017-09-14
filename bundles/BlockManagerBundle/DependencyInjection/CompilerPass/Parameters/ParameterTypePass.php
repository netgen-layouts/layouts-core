<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ParameterTypePass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.parameters.registry.parameter_type';
    const TAG_NAME = 'netgen_block_manager.parameters.parameter_type';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $registry = $container->findDefinition(self::SERVICE_NAME);
        $parameterTypeServices = array_keys($container->findTaggedServiceIds(self::TAG_NAME));

        foreach ($parameterTypeServices as $parameterTypeService) {
            $registry->addMethodCall(
                'addParameterType',
                array(new Reference($parameterTypeService))
            );
        }
    }
}

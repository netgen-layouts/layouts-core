<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ParameterTypePass implements CompilerPassInterface
{
    private static $serviceName = 'netgen_block_manager.parameters.registry.parameter_type';
    private static $tagName = 'netgen_block_manager.parameters.parameter_type';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::$serviceName)) {
            return;
        }

        $registry = $container->findDefinition(self::$serviceName);
        $parameterTypeServices = array_keys($container->findTaggedServiceIds(self::$tagName));

        foreach ($parameterTypeServices as $parameterTypeService) {
            $registry->addMethodCall(
                'addParameterType',
                array(new Reference($parameterTypeService))
            );
        }
    }
}

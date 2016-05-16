<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use RuntimeException;

class ResultValueBuilderPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.collection.result_value_builder';
    const TAG_NAME = 'netgen_block_manager.collection.value_converter';

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            if (!$container->has(self::SERVICE_NAME)) {
                throw new RuntimeException("Service '{self::SERVICE_NAME}' is missing.");
            }
        }

        $resultValueBuilder = $container->findDefinition(self::SERVICE_NAME);
        $valueConverterServices = array_keys($container->findTaggedServiceIds(self::TAG_NAME));

        $valueConverters = array();
        foreach ($valueConverterServices as $serviceName) {
            $valueConverters[] = new Reference($serviceName);
        }

        $resultValueBuilder->replaceArgument(1, $valueConverters);
    }
}

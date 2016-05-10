<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ResultValueBuilderPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.collection.result_value_builder';

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $resultValueBuilder = $container->findDefinition(self::SERVICE_NAME);

        $valueConverterServices = $container->findTaggedServiceIds('netgen_block_manager.collection.value_converter');
        $valueConverters = array();

        foreach ($valueConverterServices as $serviceName => $tag) {
            $valueConverters[] = new Reference($serviceName);
        }

        $resultValueBuilder->replaceArgument(1, $valueConverters);
    }
}

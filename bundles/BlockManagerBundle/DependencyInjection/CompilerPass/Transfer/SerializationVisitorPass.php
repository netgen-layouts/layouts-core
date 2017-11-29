<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Transfer;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass registering serialization visitors with aggregate visitor.
 */
final class SerializationVisitorPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.transfer.serializer.visitor.aggregate';
    const TAG_NAME = 'netgen_block_manager.transfer.serializer.visitor';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $aggregateVisitorDefinition = $container->findDefinition(self::SERVICE_NAME);
        $visitors = array_keys($container->findTaggedServiceIds(self::TAG_NAME));

        $visitorServices = array();
        foreach ($visitors as $visitor) {
            $visitorServices[] = new Reference($visitor);
        }

        $aggregateVisitorDefinition->replaceArgument(0, $visitorServices);
    }
}

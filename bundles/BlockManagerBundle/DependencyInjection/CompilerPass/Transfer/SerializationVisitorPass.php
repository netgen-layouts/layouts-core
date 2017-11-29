<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Transfer;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass registering serialization visitors with aggregate visitor.
 */
final class SerializationVisitorPass implements CompilerPassInterface
{
    /**
     * Tag used for serialization visitors.
     *
     * @var string
     */
    private static $visitorTag = 'netgen_block_manager.transfer.serializer.visitor';

    /**
     * Aggregate visitor service identifier.
     *
     * @var string
     */
    private static $aggregateVisitorId = 'netgen_block_manager.transfer.serializer.visitor.aggregate';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(static::$aggregateVisitorId)) {
            return;
        }

        $aggregateVisitorDefinition = $container->getDefinition(static::$aggregateVisitorId);
        $visitors = $container->findTaggedServiceIds(static::$visitorTag);

        $visitorServices = array();
        foreach (array_keys($visitors) as $serviceId) {
            $visitorServices[] = new Reference($serviceId);
        }

        $aggregateVisitorDefinition->replaceArgument(0, $visitorServices);
    }
}

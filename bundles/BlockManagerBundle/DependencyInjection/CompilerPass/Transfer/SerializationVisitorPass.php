<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Transfer;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass registering serialization visitors with aggregate visitor.
 */
class SerializationVisitorPass implements CompilerPassInterface
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
        if (!$container->hasDefinition(static::$aggregateVisitorId)) {
            return;
        }

        $aggregateVisitorDefinition = $container->getDefinition(static::$aggregateVisitorId);
        $visitors = $container->findTaggedServiceIds(static::$visitorTag);

        $this->addVisitors($aggregateVisitorDefinition, array_keys($visitors));
    }

    /**
     * Register addVisitor() method call on $definition with the given $serviceIds.
     *
     *
     * @param \Symfony\Component\DependencyInjection\Definition $definition
     * @param array $serviceIds
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    protected function addVisitors(Definition $definition, array $serviceIds)
    {
        foreach ($serviceIds as $serviceId) {
            $definition->addMethodCall('addVisitor', array(new Reference($serviceId)));
        }
    }
}

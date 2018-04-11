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
    private static $serviceName = 'netgen_block_manager.transfer.serializer.visitor.aggregate';
    private static $tagName = 'netgen_block_manager.transfer.serializer.visitor';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::$serviceName)) {
            return;
        }

        $aggregateVisitorDefinition = $container->findDefinition(self::$serviceName);
        $visitors = array_keys($container->findTaggedServiceIds(self::$tagName));

        $visitorServices = array();
        foreach ($visitors as $visitor) {
            $visitorServices[] = new Reference($visitor);
        }

        $aggregateVisitorDefinition->replaceArgument(0, $visitorServices);
    }
}

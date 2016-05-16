<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use RuntimeException;

class ConditionMatcherRegistryPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.layout_resolver.condition_matcher.registry';
    const TAG_NAME = 'netgen_block_manager.layout_resolver.condition_matcher';

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

        $conditionMatcherRegistry = $container->findDefinition(self::SERVICE_NAME);
        $conditionMatchers = array_keys($container->findTaggedServiceIds(self::TAG_NAME));

        foreach ($conditionMatchers as $conditionMatcher) {
            $conditionMatcherRegistry->addMethodCall(
                'addConditionMatcher',
                array(new Reference($conditionMatcher))
            );
        }
    }
}

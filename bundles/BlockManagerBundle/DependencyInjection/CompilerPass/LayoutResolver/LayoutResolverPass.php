<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use RuntimeException;

class LayoutResolverPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.layout.resolver';
    const CONDITION_MATCHER_TAG_NAME = 'netgen_block_manager.layout.resolver.condition_matcher';
    const TARGET_VALUE_PROVIDER_TAG_NAME = 'netgen_block_manager.layout.resolver.target_value_provider';

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

        $layoutResolver = $container->findDefinition(self::SERVICE_NAME);
        $conditionMatchers = array();
        $targetValueProviders = array();

        foreach ($container->findTaggedServiceIds(self::CONDITION_MATCHER_TAG_NAME) as $conditionMatcher => $tag) {
            if (!isset($tag[0]['identifier'])) {
                throw new RuntimeException('Condition matcher service tags should have an "identifier" attribute.');
            }

            $conditionMatchers[$tag[0]['identifier']] = new Reference($conditionMatcher);
        }

        foreach ($container->findTaggedServiceIds(self::TARGET_VALUE_PROVIDER_TAG_NAME) as $targetValueProvider => $tag) {
            foreach ($tag as $tagEntry) {
                if (!isset($tagEntry['identifier'])) {
                    throw new RuntimeException('Target value provider service tags should have an "identifier" attribute.');
                }

                $targetValueProviders[$tagEntry['identifier']] = new Reference($targetValueProvider);
            }
        }

        $layoutResolver->replaceArgument(1, $targetValueProviders);
        $layoutResolver->replaceArgument(2, $conditionMatchers);
    }
}

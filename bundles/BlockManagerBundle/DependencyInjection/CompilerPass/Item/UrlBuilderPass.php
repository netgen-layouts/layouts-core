<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item;

use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class UrlBuilderPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.item.url_builder';
    const TAG_NAME = 'netgen_block_manager.item.value_url_builder';

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $urlBuilder = $container->findDefinition(self::SERVICE_NAME);

        $valueUrlBuilders = array();
        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $valueUrlBuilder => $tag) {
            if (!isset($tag[0]['value_type'])) {
                throw new RuntimeException(
                    "Value URL builder service definition must have a 'value_type' attribute in its' tag."
                );
            }

            $valueUrlBuilders[$tag[0]['value_type']] = new Reference($valueUrlBuilder);
        }

        $urlBuilder->replaceArgument(0, $valueUrlBuilders);
    }
}

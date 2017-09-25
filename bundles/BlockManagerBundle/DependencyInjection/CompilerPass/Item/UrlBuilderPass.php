<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item;

use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class UrlBuilderPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.item.url_builder';
    const TAG_NAME = 'netgen_block_manager.item.value_url_builder';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $urlBuilder = $container->findDefinition(self::SERVICE_NAME);

        $valueUrlBuilders = array();
        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $valueUrlBuilder => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['value_type'])) {
                    throw new RuntimeException(
                        "Value URL builder service definition must have a 'value_type' attribute in its' tag."
                    );
                }

                if (!preg_match('/^[A-Za-z]([A-Za-z0-9_])*$/', $tag['value_type'])) {
                    throw new RuntimeException(
                        'Value type must begin with a letter and be followed by any combination of letters, digits and underscore.'
                    );
                }

                $valueUrlBuilders[$tag['value_type']] = new Reference($valueUrlBuilder);
            }
        }

        $urlBuilder->replaceArgument(0, $valueUrlBuilders);
    }
}

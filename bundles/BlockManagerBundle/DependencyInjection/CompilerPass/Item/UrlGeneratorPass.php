<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item;

use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class UrlGeneratorPass implements CompilerPassInterface
{
    private static $serviceName = 'netgen_block_manager.item.url_generator';
    private static $tagName = 'netgen_block_manager.item.value_url_generator';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::$serviceName)) {
            return;
        }

        $urlGenerator = $container->findDefinition(self::$serviceName);

        $valueUrlGenerators = [];
        foreach ($container->findTaggedServiceIds(self::$tagName) as $valueUrlGenerator => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['value_type'])) {
                    throw new RuntimeException(
                        "Value URL generator service definition must have a 'value_type' attribute in its' tag."
                    );
                }

                if (!preg_match('/^[A-Za-z]([A-Za-z0-9_])*$/', $tag['value_type'])) {
                    throw new RuntimeException(
                        'Value type must begin with a letter and be followed by any combination of letters, digits and underscore.'
                    );
                }

                $valueUrlGenerators[$tag['value_type']] = new Reference($valueUrlGenerator);
            }
        }

        $urlGenerator->replaceArgument(0, $valueUrlGenerators);
    }
}

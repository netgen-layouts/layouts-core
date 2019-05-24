<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Item;

use Netgen\Layouts\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class CmsItemLoaderPass implements CompilerPassInterface
{
    private const SERVICE_NAME = 'netgen_layouts.item.item_loader';
    private const TAG_NAME = 'netgen_layouts.cms_value_loader';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $cmsItemLoader = $container->findDefinition(self::SERVICE_NAME);

        $valueLoaders = [];
        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $serviceName => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['value_type'])) {
                    throw new RuntimeException(
                        "Value loader service definition must have a 'value_type' attribute in its' tag."
                    );
                }

                if (preg_match('/^[A-Za-z]([A-Za-z0-9_])*$/', $tag['value_type']) !== 1) {
                    throw new RuntimeException(
                        'Value type must begin with a letter and be followed by any combination of letters, digits and underscore.'
                    );
                }

                $valueLoaders[$tag['value_type']] = new ServiceClosureArgument(new Reference($serviceName));
            }
        }

        $cmsItemLoader->addArgument(new Definition(ServiceLocator::class, [$valueLoaders]));
    }
}

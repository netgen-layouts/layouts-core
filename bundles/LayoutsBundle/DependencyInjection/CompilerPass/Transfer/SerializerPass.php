<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Transfer;

use Netgen\Layouts\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;
use function preg_match;

final class SerializerPass implements CompilerPassInterface
{
    private const SERVICE_NAME = 'netgen_layouts.transfer.serializer';
    private const TAG_NAME = 'netgen_layouts.transfer.entity_loader';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $serializer = $container->findDefinition(self::SERVICE_NAME);

        $entityLoaders = [];
        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $serviceName => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['entity_type'])) {
                    throw new RuntimeException(
                        "Entity loader service definition must have a 'entity_type' attribute in its' tag."
                    );
                }

                if (preg_match('/^[A-Za-z]([A-Za-z0-9_])*$/', $tag['entity_type']) !== 1) {
                    throw new RuntimeException(
                        'Entity type must begin with a letter and be followed by any combination of letters, digits and underscore.'
                    );
                }

                $entityLoaders[$tag['entity_type']] = new ServiceClosureArgument(new Reference($serviceName));
            }
        }

        $serializer->addArgument(new Definition(ServiceLocator::class, [$entityLoaders]));
    }
}

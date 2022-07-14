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

final class EntityHandlerPass implements CompilerPassInterface
{
    private const SERVICE_NAMES = [
        'netgen_layouts.transfer.importer',
        'netgen_layouts.transfer.serializer',
    ];

    private const TAG_NAME = 'netgen_layouts.transfer.entity_handler';

    public function process(ContainerBuilder $container): void
    {
        $handlers = [];
        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $serviceName => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['entity_type'])) {
                    throw new RuntimeException(
                        "Entity handler service definition must have a 'entity_type' attribute in its' tag.",
                    );
                }

                if (preg_match('/^[A-Za-z]([A-Za-z0-9_])*$/', $tag['entity_type']) !== 1) {
                    throw new RuntimeException(
                        'Entity type must begin with a letter and be followed by any combination of letters, digits and underscore.',
                    );
                }

                $handlers[$tag['entity_type']] = new ServiceClosureArgument(new Reference($serviceName));
            }
        }

        $handlers = new Definition(ServiceLocator::class, [$handlers]);

        foreach (self::SERVICE_NAMES as $serviceName) {
            if (!$container->has($serviceName)) {
                continue;
            }

            $container
                ->findDefinition($serviceName)
                ->addArgument($handlers);
        }
    }
}

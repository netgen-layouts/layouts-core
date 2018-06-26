<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Twig;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Custom implementation of Twig runtime loading for Symfony 2.8.
 *
 * @deprecated Remove when support for Symfony 2.8 ends.
 */
final class RuntimeLoaderPass implements CompilerPassInterface
{
    private const SERVICE_NAME = 'netgen_block_manager.templating.twig.runtime.container_loader';
    private const TAG_NAME = 'netgen_block_manager.twig.runtime';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        if ($container->has('twig.runtime_loader')) {
            // If official Twig runtime loader exists, we skip using our custom runtime loaders
            return;
        }

        $twig = $container->findDefinition('twig');
        $runtimeLoader = $container->findDefinition(self::SERVICE_NAME);

        $runtimes = array_keys($container->findTaggedServiceIds(self::TAG_NAME));
        foreach ($runtimes as $runtime) {
            $runtimeLoader->addMethodCall(
                'addRuntime',
                [
                    $container->getDefinition($runtime)->getClass(),
                    $runtime,
                ]
            );
        }

        $twig->addMethodCall(
            'addRuntimeLoader',
            [
                new Reference(self::SERVICE_NAME),
            ]
        );
    }
}

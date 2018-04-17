<?php

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
    private static $serviceName = 'netgen_block_manager.templating.twig.runtime.container_loader';
    private static $tagName = 'netgen_block_manager.twig.runtime';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::$serviceName)) {
            return;
        }

        if ($container->has('twig.runtime_loader')) {
            // If official Twig runtime loader exists, we skip using our custom runtime loaders
            return;
        }

        $twig = $container->findDefinition('twig');
        $runtimeLoader = $container->findDefinition(self::$serviceName);

        $runtimes = array_keys($container->findTaggedServiceIds(self::$tagName));
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
                new Reference(self::$serviceName),
            ]
        );
    }
}

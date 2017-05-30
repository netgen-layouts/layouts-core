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
class RuntimeLoaderPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'twig';
    const TAG_NAME = 'netgen_block_manager.twig.runtime_loader';

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->has('twig.runtime_loader')) {
            // If official Twig runtime loader exists,
            // we skip using our custom runtime loaders
            return;
        }

        $twig = $container->findDefinition('twig');
        $runtimeLoaders = array_keys($container->findTaggedServiceIds(self::TAG_NAME));

        foreach ($runtimeLoaders as $runtimeLoader) {
            $twig->addMethodCall(
                'addRuntimeLoader',
                array(
                    new Reference($runtimeLoader),
                )
            );
        }
    }
}

<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Context;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ContextBuilderPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.context.builder';
    const TAG_NAME = 'netgen_block_manager.context.provider';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $contextBuilder = $container->findDefinition(self::SERVICE_NAME);
        $contextProviders = array_keys($container->findTaggedServiceIds(self::TAG_NAME));

        foreach ($contextProviders as $contextProvider) {
            $contextBuilder->addMethodCall(
                'registerProvider',
                array(new Reference($contextProvider))
            );
        }
    }
}

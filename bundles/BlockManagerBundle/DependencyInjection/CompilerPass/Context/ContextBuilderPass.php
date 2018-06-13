<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Context;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ContextBuilderPass implements CompilerPassInterface
{
    private static $serviceName = 'netgen_block_manager.context.builder';
    private static $tagName = 'netgen_block_manager.context.provider';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::$serviceName)) {
            return;
        }

        $contextBuilder = $container->findDefinition(self::$serviceName);
        $contextProviders = array_keys($container->findTaggedServiceIds(self::$tagName));

        foreach ($contextProviders as $contextProvider) {
            $contextBuilder->addMethodCall(
                'registerProvider',
                [new Reference($contextProvider)]
            );
        }
    }
}

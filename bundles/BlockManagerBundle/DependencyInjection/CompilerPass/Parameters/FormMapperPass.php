<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters;

use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class FormMapperPass implements CompilerPassInterface
{
    private static $serviceName = 'netgen_block_manager.parameters.registry.form_mapper';
    private static $tagName = 'netgen_block_manager.parameters.form.mapper';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::$serviceName)) {
            return;
        }

        $registry = $container->findDefinition(self::$serviceName);

        foreach ($container->findTaggedServiceIds(self::$tagName) as $formMapper => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['type'])) {
                    throw new RuntimeException(
                        "Parameter form mapper service definition must have a 'type' attribute in its' tag."
                    );
                }

                $registry->addMethodCall(
                    'addFormMapper',
                    [
                        $tag['type'],
                        new Reference($formMapper),
                    ]
                );
            }
        }
    }
}

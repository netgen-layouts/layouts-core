<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\Form;

use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ConditionTypePass implements CompilerPassInterface
{
    private static $serviceName = 'netgen_block_manager.layout.resolver.form.condition_type';
    private static $tagName = 'netgen_block_manager.layout.resolver.form.condition_type.mapper';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::$serviceName)) {
            return;
        }

        $formType = $container->findDefinition(self::$serviceName);
        $mapperServices = $container->findTaggedServiceIds(self::$tagName);

        $mappers = [];
        foreach ($mapperServices as $mapperService => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['condition_type'])) {
                    throw new RuntimeException('Condition type form mapper service tags should have an "condition_type" attribute.');
                }

                $mappers[$tag['condition_type']] = new Reference($mapperService);
            }
        }

        $formType->replaceArgument(0, $mappers);
    }
}

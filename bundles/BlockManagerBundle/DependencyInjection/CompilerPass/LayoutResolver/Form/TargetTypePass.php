<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\Form;

use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class TargetTypePass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.layout.resolver.form.target_type';
    const TAG_NAME = 'netgen_block_manager.layout.resolver.form.target_type.mapper';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $formType = $container->findDefinition(self::SERVICE_NAME);
        $mapperServices = $container->findTaggedServiceIds(self::TAG_NAME);

        $mappers = array();
        foreach ($mapperServices as $mapperService => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['target_type'])) {
                    throw new RuntimeException('Target type form mapper service tags should have an "target_type" attribute.');
                }

                $mappers[$tag['target_type']] = new Reference($mapperService);
            }
        }

        $formType->replaceArgument(0, $mappers);
    }
}

<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\Form;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Netgen\BlockManager\Exception\RuntimeException;

class ConditionTypePass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.layout.resolver.form.condition_type';
    const TAG_NAME = 'netgen_block_manager.layout.resolver.form.condition_type.mapper';

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
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
                if (!isset($tag['condition_type'])) {
                    throw new RuntimeException('Condition type form mapper service tags should have an "condition_type" attribute.');
                }

                $mappers[$tag['condition_type']] = new Reference($mapperService);
            }
        }

        $formType->replaceArgument(0, $mappers);
    }
}

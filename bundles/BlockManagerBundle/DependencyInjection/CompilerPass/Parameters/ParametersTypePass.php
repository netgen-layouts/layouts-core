<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Netgen\BlockManager\Exception\RuntimeException;

class ParametersTypePass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.parameters.form.type.parameters';
    const TAG_NAME = 'netgen_block_manager.parameters.form.mapper';

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

        $parametersForm = $container->findDefinition(self::SERVICE_NAME);
        $formMappers = array();

        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $formMapper => $tag) {
            if (!isset($tag[0]['type'])) {
                throw new RuntimeException(
                    "Parameter form mapper service definition must have a 'type' attribute in its' tag."
                );
            }

            $formMappers[$tag[0]['type']] = new Reference($formMapper);
        }

        $parametersForm->replaceArgument(
            0,
            $formMappers
        );
    }
}

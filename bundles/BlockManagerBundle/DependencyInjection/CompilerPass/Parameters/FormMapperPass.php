<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use RuntimeException;

class FormMapperPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.parameters.form_mapper';
    const TAG_NAME = 'netgen_block_manager.parameters.parameter_handler';

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

        $formMapper = $container->findDefinition(self::SERVICE_NAME);
        $parameterHandlers = $container->findTaggedServiceIds(self::TAG_NAME);

        foreach ($parameterHandlers as $parameterHandler => $tag) {
            if (!isset($tag[0]['type'])) {
                throw new RuntimeException(
                    "Parameter handler service definition must have a 'type' attribute in its' tag."
                );
            }

            $formMapper->addMethodCall(
                'addParameterHandler',
                array($tag[0]['type'], new Reference($parameterHandler))
            );
        }
    }
}

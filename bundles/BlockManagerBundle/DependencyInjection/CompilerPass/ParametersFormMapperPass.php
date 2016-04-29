<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ParametersFormMapperPass implements CompilerPassInterface
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

        foreach ($parameterHandlers as $parameterHandler => $parameterHandlerTag) {
            $formMapper->addMethodCall(
                'addParameterHandler',
                array($parameterHandlerTag[0]['parameter_type'], new Reference($parameterHandler))
            );
        }
    }
}

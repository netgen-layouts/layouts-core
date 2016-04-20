<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Form;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ParameterMapperPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.form.parameter_mapper';
    const TAG_NAME = 'netgen_block_manager.form.parameter_handler';

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

        $parameterMapper = $container->findDefinition(self::SERVICE_NAME);
        $parameterHandlers = $container->findTaggedServiceIds(self::TAG_NAME);

        foreach ($parameterHandlers as $parameterHandler => $parameterHandlerTag) {
            $parameterMapper->addMethodCall(
                'addParameterHandler',
                array($parameterHandlerTag[0]['parameter_type'], new Reference($parameterHandler))
            );
        }
    }
}

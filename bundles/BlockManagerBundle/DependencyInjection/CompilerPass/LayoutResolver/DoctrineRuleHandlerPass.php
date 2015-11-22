<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DoctrineRuleHandlerPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.layout_resolver.rule_handler.doctrine.handler';
    const TAG_NAME = 'netgen_block_manager.layout_resolver.rule_handler.doctrine.target_handler';

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

        $ruleHandler = $container->findDefinition(self::SERVICE_NAME);
        $targetHandlers = array_keys($container->findTaggedServiceIds(self::TAG_NAME));

        foreach ($targetHandlers as $targetHandler) {
            $ruleHandler->addMethodCall(
                'addTargetHandler',
                array(new Reference($targetHandler))
            );
        }
    }
}

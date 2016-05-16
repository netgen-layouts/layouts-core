<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use RuntimeException;

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
            if (!$container->has(self::SERVICE_NAME)) {
                throw new RuntimeException("Service '{self::SERVICE_NAME}' is missing.");
            }
        }

        $ruleHandler = $container->findDefinition(self::SERVICE_NAME);
        $targetHandlers = $container->findTaggedServiceIds(self::TAG_NAME);

        foreach ($targetHandlers as $targetHandler => $tag) {
            if (!isset($tag[0]['alias'])) {
                throw new RuntimeException('Doctrine target handler service tags should have an "alias" attribute.');
            }

            $ruleHandler->addMethodCall(
                'addTargetHandler',
                array($tag[0]['alias'], new Reference($targetHandler))
            );
        }
    }
}

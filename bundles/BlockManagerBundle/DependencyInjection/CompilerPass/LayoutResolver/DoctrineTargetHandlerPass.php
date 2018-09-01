<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\DefinitionClassTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class DoctrineTargetHandlerPass implements CompilerPassInterface
{
    use DefinitionClassTrait;

    private const SERVICE_NAME = 'netgen_block_manager.persistence.doctrine.layout_resolver.query_handler';
    private const TAG_NAME = 'netgen_block_manager.layout.resolver.target_handler.doctrine';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $layoutResolverQueryHandler = $container->findDefinition(self::SERVICE_NAME);
        $targetHandlers = [];

        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $targetHandler => $tags) {
            foreach ($tags as $tag) {
                if (isset($tag['target_type'])) {
                    $targetHandlers[$tag['target_type']] = new Reference($targetHandler);
                    continue 2;
                }
            }

            $handlerClass = $this->getDefinitionClass($container, $targetHandler);
            if (isset($handlerClass::$defaultTargetType)) {
                $targetHandlers[$handlerClass::$defaultTargetType] = new Reference($targetHandler);
                continue;
            }
        }

        $layoutResolverQueryHandler->replaceArgument(2, $targetHandlers);
    }
}

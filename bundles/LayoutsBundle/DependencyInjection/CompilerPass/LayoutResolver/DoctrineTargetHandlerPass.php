<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\DefinitionClassTrait;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class DoctrineTargetHandlerPass implements CompilerPassInterface
{
    use DefinitionClassTrait;

    private const SERVICE_NAME = 'netgen_layouts.persistence.doctrine.layout_resolver.query_handler';
    private const TAG_NAME = 'netgen_layouts.target_type.doctrine_handler';

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
                    $targetHandlers[$tag['target_type']] = new ServiceClosureArgument(new Reference($targetHandler));

                    continue 2;
                }
            }

            $handlerClass = $this->getDefinitionClass($container, $targetHandler);
            if (isset($handlerClass::$defaultTargetType)) {
                $targetHandlers[$handlerClass::$defaultTargetType] = new ServiceClosureArgument(new Reference($targetHandler));

                continue;
            }
        }

        $layoutResolverQueryHandler->addArgument(new Definition(ServiceLocator::class, [$targetHandlers]));
    }
}

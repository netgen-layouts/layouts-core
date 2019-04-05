<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ViewBuilderPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    private const SERVICE_NAME = 'netgen_block_manager.view.view_builder';
    private const TAG_NAME = 'netgen_block_manager.view.provider';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $viewBuilder = $container->findDefinition(self::SERVICE_NAME);
        $viewProviders = $this->findAndSortTaggedServices(self::TAG_NAME, $container);

        $viewBuilder->replaceArgument(2, $viewProviders);
    }
}

<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class NetgenLayoutsBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CompilerPass\Block\BlockDefinitionPass());
        $container->addCompilerPass(new CompilerPass\LayoutResolver\DoctrineTargetHandlerPass());
        $container->addCompilerPass(new CompilerPass\LayoutResolver\Form\ConditionTypePass());
        $container->addCompilerPass(new CompilerPass\LayoutResolver\Form\TargetTypePass());
        $container->addCompilerPass(new CompilerPass\View\TemplateResolverPass());
        $container->addCompilerPass(new CompilerPass\View\DefaultViewTemplatesPass());
        $container->addCompilerPass(new CompilerPass\Templating\PluginRendererPass());
        $container->addCompilerPass(new CompilerPass\Parameters\ParametersFormPass());
        $container->addCompilerPass(new CompilerPass\Item\ValueTypePass());
        $container->addCompilerPass(new CompilerPass\Item\CmsItemLoaderPass());
        $container->addCompilerPass(new CompilerPass\Item\UrlGeneratorPass());
        $container->addCompilerPass(new CompilerPass\Collection\ItemDefinitionPass());
        $container->addCompilerPass(new CompilerPass\Collection\QueryTypePass());
        $container->addCompilerPass(new CompilerPass\Layout\LayoutTypePass());
        $container->addCompilerPass(new CompilerPass\Block\BlockTypePass());
        $container->addCompilerPass(new CompilerPass\Block\BlockTypeGroupPass());
        $container->addCompilerPass(new CompilerPass\HttpCache\LegacyTagHandlerPass());
        $container->addCompilerPass(new CompilerPass\HttpCache\CacheManagerPass());
        $container->addCompilerPass(new CompilerPass\HttpCache\ConfigureHttpCachePass());
        $container->addCompilerPass(new CompilerPass\HttpCache\FOSHostHeaderProviderPass());
        $container->addCompilerPass(new CompilerPass\Design\ThemePass());
        $container->addCompilerPass(new CompilerPass\Transfer\EntityHandlerPass());
        $container->addCompilerPass(new CompilerPass\ControllerContainerPass());
        $container->addCompilerPass(new CompilerPass\CleanupConfigPass(), PassConfig::TYPE_REMOVE);
    }
}

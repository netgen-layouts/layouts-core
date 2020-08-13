<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\View;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\DefinitionClassTrait;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class TemplateResolverPass implements CompilerPassInterface
{
    use DefinitionClassTrait;

    private const SERVICE_NAME = 'netgen_layouts.view.template_resolver';
    private const TAG_NAME = 'netgen_layouts.view_matcher';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $templateResolver = $container->findDefinition(self::SERVICE_NAME);
        $matcherServices = $container->findTaggedServiceIds(self::TAG_NAME);

        $matchers = [];
        foreach ($matcherServices as $serviceName => $tags) {
            foreach ($tags as $tag) {
                if (isset($tag['identifier'])) {
                    $matchers[$tag['identifier']] = new ServiceClosureArgument(new Reference($serviceName));

                    continue 2;
                }
            }

            $matcherClass = $this->getDefinitionClass($container, $serviceName);
            if (isset($matcherClass::$defaultIdentifier)) {
                $matchers[$matcherClass::$defaultIdentifier] = new ServiceClosureArgument(new Reference($serviceName));

                continue;
            }
        }

        $templateResolver->addArgument(new Definition(ServiceLocator::class, [$matchers]));
    }
}

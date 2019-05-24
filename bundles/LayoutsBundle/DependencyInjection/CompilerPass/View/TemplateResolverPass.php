<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\View;

use Netgen\Layouts\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class TemplateResolverPass implements CompilerPassInterface
{
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
                if (!isset($tag['identifier'])) {
                    throw new RuntimeException(
                        "Matcher service definition must have an 'identifier' attribute in its' tag."
                    );
                }

                $matchers[$tag['identifier']] = new ServiceClosureArgument(new Reference($serviceName));
            }
        }

        $templateResolver->addArgument(new Definition(ServiceLocator::class, [$matchers]));
    }
}

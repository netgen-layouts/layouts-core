<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View;

use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class TemplateResolverPass implements CompilerPassInterface
{
    private static $serviceName = 'netgen_block_manager.view.template_resolver';
    private static $tagName = 'netgen_block_manager.view.template_matcher';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::$serviceName)) {
            return;
        }

        $templateResolver = $container->findDefinition(self::$serviceName);
        $matcherServices = $container->findTaggedServiceIds(self::$tagName);

        $matchers = [];
        foreach ($matcherServices as $serviceName => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['identifier'])) {
                    throw new RuntimeException(
                        "Matcher service definition must have an 'identifier' attribute in its' tag."
                    );
                }

                $matchers[$tag['identifier']] = new Reference($serviceName);
            }
        }

        $templateResolver->replaceArgument(0, $matchers);
    }
}

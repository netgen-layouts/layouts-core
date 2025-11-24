<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\LayoutResolver\Form;

use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class ConditionTypePass implements CompilerPassInterface
{
    private const string SERVICE_NAME = 'netgen_layouts.layout.resolver.form.condition_type';
    private const string TAG_NAME = 'netgen_layouts.condition_type.form_mapper';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $formType = $container->findDefinition(self::SERVICE_NAME);
        $mapperServices = $container->findTaggedServiceIds(self::TAG_NAME);

        $mappers = [];
        foreach ($mapperServices as $mapperService => $tags) {
            foreach ($tags as $tag) {
                if (isset($tag['condition_type'])) {
                    $mappers[$tag['condition_type']] = new ServiceClosureArgument(new Reference($mapperService));

                    continue 2;
                }
            }
        }

        $formType->replaceArgument(0, new Definition(ServiceLocator::class, [$mappers]));
    }
}

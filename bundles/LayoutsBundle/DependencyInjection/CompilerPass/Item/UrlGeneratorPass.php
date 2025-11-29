<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Item;

use Netgen\Layouts\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

use function preg_match;

final class UrlGeneratorPass implements CompilerPassInterface
{
    private const string SERVICE_NAME = 'netgen_layouts.item.url_generator';
    private const string TAG_NAME = 'netgen_layouts.cms_value_url_generator';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $urlGenerator = $container->findDefinition(self::SERVICE_NAME);

        $valueUrlGenerators = [];
        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $valueUrlGenerator => $tags) {
            foreach ($tags as $tag) {
                if (isset($tag['value_type'])) {
                    $this->validateValueType($tag['value_type']);
                    $valueUrlGenerators[$tag['value_type']] = new ServiceClosureArgument(new Reference($valueUrlGenerator));

                    continue 2;
                }
            }
        }

        $urlGenerator->replaceArgument(0, new Definition(ServiceLocator::class, [$valueUrlGenerators]));
    }

    private function validateValueType(string $valueType): void
    {
        if (preg_match('/^[A-Za-z]\w*$/', $valueType) !== 1) {
            throw new RuntimeException(
                'Value type must begin with a letter and be followed by any combination of letters, digits and underscore.',
            );
        }
    }
}

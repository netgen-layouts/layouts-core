<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Item;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\DefinitionClassTrait;
use Netgen\Layouts\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

use function preg_match;

final class CmsItemLoaderPass implements CompilerPassInterface
{
    use DefinitionClassTrait;

    private const SERVICE_NAME = 'netgen_layouts.item.item_loader';
    private const TAG_NAME = 'netgen_layouts.cms_value_loader';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $cmsItemLoader = $container->findDefinition(self::SERVICE_NAME);

        $valueLoaders = [];
        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $serviceName => $tags) {
            foreach ($tags as $tag) {
                if (isset($tag['value_type'])) {
                    $this->validateValueType($tag['value_type']);
                    $valueLoaders[$tag['value_type']] = new ServiceClosureArgument(new Reference($serviceName));

                    continue 2;
                }
            }

            $valueLoaderClass = $this->getDefinitionClass($container, $serviceName);
            if (isset($valueLoaderClass::$defaultValueType)) {
                $this->validateValueType($valueLoaderClass::$defaultValueType);
                $valueLoaders[$valueLoaderClass::$defaultValueType] = new ServiceClosureArgument(new Reference($serviceName));

                continue;
            }
        }

        $cmsItemLoader->addArgument(new Definition(ServiceLocator::class, [$valueLoaders]));
    }

    private function validateValueType(string $valueType): void
    {
        if (preg_match('/^[A-Za-z]([A-Za-z0-9_])*$/', $valueType) !== 1) {
            throw new RuntimeException(
                'Value type must begin with a letter and be followed by any combination of letters, digits and underscore.',
            );
        }
    }
}

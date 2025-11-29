<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Item;

use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Item\ValueType\ValueType;
use Netgen\Layouts\Item\ValueType\ValueTypeFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

use function sprintf;

final class ValueTypePass implements CompilerPassInterface
{
    private const string SERVICE_NAME = 'netgen_layouts.item.registry.value_type';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        /** @var array<string, mixed[]> $valueTypes */
        $valueTypes = $container->getParameter('netgen_layouts.value_types');
        $valueTypeServices = [...$this->buildValueTypes($container, $valueTypes)];

        $registry = $container->findDefinition(self::SERVICE_NAME);

        $registry->replaceArgument(0, $valueTypeServices);
    }

    /**
     * Builds the value type objects from provided configuration.
     *
     * @param array<string, mixed[]> $valueTypes
     *
     * @return iterable<string, \Symfony\Component\DependencyInjection\Reference>
     */
    private function buildValueTypes(ContainerBuilder $container, array $valueTypes): iterable
    {
        foreach ($valueTypes as $identifier => $valueType) {
            if ($valueType['manual_items'] === true) {
                $this->validateBrowserType($container, $identifier);
            }

            $serviceIdentifier = sprintf('netgen_layouts.item.value_type.%s', $identifier);

            $container->register($serviceIdentifier, ValueType::class)
                ->setArguments([$identifier, $valueType])
                ->setFactory([ValueTypeFactory::class, 'buildValueType']);

            yield $identifier => new Reference($serviceIdentifier);
        }
    }

    /**
     * Validates that the provided Content Browser item type exists in the system.
     */
    private function validateBrowserType(ContainerBuilder $container, string $valueType): void
    {
        if ($container->has(sprintf('netgen_content_browser.config.%s', $valueType))) {
            return;
        }

        throw new RuntimeException(
            sprintf(
                'Netgen Content Browser backend for "%s" value type does not exist.',
                $valueType,
            ),
        );
    }
}

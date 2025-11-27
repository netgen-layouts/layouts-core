<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Item;

use Netgen\Layouts\Collection\Item\ItemDefinitionFactory;
use Netgen\Layouts\Config\ConfigDefinitionFactory;
use Netgen\Layouts\Config\ConfigDefinitionInterface;
use Netgen\Layouts\Parameters\ParameterBuilderFactory;
use Netgen\Layouts\Parameters\Registry\ParameterTypeRegistry;
use Netgen\Layouts\Tests\Config\Stubs\ConfigDefinitionHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ItemDefinitionFactory::class)]
final class ItemDefinitionFactoryTest extends TestCase
{
    private ItemDefinitionFactory $factory;

    protected function setUp(): void
    {
        $configDefinitionFactory = new ConfigDefinitionFactory(
            new ParameterBuilderFactory(
                new ParameterTypeRegistry([]),
            ),
        );

        $this->factory = new ItemDefinitionFactory(
            $configDefinitionFactory,
        );
    }

    public function testBuildItemDefinition(): void
    {
        $itemDefinition = $this->factory->buildItemDefinition(
            'value_type',
            [
                'test' => new ConfigDefinitionHandler(),
                'test2' => new ConfigDefinitionHandler(),
            ],
        );

        self::assertSame('value_type', $itemDefinition->valueType);

        $configDefinitions = $itemDefinition->configDefinitions;
        self::assertArrayHasKey('test', $configDefinitions);
        self::assertArrayHasKey('test2', $configDefinitions);
        self::assertContainsOnlyInstancesOf(ConfigDefinitionInterface::class, $configDefinitions);
    }
}

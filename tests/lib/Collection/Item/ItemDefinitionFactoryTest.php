<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Item;

use Netgen\Layouts\Collection\Item\ItemDefinitionFactory;
use Netgen\Layouts\Config\ConfigDefinitionFactory;
use Netgen\Layouts\Config\ConfigDefinitionInterface;
use Netgen\Layouts\Parameters\ParameterBuilderFactory;
use Netgen\Layouts\Parameters\Registry\ParameterTypeRegistry;
use Netgen\Layouts\Tests\Config\Stubs\ConfigDefinitionHandler;
use PHPUnit\Framework\TestCase;

final class ItemDefinitionFactoryTest extends TestCase
{
    private ConfigDefinitionFactory $configDefinitionFactory;

    private ItemDefinitionFactory $factory;

    protected function setUp(): void
    {
        $this->configDefinitionFactory = new ConfigDefinitionFactory(
            new ParameterBuilderFactory(
                new ParameterTypeRegistry([]),
            ),
        );

        $this->factory = new ItemDefinitionFactory(
            $this->configDefinitionFactory,
        );
    }

    /**
     * @covers \Netgen\Layouts\Collection\Item\ItemDefinitionFactory::__construct
     * @covers \Netgen\Layouts\Collection\Item\ItemDefinitionFactory::buildItemDefinition
     */
    public function testBuildItemDefinition(): void
    {
        $itemDefinition = $this->factory->buildItemDefinition(
            'value_type',
            [
                'test' => new ConfigDefinitionHandler(),
                'test2' => new ConfigDefinitionHandler(),
            ],
        );

        self::assertSame('value_type', $itemDefinition->getValueType());

        $configDefinitions = $itemDefinition->getConfigDefinitions();
        self::assertArrayHasKey('test', $configDefinitions);
        self::assertArrayHasKey('test2', $configDefinitions);
        self::assertContainsOnlyInstancesOf(ConfigDefinitionInterface::class, $configDefinitions);
    }
}

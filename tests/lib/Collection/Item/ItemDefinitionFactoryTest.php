<?php

namespace Netgen\BlockManager\Tests\Collection\Item;

use Netgen\BlockManager\Collection\Item\ItemDefinitionFactory;
use Netgen\BlockManager\Collection\Item\ItemDefinitionInterface;
use Netgen\BlockManager\Config\ConfigDefinitionFactory;
use Netgen\BlockManager\Config\ConfigDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderFactory;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use Netgen\BlockManager\Tests\Config\Stubs\CollectionItem\VisibilityConfigHandler;
use PHPUnit\Framework\TestCase;

final class ItemDefinitionFactoryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinitionFactory
     */
    private $configDefinitionFactory;

    /**
     * @var \Netgen\BlockManager\Collection\Item\ItemDefinitionFactory
     */
    private $factory;

    public function setUp()
    {
        $this->configDefinitionFactory = new ConfigDefinitionFactory(
            new ParameterBuilderFactory(
                new ParameterTypeRegistry()
            )
        );

        $this->factory = new ItemDefinitionFactory(
            $this->configDefinitionFactory
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Item\ItemDefinitionFactory::__construct
     * @covers \Netgen\BlockManager\Collection\Item\ItemDefinitionFactory::buildItemDefinition
     */
    public function testBuildItemDefinition()
    {
        $itemDefinition = $this->factory->buildItemDefinition(
            'value_type',
            array(
                'test' => new VisibilityConfigHandler(),
                'test2' => new VisibilityConfigHandler(),
            )
        );

        $this->assertInstanceOf(ItemDefinitionInterface::class, $itemDefinition);
        $this->assertEquals('value_type', $itemDefinition->getValueType());

        $configDefinitions = $itemDefinition->getConfigDefinitions();
        $this->assertArrayHasKey('test', $configDefinitions);
        $this->assertArrayHasKey('test2', $configDefinitions);

        $this->assertInstanceOf(ConfigDefinitionInterface::class, $configDefinitions['test']);
        $this->assertInstanceOf(ConfigDefinitionInterface::class, $configDefinitions['test2']);
    }
}

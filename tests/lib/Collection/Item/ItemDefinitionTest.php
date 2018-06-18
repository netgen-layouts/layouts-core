<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Item;

use Netgen\BlockManager\Collection\Item\ItemDefinition;
use Netgen\BlockManager\Config\ConfigDefinition;
use PHPUnit\Framework\TestCase;

final class ItemDefinitionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Collection\Item\ItemDefinition
     */
    private $itemDefinition;

    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinition
     */
    private $configDefinition;

    public function setUp(): void
    {
        $this->configDefinition = new ConfigDefinition();

        $this->itemDefinition = new ItemDefinition(
            [
                'valueType' => 'value_type',
                'configDefinitions' => ['config' => $this->configDefinition],
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Item\ItemDefinition::getValueType
     */
    public function testGetValueType(): void
    {
        $this->assertSame('value_type', $this->itemDefinition->getValueType());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Item\ItemDefinition::getConfigDefinitions
     */
    public function testGetConfigDefinitions(): void
    {
        $this->assertSame(
            ['config' => $this->configDefinition],
            $this->itemDefinition->getConfigDefinitions()
        );
    }
}

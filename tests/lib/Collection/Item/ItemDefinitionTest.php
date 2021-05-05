<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Item;

use Netgen\Layouts\Collection\Item\ItemDefinition;
use Netgen\Layouts\Config\ConfigDefinition;
use Netgen\Layouts\Exception\Config\ConfigDefinitionException;
use PHPUnit\Framework\TestCase;

final class ItemDefinitionTest extends TestCase
{
    private ItemDefinition $itemDefinition;

    private ConfigDefinition $configDefinition;

    protected function setUp(): void
    {
        $this->configDefinition = new ConfigDefinition();

        $this->itemDefinition = ItemDefinition::fromArray(
            [
                'valueType' => 'value_type',
                'configDefinitions' => ['config' => $this->configDefinition],
            ],
        );
    }

    /**
     * @covers \Netgen\Layouts\Collection\Item\ItemDefinition::getValueType
     */
    public function testGetValueType(): void
    {
        self::assertSame('value_type', $this->itemDefinition->getValueType());
    }

    /**
     * @covers \Netgen\Layouts\Collection\Item\ItemDefinition::getConfigDefinition
     */
    public function testGetConfigDefinition(): void
    {
        self::assertSame(
            $this->configDefinition,
            $this->itemDefinition->getConfigDefinition('config'),
        );
    }

    /**
     * @covers \Netgen\Layouts\Collection\Item\ItemDefinition::getConfigDefinition
     */
    public function testGetConfigDefinitionThrowsConfigDefinitionException(): void
    {
        $this->expectException(ConfigDefinitionException::class);
        $this->expectExceptionMessage('Config definition with "unknown" config key does not exist.');

        $this->itemDefinition->getConfigDefinition('unknown');
    }

    /**
     * @covers \Netgen\Layouts\Collection\Item\ItemDefinition::hasConfigDefinition
     */
    public function testHasConfigDefinition(): void
    {
        self::assertTrue($this->itemDefinition->hasConfigDefinition('config'));
    }

    /**
     * @covers \Netgen\Layouts\Collection\Item\ItemDefinition::hasConfigDefinition
     */
    public function testHasConfigDefinitionWithNonExistentDefinition(): void
    {
        self::assertFalse($this->itemDefinition->hasConfigDefinition('unknown'));
    }

    /**
     * @covers \Netgen\Layouts\Collection\Item\ItemDefinition::getConfigDefinitions
     */
    public function testGetConfigDefinitions(): void
    {
        self::assertSame(
            ['config' => $this->configDefinition],
            $this->itemDefinition->getConfigDefinitions(),
        );
    }
}

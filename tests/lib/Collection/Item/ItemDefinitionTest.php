<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Item;

use Netgen\Layouts\Collection\Item\ItemDefinition;
use Netgen\Layouts\Config\ConfigDefinition;
use Netgen\Layouts\Exception\Config\ConfigDefinitionException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ItemDefinition::class)]
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

    public function testGetValueType(): void
    {
        self::assertSame('value_type', $this->itemDefinition->valueType);
    }

    public function testGetConfigDefinition(): void
    {
        self::assertSame(
            $this->configDefinition,
            $this->itemDefinition->getConfigDefinition('config'),
        );
    }

    public function testGetConfigDefinitionThrowsConfigDefinitionException(): void
    {
        $this->expectException(ConfigDefinitionException::class);
        $this->expectExceptionMessage('Config definition with "unknown" config key does not exist.');

        $this->itemDefinition->getConfigDefinition('unknown');
    }

    public function testHasConfigDefinition(): void
    {
        self::assertTrue($this->itemDefinition->hasConfigDefinition('config'));
    }

    public function testHasConfigDefinitionWithNonExistentDefinition(): void
    {
        self::assertFalse($this->itemDefinition->hasConfigDefinition('unknown'));
    }

    public function testGetConfigDefinitions(): void
    {
        self::assertSame(
            ['config' => $this->configDefinition],
            $this->itemDefinition->getConfigDefinitions(),
        );
    }
}

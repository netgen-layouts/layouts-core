<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Collection\Item\ItemDefinition;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\Item\CmsItemInterface;
use Netgen\BlockManager\Item\NullCmsItem;
use PHPUnit\Framework\TestCase;

final class ItemTest extends TestCase
{
    public function testInstance(): void
    {
        $this->assertInstanceOf(Value::class, new Item());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::__construct
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getCmsItem
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getCollectionId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getDefinition
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getPosition
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getType
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getValue
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::isManual
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::isOverride
     */
    public function testSetProperties(): void
    {
        $cmsItem = new CmsItem();
        $definition = new ItemDefinition();

        $item = Item::fromArray(
            [
                'id' => 42,
                'collectionId' => 30,
                'definition' => $definition,
                'position' => 3,
                'type' => Item::TYPE_OVERRIDE,
                'value' => 32,
                'cmsItem' => function () use ($cmsItem): CmsItemInterface {
                    return $cmsItem;
                },
            ]
        );

        $this->assertSame(42, $item->getId());
        $this->assertSame(30, $item->getCollectionId());
        $this->assertSame($definition, $item->getDefinition());
        $this->assertSame(3, $item->getPosition());
        $this->assertSame(Item::TYPE_OVERRIDE, $item->getType());
        $this->assertFalse($item->isManual());
        $this->assertTrue($item->isOverride());
        $this->assertSame(32, $item->getValue());
        $this->assertSame($cmsItem, $item->getCmsItem());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::isValid
     * @dataProvider isValidProvider
     */
    public function testIsValid(bool $cmsItemVisible, bool $isValid): void
    {
        $item = Item::fromArray(
            [
                'definition' => new ItemDefinition(),
                'cmsItem' => CmsItem::fromArray(['isVisible' => $cmsItemVisible]),
            ]
        );

        $this->assertSame($isValid, $item->isValid());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::isValid
     */
    public function testIsValidWithNullCmsItem(): void
    {
        $item = Item::fromArray(
            [
                'cmsItem' => new NullCmsItem('value'),
            ]
        );

        $this->assertFalse($item->isValid());
    }

    public function isValidProvider(): array
    {
        return [
            [true, true],
            [false, false],
        ];
    }
}

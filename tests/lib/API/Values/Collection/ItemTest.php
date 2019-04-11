<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Collection;

use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\Collection\Item\ItemDefinition;
use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\CmsItemInterface;
use Netgen\Layouts\Item\NullCmsItem;
use PHPUnit\Framework\TestCase;

final class ItemTest extends TestCase
{
    public function testInstance(): void
    {
        self::assertInstanceOf(Value::class, new Item());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Collection\Item::getCmsItem
     * @covers \Netgen\Layouts\API\Values\Collection\Item::getCollectionId
     * @covers \Netgen\Layouts\API\Values\Collection\Item::getDefinition
     * @covers \Netgen\Layouts\API\Values\Collection\Item::getId
     * @covers \Netgen\Layouts\API\Values\Collection\Item::getPosition
     * @covers \Netgen\Layouts\API\Values\Collection\Item::getValue
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
                'value' => 32,
                'cmsItem' => static function () use ($cmsItem): CmsItemInterface {
                    return $cmsItem;
                },
            ]
        );

        self::assertSame(42, $item->getId());
        self::assertSame(30, $item->getCollectionId());
        self::assertSame($definition, $item->getDefinition());
        self::assertSame(3, $item->getPosition());
        self::assertSame(32, $item->getValue());
        self::assertSame($cmsItem, $item->getCmsItem());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Collection\Item::isValid
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

        self::assertSame($isValid, $item->isValid());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Collection\Item::isValid
     */
    public function testIsValidWithNullCmsItem(): void
    {
        $item = Item::fromArray(
            [
                'cmsItem' => new NullCmsItem('value'),
            ]
        );

        self::assertFalse($item->isValid());
    }

    public function isValidProvider(): array
    {
        return [
            [true, true],
            [false, false],
        ];
    }
}

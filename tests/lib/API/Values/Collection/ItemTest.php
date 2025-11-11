<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Collection;

use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\Collection\Item\ItemDefinition;
use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\CmsItemInterface;
use Netgen\Layouts\Item\NullCmsItem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(Item::class)]
final class ItemTest extends TestCase
{
    public function testSetProperties(): void
    {
        $cmsItem = new CmsItem();
        $definition = new ItemDefinition();

        $itemUuid = Uuid::uuid4();
        $collectionUuid = Uuid::uuid4();

        $item = Item::fromArray(
            [
                'id' => $itemUuid,
                'collectionId' => $collectionUuid,
                'definition' => $definition,
                'position' => 3,
                'value' => 32,
                'viewType' => 'overlay',
                'cmsItem' => static fn (): CmsItemInterface => $cmsItem,
            ],
        );

        self::assertSame($itemUuid->toString(), $item->getId()->toString());
        self::assertSame($collectionUuid->toString(), $item->getCollectionId()->toString());
        self::assertSame($definition, $item->getDefinition());
        self::assertSame(3, $item->getPosition());
        self::assertSame(32, $item->getValue());
        self::assertSame('overlay', $item->getViewType());
        self::assertSame($cmsItem, $item->getCmsItem());
    }

    #[DataProvider('isValidDataProvider')]
    public function testIsValid(bool $cmsItemVisible, bool $isValid): void
    {
        $item = Item::fromArray(
            [
                'id' => Uuid::uuid4(),
                'collectionId' => Uuid::uuid4(),
                'definition' => new ItemDefinition(),
                'cmsItem' => CmsItem::fromArray(['isVisible' => $cmsItemVisible]),
            ],
        );

        self::assertSame($isValid, $item->isValid());
    }

    public function testIsValidWithNullCmsItem(): void
    {
        $item = Item::fromArray(
            [
                'id' => Uuid::uuid4(),
                'collectionId' => Uuid::uuid4(),
                'cmsItem' => new NullCmsItem('value'),
            ],
        );

        self::assertFalse($item->isValid());
    }

    public static function isValidDataProvider(): iterable
    {
        return [
            [true, true],
            [false, false],
        ];
    }
}

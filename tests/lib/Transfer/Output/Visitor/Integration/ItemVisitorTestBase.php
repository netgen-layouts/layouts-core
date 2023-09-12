<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Transfer\Output\Visitor\ItemVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use Ramsey\Uuid\Uuid;

/**
 * @extends \Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\VisitorTestBase<\Netgen\Layouts\API\Values\Collection\Item>
 */
abstract class ItemVisitorTestBase extends VisitorTestBase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->cmsItemLoaderMock
            ->method('load')
            ->willReturn(CmsItem::fromArray(['remoteId' => 'abc']));
    }

    public function getVisitor(): VisitorInterface
    {
        return new ItemVisitor();
    }

    public static function acceptDataProvider(): iterable
    {
        return [
            [new Item(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public static function visitDataProvider(): iterable
    {
        return [
            [fn (): Item => $this->collectionService->loadItem(Uuid::fromString('79b6f162-d801-57e0-8b2d-a4b568a74231')), 'item/item_4.json'],
            [fn (): Item => $this->collectionService->loadItem(Uuid::fromString('966e55da-9671-581a-b3b4-84363f7db33d')), 'item/item_5.json'],
        ];
    }
}

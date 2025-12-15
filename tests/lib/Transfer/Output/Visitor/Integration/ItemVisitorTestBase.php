<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Transfer\Output\Visitor\ItemVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @extends \Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\VisitorTestBase<\Netgen\Layouts\API\Values\Collection\Item>
 */
abstract class ItemVisitorTestBase extends VisitorTestBase
{
    final protected function setUp(): void
    {
        parent::setUp();

        $this->cmsItemLoaderStub
            ->method('load')
            ->willReturn(CmsItem::fromArray(['remoteId' => 'abc']));
    }

    final public function getVisitor(): VisitorInterface
    {
        return new ItemVisitor();
    }

    final public static function acceptDataProvider(): iterable
    {
        return [
            [new Item(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    final public static function visitDataProvider(): iterable
    {
        return [
            ['item/item_4.json', '79b6f162-d801-57e0-8b2d-a4b568a74231'],
            ['item/item_5.json', '966e55da-9671-581a-b3b4-84363f7db33d'],
        ];
    }

    final protected function loadValue(string $id, string ...$additionalParameters): Item
    {
        return $this->collectionService->loadItem(Uuid::fromString($id));
    }
}

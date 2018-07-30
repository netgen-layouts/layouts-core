<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Netgen\BlockManager\API\Values\Collection\Item as APIItem;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\Transfer\Output\Visitor\ItemVisitor;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

abstract class ItemVisitorTest extends VisitorTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->cmsItemLoaderMock
            ->expects($this->any())
            ->method('load')
            ->will($this->returnValue(CmsItem::fromArray(['remoteId' => 'abc'])));

        $this->collectionService = $this->createCollectionService();
    }

    /**
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Implementation requires sub-visitor
     */
    public function testVisitThrowsRuntimeExceptionWithoutSubVisitor(): void
    {
        $this->getVisitor()->visit(new Item());
    }

    public function getVisitor(): VisitorInterface
    {
        return new ItemVisitor();
    }

    public function acceptProvider(): array
    {
        return [
            [new Item(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public function visitProvider(): array
    {
        return [
            [function (): APIItem { return $this->collectionService->loadItem(4); }, 'item/item_4.json'],
            [function (): APIItem { return $this->collectionService->loadItem(5); }, 'item/item_5.json'],
        ];
    }
}
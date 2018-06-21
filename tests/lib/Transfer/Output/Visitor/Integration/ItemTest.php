<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Netgen\BlockManager\API\Values\Collection\Item as APIItem;
use Netgen\BlockManager\Collection\Item\ItemDefinition;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Collection\Item as ItemValue;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\Transfer\Output\Visitor\Item;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

abstract class ItemTest extends VisitorTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->cmsItemLoaderMock
            ->expects($this->any())
            ->method('load')
            ->will($this->returnValue(new CmsItem(['remoteId' => 'abc'])));

        $this->collectionService = $this->createCollectionService();
    }

    /**
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Implementation requires sub-visitor
     */
    public function testVisitThrowsRuntimeExceptionWithoutSubVisitor(): void
    {
        $this->getVisitor()->visit(new ItemValue());
    }

    /**
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Unknown type '9999'
     */
    public function testVisitThrowsRuntimeExceptionWithInvalidItemType(): void
    {
        $this->getVisitor()->visit(
            new ItemValue(
                [
                    'type' => 9999,
                    'position' => 42,
                    'definition' => new ItemDefinition(),
                    'cmsItem' => new CmsItem(),
                ]
            ),
            $this->subVisitorMock
        );
    }

    public function testVisitWithOverrideItem(): void
    {
        // Implemented manually since we don't have override items in the fixtures
        $visitedData = $this->getVisitor()->visit(
            new ItemValue(
                [
                    'type' => ItemValue::TYPE_OVERRIDE,
                    'position' => 42,
                    'definition' => new ItemDefinition(['valueType' => 'value_type']),
                    'cmsItem' => new CmsItem(),
                ]
            ),
            $this->subVisitorMock
        );

        $this->assertInternalType('array', $visitedData);
        $this->assertArrayHasKey('type', $visitedData);
        $this->assertSame('OVERRIDE', $visitedData['type']);
    }

    public function getVisitor(): VisitorInterface
    {
        return new Item();
    }

    public function acceptProvider(): array
    {
        return [
            [new ItemValue(), true],
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

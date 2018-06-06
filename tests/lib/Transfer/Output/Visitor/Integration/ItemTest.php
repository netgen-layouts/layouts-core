<?php

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Netgen\BlockManager\Collection\Item\ItemDefinition;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Collection\Item as ItemValue;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Item\Item as CmsItem;
use Netgen\BlockManager\Transfer\Output\Visitor\Item;

abstract class ItemTest extends VisitorTest
{
    public function setUp()
    {
        parent::setUp();

        $this->itemLoaderMock
            ->expects($this->any())
            ->method('load')
            ->will($this->returnValue(new CmsItem(['remoteId' => 'abc'])));

        $this->collectionService = $this->createCollectionService();
    }

    /**
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Implementation requires sub-visitor
     */
    public function testVisitThrowsRuntimeExceptionWithoutSubVisitor()
    {
        $this->getVisitor()->visit(new ItemValue());
    }

    /**
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Unknown type '9999'
     */
    public function testVisitThrowsRuntimeExceptionWithInvalidItemType()
    {
        $this->getVisitor()->visit(
            new ItemValue(
                [
                    'type' => 9999,
                    'definition' => new ItemDefinition(),
                    'cmsItem' => new CmsItem(),
                ]
            ),
            $this->subVisitorMock
        );
    }

    public function testVisitWithOverrideItem()
    {
        // Implemented manually since we don't have override items in the fixtures
        $visitedData = $this->getVisitor()->visit(
            new ItemValue(
                [
                    'type' => ItemValue::TYPE_OVERRIDE,
                    'definition' => new ItemDefinition(),
                    'cmsItem' => new CmsItem(),
                ]
            ),
            $this->subVisitorMock
        );

        $this->assertInternalType('array', $visitedData);
        $this->assertArrayHasKey('type', $visitedData);
        $this->assertEquals('OVERRIDE', $visitedData['type']);
    }

    public function getVisitor()
    {
        return new Item();
    }

    public function acceptProvider()
    {
        return [
            [new ItemValue(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public function visitProvider()
    {
        return [
            [function () { return $this->collectionService->loadItem(4); }, 'item/item_4.json'],
            [function () { return $this->collectionService->loadItem(5); }, 'item/item_5.json'],
        ];
    }
}

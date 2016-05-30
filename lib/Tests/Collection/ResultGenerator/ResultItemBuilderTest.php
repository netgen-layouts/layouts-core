<?php

namespace Netgen\BlockManager\Tests\Collection\ResultGenerator;

use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Collection\ResultGenerator\ResultItemBuilder;
use Netgen\BlockManager\Collection\ResultItem;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Core\Values\Collection\Item as CollectionItem;
use Netgen\BlockManager\Tests\Item\Stubs\Value;

class ResultItemBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemBuilderMock;

    /**
     * @var \Netgen\BlockManager\Collection\ResultGenerator\ResultItemBuilder
     */
    protected $builder;

    public function setUp()
    {
        $this->itemBuilderMock = $this->getMock(ItemBuilderInterface::class);

        $this->builder = new ResultItemBuilder($this->itemBuilderMock);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\ResultItemBuilder::__construct
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\ResultItemBuilder::build
     */
    public function testBuild()
    {
        $value = new Value(42);

        $this->itemBuilderMock
            ->expects($this->once())
            ->method('buildFromObject')
            ->with($this->equalTo(new Value(42)))
            ->will($this->returnValue(new Item()));

        $resultItem = new ResultItem(
            array(
                'item' => new Item(),
                'collectionItem' => null,
                'type' => ResultItem::TYPE_DYNAMIC,
                'position' => 5,
            )
        );

        self::assertEquals($resultItem, $this->builder->build($value, 5));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\ResultItemBuilder::__construct
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\ResultItemBuilder::buildFromItem
     */
    public function testBuildFromItem()
    {
        $item = new CollectionItem(
            array(
                'type' => CollectionItem::TYPE_MANUAL,
                'valueId' => 42,
                'valueType' => 'value',
            )
        );

        $this->itemBuilderMock
            ->expects($this->once())
            ->method('build')
            ->with($this->equalTo(42), $this->equalTo('value'))
            ->will($this->returnValue(new Item()));

        $resultItem = new ResultItem(
            array(
                'item' => new Item(),
                'collectionItem' => $item,
                'type' => ResultItem::TYPE_MANUAL,
                'position' => 5,
            )
        );

        self::assertEquals($resultItem, $this->builder->buildFromItem($item, 5));
    }
}

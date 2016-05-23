<?php

namespace Netgen\BlockManager\Tests\Collection\ResultGenerator;

use Netgen\BlockManager\Value\ValueBuilderInterface;
use Netgen\BlockManager\Collection\ResultGenerator\ResultItemBuilder;
use Netgen\BlockManager\Collection\ResultItem;
use Netgen\BlockManager\Value\Value;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Tests\Value\Stubs\ExternalValue;

class ResultItemBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $valueBuilderMock;

    /**
     * @var \Netgen\BlockManager\Collection\ResultGenerator\ResultItemBuilder
     */
    protected $builder;

    public function setUp()
    {
        $this->valueBuilderMock = $this->getMock(ValueBuilderInterface::class);

        $this->builder = new ResultItemBuilder($this->valueBuilderMock);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\ResultItemBuilder::__construct
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\ResultItemBuilder::build
     */
    public function testBuild()
    {
        $value = new ExternalValue(42);

        $this->valueBuilderMock
            ->expects($this->once())
            ->method('buildFromObject')
            ->with($this->equalTo(new ExternalValue(42)))
            ->will($this->returnValue(new Value()));

        $resultItem = new ResultItem(
            array(
                'value' => new Value(),
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
        $item = new Item(
            array(
                'type' => Item::TYPE_MANUAL,
                'valueId' => 42,
                'valueType' => 'value',
            )
        );

        $this->valueBuilderMock
            ->expects($this->once())
            ->method('build')
            ->with($this->equalTo(42), $this->equalTo('value'))
            ->will($this->returnValue(new Value()));

        $resultItem = new ResultItem(
            array(
                'value' => new Value(),
                'collectionItem' => $item,
                'type' => ResultItem::TYPE_MANUAL,
                'position' => 5,
            )
        );

        self::assertEquals($resultItem, $this->builder->buildFromItem($item, 5));
    }
}

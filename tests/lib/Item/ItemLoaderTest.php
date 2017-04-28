<?php

namespace Netgen\BlockManager\Tests\Item;

use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Item\ItemLoader;
use Netgen\BlockManager\Tests\Item\Stubs\Value;
use Netgen\BlockManager\Tests\Item\Stubs\ValueLoader;
use PHPUnit\Framework\TestCase;

class ItemLoaderTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemBuilderMock;

    /**
     * @var \Netgen\BlockManager\Item\ItemLoaderInterface
     */
    protected $itemLoader;

    public function setUp()
    {
        $this->itemBuilderMock = $this->createMock(ItemBuilderInterface::class);
    }

    /**
     * @covers \Netgen\BlockManager\Item\ItemLoader::__construct
     * @covers \Netgen\BlockManager\Item\ItemLoader::load
     */
    public function testLoad()
    {
        $item = new Item(
            array(
                'valueId' => 42,
                'name' => 'Some value',
                'valueType' => 'value',
                'isVisible' => true,
                'object' => new Value(42),
            )
        );

        $this->itemLoader = new ItemLoader(
            $this->itemBuilderMock,
            array('value' => new ValueLoader())
        );

        $this->itemBuilderMock
            ->expects($this->any())
            ->method('build')
            ->will($this->returnValue($item));

        $this->assertEquals($item, $this->itemLoader->load(42, 'value'));
    }

    /**
     * @covers \Netgen\BlockManager\Item\ItemLoader::load
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Value type "value" does not exist.
     */
    public function testLoadItemThrowsItemException()
    {
        $this->itemLoader = new ItemLoader($this->itemBuilderMock);

        $this->itemLoader->load(42, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Item\ItemLoader::load
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Value with ID 42 does not exist.
     */
    public function testLoadItemThrowsItemExceptionWithNoItem()
    {
        $this->itemLoader = new ItemLoader(
            $this->itemBuilderMock,
            array('value' => new ValueLoader(true))
        );

        $this->itemLoader->load(42, 'value');
    }
}

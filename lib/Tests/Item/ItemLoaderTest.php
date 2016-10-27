<?php

namespace Netgen\BlockManager\Tests\Item;

use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Item\ItemLoader;
use Netgen\BlockManager\Item\Registry\ValueLoaderRegistry;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Tests\Item\Stubs\Value;
use Netgen\BlockManager\Tests\Item\Stubs\ValueLoader;
use PHPUnit\Framework\TestCase;

class ItemLoaderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\Registry\ValueLoaderRegistryInterface
     */
    protected $valueLoaderRegistry;

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
        $this->valueLoaderRegistry = new ValueLoaderRegistry();
        $this->itemBuilderMock = $this->createMock(ItemBuilderInterface::class);

        $this->itemLoader = new ItemLoader(
            $this->valueLoaderRegistry,
            $this->itemBuilderMock
        );
    }

    /**
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

        $this->valueLoaderRegistry->addValueLoader(new ValueLoader());

        $this->itemBuilderMock
            ->expects($this->any())
            ->method('build')
            ->will($this->returnValue($item));

        $this->assertEquals($item, $this->itemLoader->load(42, 'value'));
    }

    /**
     * @covers \Netgen\BlockManager\Item\ItemLoader::load
     * @expectedException \Netgen\BlockManager\Exception\InvalidItemException
     */
    public function testLoadItemThrowsInvalidItemException()
    {
        $this->itemLoader->load(42, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Item\ItemLoader::load
     * @expectedException \Netgen\BlockManager\Exception\InvalidItemException
     */
    public function testLoadItemThrowsInvalidItemExceptionWithNoItem()
    {
        $this->valueLoaderRegistry->addValueLoader(new ValueLoader(true));

        $this->itemLoader->load(42, 'value');
    }
}

<?php

namespace Netgen\BlockManager\Tests\Item;

use Netgen\BlockManager\Item\Registry\ValueLoaderRegistryInterface;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\ItemBuilder;
use Netgen\BlockManager\Tests\Item\Stubs\UnsupportedValueConverter;
use Netgen\BlockManager\Tests\Item\Stubs\Value;
use Netgen\BlockManager\Tests\Item\Stubs\ValueConverter;
use Netgen\BlockManager\Tests\Item\Stubs\ValueLoader;
use stdClass;

class ItemBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemLoaderRegistryMock;

    public function setUp()
    {
        $this->valueLoaderRegistryMock = $this->getMock(ValueLoaderRegistryInterface::class);
    }

    /**
     * @covers \Netgen\BlockManager\Item\ItemBuilder::__construct
     * @covers \Netgen\BlockManager\Item\ItemBuilder::buildFromObject
     */
    public function testBuildFromObject()
    {
        $value = new Value(42);

        $item = new Item(
            array(
                'valueId' => 42,
                'name' => 'Some value',
                'valueType' => 'value',
                'isVisible' => true,
                'object' => $value,
            )
        );

        $builder = new ItemBuilder(
            $this->valueLoaderRegistryMock,
            array(new ValueConverter())
        );

        self::assertEquals(
            $item,
            $builder->buildFromObject($value)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Item\ItemBuilder::__construct
     * @covers \Netgen\BlockManager\Item\ItemBuilder::build
     */
    public function testBuild()
    {
        $this->valueLoaderRegistryMock
            ->expects($this->any())
            ->method('getValueLoader')
            ->will($this->returnValue(new ValueLoader()));

        $item = new Item(
            array(
                'valueId' => 42,
                'name' => 'Some value',
                'valueType' => 'value',
                'isVisible' => true,
                'object' => new Value(42),
            )
        );

        $builder = new ItemBuilder(
            $this->valueLoaderRegistryMock,
            array(new ValueConverter())
        );

        self::assertEquals(
            $item,
            $builder->build(42, 'value')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Item\ItemBuilder::build
     * @expectedException \RuntimeException
     */
    public function testBuildThrowsRuntimeException()
    {
        $this->valueLoaderRegistryMock
            ->expects($this->any())
            ->method('getValueLoader')
            ->will($this->returnValue(new ValueLoader(true)));

        $builder = new ItemBuilder(
            $this->valueLoaderRegistryMock,
            array(new ValueConverter())
        );

        $builder->build(42, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Item\ItemBuilder::buildFromObject
     * @expectedException \RuntimeException
     */
    public function testBuildFromObjectThrowsRuntimeExceptionWithUnsupportedValueConverter()
    {
        $builder = new ItemBuilder(
            $this->valueLoaderRegistryMock,
            array(new UnsupportedValueConverter())
        );

        $builder->buildFromObject(new Value(42));
    }

    /**
     * @covers \Netgen\BlockManager\Item\ItemBuilder::buildFromObject
     * @expectedException \RuntimeException
     */
    public function testBuildFromObjectThrowsRuntimeExceptionWithNoValueConverters()
    {
        $builder = new ItemBuilder($this->valueLoaderRegistryMock);

        $builder->buildFromObject(new Value(42));
    }

    /**
     * @covers \Netgen\BlockManager\Item\ItemBuilder::buildFromObject
     * @expectedException \RuntimeException
     */
    public function testBuildFromObjectThrowsRuntimeExceptionWithWrongInterface()
    {
        $builder = new ItemBuilder(
            $this->valueLoaderRegistryMock,
            array(new stdClass())
        );

        $builder->buildFromObject(new Value(42));
    }
}

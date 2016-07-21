<?php

namespace Netgen\BlockManager\Tests\Item;

use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Item\NullValue;
use Netgen\BlockManager\Item\Registry\ValueLoaderRegistryInterface;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\ItemBuilder;
use Netgen\BlockManager\Item\ValueConverter\NullValueConverter;
use Netgen\BlockManager\Tests\Item\Stubs\UnsupportedValueConverter;
use Netgen\BlockManager\Tests\Item\Stubs\Value;
use Netgen\BlockManager\Tests\Item\Stubs\ValueConverter;
use Netgen\BlockManager\Tests\Item\Stubs\ValueLoader;
use stdClass;
use PHPUnit\Framework\TestCase;

class ItemBuilderTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $valueLoaderRegistryMock;

    public function setUp()
    {
        $this->valueLoaderRegistryMock = $this->createMock(ValueLoaderRegistryInterface::class);
    }

    /**
     * @covers \Netgen\BlockManager\Item\ItemBuilder::__construct
     * @expectedException \RuntimeException
     */
    public function testConstructorThrowsRuntimeExceptionWithNoValueConverters()
    {
        $builder = new ItemBuilder($this->valueLoaderRegistryMock);
    }

    /**
     * @covers \Netgen\BlockManager\Item\ItemBuilder::__construct
     * @expectedException \RuntimeException
     */
    public function testConstructorThrowsRuntimeExceptionWithWrongInterface()
    {
        $builder = new ItemBuilder(
            $this->valueLoaderRegistryMock,
            array(new stdClass())
        );
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

        $this->assertEquals(
            $item,
            $builder->buildFromObject($value)
        );
    }

    /**
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

        $this->assertEquals(
            $item,
            $builder->build(42, 'value')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Item\ItemBuilder::build
     */
    public function testBuildInvalidItem()
    {
        $this->valueLoaderRegistryMock
            ->expects($this->any())
            ->method('getValueLoader')
            ->will($this->returnValue(new ValueLoader(true)));

        $builder = new ItemBuilder(
            $this->valueLoaderRegistryMock,
            array(new ValueConverter(), new NullValueConverter())
        );

        $item = $builder->build(42, 'value');
        $this->assertInstanceOf(NullValue::class, $item->getObject());
    }

    /**
     * @covers \Netgen\BlockManager\Item\ItemBuilder::build
     */
    public function testBuildInvalidItemWithNoValueLoader()
    {
        $this->valueLoaderRegistryMock
            ->expects($this->any())
            ->method('getValueLoader')
            ->will($this->throwException(
                new InvalidArgumentException('item', 'not found'))
            );

        $builder = new ItemBuilder(
            $this->valueLoaderRegistryMock,
            array(new ValueConverter(), new NullValueConverter())
        );

        $item = $builder->build(42, 'value');
        $this->assertInstanceOf(NullValue::class, $item->getObject());
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
}

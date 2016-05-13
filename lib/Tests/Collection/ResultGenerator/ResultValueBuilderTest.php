<?php

namespace Netgen\BlockManager\Tests\Collection\ResultGenerator;

use Netgen\BlockManager\Collection\Registry\ValueLoaderRegistryInterface;
use Netgen\BlockManager\Collection\ResultValue;
use Netgen\BlockManager\Collection\ResultGenerator\ResultValueBuilder;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Tests\Collection\Stubs\UnsupportedValueConverter;
use Netgen\BlockManager\Tests\Collection\Stubs\Value;
use Netgen\BlockManager\Tests\Collection\Stubs\ValueConverter;
use Netgen\BlockManager\Tests\Collection\Stubs\ValueLoader;
use stdClass;

class ResultValueBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $valueLoaderRegistryMock;

    public function setUp()
    {
        $this->valueLoaderRegistryMock = $this->getMock(ValueLoaderRegistryInterface::class);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\ResultValueBuilder::__construct
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\ResultValueBuilder::build
     */
    public function testBuild()
    {
        $value = new Value(42);

        $resultValue = new ResultValue();
        $resultValue->id = 42;
        $resultValue->name = 'Some value';
        $resultValue->type = 'value';
        $resultValue->isVisible = true;
        $resultValue->object = $value;

        $builder = new ResultValueBuilder(
            $this->valueLoaderRegistryMock,
            array(new ValueConverter())
        );

        self::assertEquals(
            $resultValue,
            $builder->build($value)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\ResultValueBuilder::__construct
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\ResultValueBuilder::buildFromItem
     */
    public function testBuildFromItem()
    {
        $this->valueLoaderRegistryMock
            ->expects($this->any())
            ->method('getValueLoader')
            ->will($this->returnValue(new ValueLoader()));

        $resultValue = new ResultValue();
        $resultValue->id = 42;
        $resultValue->name = 'Some value';
        $resultValue->type = 'value';
        $resultValue->isVisible = true;
        $resultValue->object = new Value(42);

        $builder = new ResultValueBuilder(
            $this->valueLoaderRegistryMock,
            array(new ValueConverter())
        );

        self::assertEquals(
            $resultValue,
            $builder->buildFromItem(new Item(array('valueId' => 42, 'valueType' => 'value')))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\ResultValueBuilder::buildFromItem
     * @expectedException \RuntimeException
     */
    public function testBuildFromItemThrowsRuntimeException()
    {
        $this->valueLoaderRegistryMock
            ->expects($this->any())
            ->method('getValueLoader')
            ->will($this->returnValue(new ValueLoader(true)));

        $builder = new ResultValueBuilder(
            $this->valueLoaderRegistryMock,
            array(new ValueConverter())
        );

        $builder->buildFromItem(new Item(array('valueId' => 42, 'valueType' => 'value')));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\ResultValueBuilder::build
     * @expectedException \RuntimeException
     */
    public function testBuildThrowsRuntimeExceptionWithUnsupportedValueConverter()
    {
        $builder = new ResultValueBuilder(
            $this->valueLoaderRegistryMock,
            array(new UnsupportedValueConverter())
        );

        $builder->build(new Value(42));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\ResultValueBuilder::build
     * @expectedException \RuntimeException
     */
    public function testBuildThrowsRuntimeExceptionWithNoValueConverters()
    {
        $builder = new ResultValueBuilder($this->valueLoaderRegistryMock);

        $builder->build(new Value(42));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\ResultValueBuilder::build
     * @expectedException \RuntimeException
     */
    public function testBuildThrowsRuntimeExceptionWithWrongInterface()
    {
        $builder = new ResultValueBuilder(
            $this->valueLoaderRegistryMock,
            array(new stdClass())
        );

        $builder->build(new Value(42));
    }
}

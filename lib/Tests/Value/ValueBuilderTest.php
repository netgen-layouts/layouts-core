<?php

namespace Netgen\BlockManager\Tests\Value;

use Netgen\BlockManager\Value\Registry\ValueLoaderRegistryInterface;
use Netgen\BlockManager\Value\Value;
use Netgen\BlockManager\Value\ValueBuilder;
use Netgen\BlockManager\Tests\Value\Stubs\UnsupportedValueConverter;
use Netgen\BlockManager\Tests\Value\Stubs\ExternalValue;
use Netgen\BlockManager\Tests\Value\Stubs\ValueConverter;
use Netgen\BlockManager\Tests\Value\Stubs\ValueLoader;
use stdClass;

class ValueBuilderTest extends \PHPUnit_Framework_TestCase
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
     * @covers \Netgen\BlockManager\Value\ValueBuilder::__construct
     * @covers \Netgen\BlockManager\Value\ValueBuilder::buildFromObject
     */
    public function testBuildFromObject()
    {
        $externalValue = new ExternalValue(42);

        $value = new Value(
            array(
                'valueId' => 42,
                'name' => 'Some value',
                'valueType' => 'value',
                'isVisible' => true,
                'object' => $externalValue,
            )
        );

        $builder = new ValueBuilder(
            $this->valueLoaderRegistryMock,
            array(new ValueConverter())
        );

        self::assertEquals(
            $value,
            $builder->buildFromObject($externalValue)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Value\ValueBuilder::__construct
     * @covers \Netgen\BlockManager\Value\ValueBuilder::build
     */
    public function testBuild()
    {
        $this->valueLoaderRegistryMock
            ->expects($this->any())
            ->method('getValueLoader')
            ->will($this->returnValue(new ValueLoader()));

        $value = new Value(
            array(
                'valueId' => 42,
                'name' => 'Some value',
                'valueType' => 'value',
                'isVisible' => true,
                'object' => new ExternalValue(42),
            )
        );

        $builder = new ValueBuilder(
            $this->valueLoaderRegistryMock,
            array(new ValueConverter())
        );

        self::assertEquals(
            $value,
            $builder->build(42, 'value')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Value\ValueBuilder::build
     * @expectedException \RuntimeException
     */
    public function testBuildThrowsRuntimeException()
    {
        $this->valueLoaderRegistryMock
            ->expects($this->any())
            ->method('getValueLoader')
            ->will($this->returnValue(new ValueLoader(true)));

        $builder = new ValueBuilder(
            $this->valueLoaderRegistryMock,
            array(new ValueConverter())
        );

        $builder->build(42, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Value\ValueBuilder::buildFromObject
     * @expectedException \RuntimeException
     */
    public function testBuildFromObjectThrowsRuntimeExceptionWithUnsupportedValueConverter()
    {
        $builder = new ValueBuilder(
            $this->valueLoaderRegistryMock,
            array(new UnsupportedValueConverter())
        );

        $builder->buildFromObject(new ExternalValue(42));
    }

    /**
     * @covers \Netgen\BlockManager\Value\ValueBuilder::buildFromObject
     * @expectedException \RuntimeException
     */
    public function testBuildFromObjectThrowsRuntimeExceptionWithNoValueConverters()
    {
        $builder = new ValueBuilder($this->valueLoaderRegistryMock);

        $builder->buildFromObject(new ExternalValue(42));
    }

    /**
     * @covers \Netgen\BlockManager\Value\ValueBuilder::buildFromObject
     * @expectedException \RuntimeException
     */
    public function testBuildFromObjectThrowsRuntimeExceptionWithWrongInterface()
    {
        $builder = new ValueBuilder(
            $this->valueLoaderRegistryMock,
            array(new stdClass())
        );

        $builder->buildFromObject(new ExternalValue(42));
    }
}

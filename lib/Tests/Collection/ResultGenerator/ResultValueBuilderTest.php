<?php

namespace Netgen\BlockManager\Tests\Collection\ResultGenerator;

use Netgen\BlockManager\Collection\ResultValue;
use Netgen\BlockManager\Collection\ResultGenerator\ResultValueBuilder;
use Netgen\BlockManager\Tests\Collection\Stubs\UnsupportedValueConverter;
use Netgen\BlockManager\Tests\Collection\Stubs\Value;
use Netgen\BlockManager\Tests\Collection\Stubs\ValueConverter;
use stdClass;

class ResultValueBuilderTest extends \PHPUnit_Framework_TestCase
{
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

        $builder = new ResultValueBuilder(array(new ValueConverter()));

        self::assertEquals(
            $resultValue,
            $builder->build($value)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\ResultValueBuilder::build
     * @expectedException \RuntimeException
     */
    public function testBuildThrowsRuntimeExceptionWithUnsupportedValueConverter()
    {
        $builder = new ResultValueBuilder(array(new UnsupportedValueConverter()));

        $builder->build(new Value(42));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\ResultValueBuilder::build
     * @expectedException \RuntimeException
     */
    public function testBuildThrowsRuntimeExceptionWithNoValueConverters()
    {
        $builder = new ResultValueBuilder();

        $builder->build(new Value(42));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\ResultValueBuilder::build
     * @expectedException \RuntimeException
     */
    public function testBuildThrowsRuntimeExceptionWithWrongInterface()
    {
        $builder = new ResultValueBuilder(array(new stdClass()));

        $builder->build(new Value(42));
    }
}

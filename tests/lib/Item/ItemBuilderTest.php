<?php

namespace Netgen\BlockManager\Tests\Item;

use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\ItemBuilder;
use Netgen\BlockManager\Tests\Item\Stubs\UnsupportedValueConverter;
use Netgen\BlockManager\Tests\Item\Stubs\Value;
use Netgen\BlockManager\Tests\Item\Stubs\ValueConverter;
use PHPUnit\Framework\TestCase;
use stdClass;

class ItemBuilderTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Item\ItemBuilder::__construct
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Value converter "stdClass" needs to implement ValueConverterInterface.
     */
    public function testConstructorThrowsRuntimeExceptionWithWrongInterface()
    {
        new ItemBuilder(array(new stdClass()));
    }

    /**
     * @covers \Netgen\BlockManager\Item\ItemBuilder::__construct
     * @covers \Netgen\BlockManager\Item\ItemBuilder::build
     */
    public function testBuild()
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

        $builder = new ItemBuilder(array(new ValueConverter()));

        $this->assertEquals($item, $builder->build($value));
    }

    /**
     * @covers \Netgen\BlockManager\Item\ItemBuilder::build
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Value converter for "Netgen\BlockManager\Tests\Item\Stubs\Value" type does not exist.
     */
    public function testBuildThrowsRuntimeException()
    {
        $builder = new ItemBuilder(array(new UnsupportedValueConverter()));

        $builder->build(new Value(42));
    }
}

<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Item;

use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\ItemBuilder;
use Netgen\BlockManager\Tests\Item\Stubs\UnsupportedValueConverter;
use Netgen\BlockManager\Tests\Item\Stubs\Value;
use Netgen\BlockManager\Tests\Item\Stubs\ValueConverter;
use PHPUnit\Framework\TestCase;

final class ItemBuilderTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Item\ItemBuilder::__construct
     * @covers \Netgen\BlockManager\Item\ItemBuilder::build
     */
    public function testBuild()
    {
        $value = new Value(42, 'abc');

        $item = new Item(
            [
                'value' => 42,
                'remoteId' => 'abc',
                'name' => 'Some value',
                'valueType' => 'value',
                'isVisible' => true,
                'object' => $value,
            ]
        );

        $builder = new ItemBuilder([new ValueConverter()]);

        $this->assertEquals($item, $builder->build($value));
    }

    /**
     * @covers \Netgen\BlockManager\Item\ItemBuilder::build
     * @expectedException \Netgen\BlockManager\Exception\Item\ValueException
     * @expectedExceptionMessage Value converter for "Netgen\BlockManager\Tests\Item\Stubs\Value" type does not exist.
     */
    public function testBuildThrowsValueException()
    {
        $builder = new ItemBuilder([new UnsupportedValueConverter()]);

        $builder->build(new Value(42, 'abc'));
    }
}

<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Item;

use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\ItemBuilder;
use Netgen\BlockManager\Tests\Item\Stubs\UnsupportedValueConverter;
use Netgen\BlockManager\Tests\Item\Stubs\Value;
use Netgen\BlockManager\Tests\Item\Stubs\ValueConverter;
use Netgen\BlockManager\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\TestCase;

final class ItemBuilderTest extends TestCase
{
    use ExportObjectTrait;

    /**
     * @covers \Netgen\BlockManager\Item\ItemBuilder::__construct
     * @covers \Netgen\BlockManager\Item\ItemBuilder::build
     */
    public function testBuild(): void
    {
        $value = new Value(42, 'abc');

        $builder = new ItemBuilder([new ValueConverter()]);

        $builtItem = $builder->build($value);

        $this->assertInstanceOf(Item::class, $builtItem);

        $this->assertSame(
            [
                'value' => 42,
                'remoteId' => 'abc',
                'valueType' => 'value',
                'name' => 'Some value',
                'isVisible' => true,
                'object' => $value,
            ],
            $this->exportObject($builtItem)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Item\ItemBuilder::build
     * @expectedException \Netgen\BlockManager\Exception\Item\ValueException
     * @expectedExceptionMessage Value converter for "Netgen\BlockManager\Tests\Item\Stubs\Value" type does not exist.
     */
    public function testBuildThrowsValueException(): void
    {
        $builder = new ItemBuilder([new UnsupportedValueConverter()]);

        $builder->build(new Value(42, 'abc'));
    }
}

<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Item;

use Netgen\BlockManager\Item\CmsItemBuilder;
use Netgen\BlockManager\Item\CmsItemInterface;
use Netgen\BlockManager\Tests\Item\Stubs\UnsupportedValueConverter;
use Netgen\BlockManager\Tests\Item\Stubs\Value;
use Netgen\BlockManager\Tests\Item\Stubs\ValueConverter;
use Netgen\BlockManager\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\TestCase;

final class CmsItemBuilderTest extends TestCase
{
    use ExportObjectTrait;

    /**
     * @covers \Netgen\BlockManager\Item\CmsItemBuilder::__construct
     * @covers \Netgen\BlockManager\Item\CmsItemBuilder::build
     */
    public function testBuild(): void
    {
        $value = new Value(42, 'abc');

        $builder = new CmsItemBuilder([new ValueConverter()]);

        $builtItem = $builder->build($value);

        $this->assertInstanceOf(CmsItemInterface::class, $builtItem);

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
     * @covers \Netgen\BlockManager\Item\CmsItemBuilder::build
     * @expectedException \Netgen\BlockManager\Exception\Item\ValueException
     * @expectedExceptionMessage Value converter for "Netgen\BlockManager\Tests\Item\Stubs\Value" type does not exist.
     */
    public function testBuildThrowsValueException(): void
    {
        $builder = new CmsItemBuilder([new UnsupportedValueConverter()]);

        $builder->build(new Value(42, 'abc'));
    }
}

<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Item;

use Netgen\Layouts\Exception\Item\ValueException;
use Netgen\Layouts\Item\CmsItemBuilder;
use Netgen\Layouts\Tests\Item\Stubs\UnsupportedValueConverter;
use Netgen\Layouts\Tests\Item\Stubs\Value;
use Netgen\Layouts\Tests\Item\Stubs\ValueConverter;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\TestCase;

final class CmsItemBuilderTest extends TestCase
{
    use ExportObjectTrait;

    /**
     * @covers \Netgen\Layouts\Item\CmsItemBuilder::__construct
     * @covers \Netgen\Layouts\Item\CmsItemBuilder::build
     */
    public function testBuild(): void
    {
        $value = new Value(42, 'abc');

        $builder = new CmsItemBuilder([new UnsupportedValueConverter(), new ValueConverter()]);

        $builtItem = $builder->build($value);

        self::assertSame(
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
     * @covers \Netgen\Layouts\Item\CmsItemBuilder::build
     */
    public function testBuildThrowsValueException(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Value converter for "Netgen\\Layouts\\Tests\\Item\\Stubs\\Value" type does not exist.');

        $builder = new CmsItemBuilder([new UnsupportedValueConverter()]);

        $builder->build(new Value(42, 'abc'));
    }
}

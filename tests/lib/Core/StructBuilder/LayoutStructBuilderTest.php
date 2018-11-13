<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\StructBuilder;

use Netgen\BlockManager\Core\StructBuilder\LayoutStructBuilder;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Tests\Core\CoreTestCase;
use Netgen\BlockManager\Tests\TestCase\ExportObjectTrait;

abstract class LayoutStructBuilderTest extends CoreTestCase
{
    use ExportObjectTrait;

    /**
     * @var \Netgen\BlockManager\Core\StructBuilder\LayoutStructBuilder
     */
    private $structBuilder;

    public function setUp(): void
    {
        parent::setUp();

        $this->structBuilder = new LayoutStructBuilder();
    }

    /**
     * @covers \Netgen\BlockManager\Core\StructBuilder\LayoutStructBuilder::newLayoutCreateStruct
     */
    public function testNewLayoutCreateStruct(): void
    {
        $layoutType = LayoutType::fromArray(['identifier' => '4_zones_a']);

        $struct = $this->structBuilder->newLayoutCreateStruct(
            $layoutType,
            'New layout',
            'en'
        );

        self::assertSame(
            [
                'layoutType' => $layoutType,
                'name' => 'New layout',
                'description' => null,
                'shared' => false,
                'mainLocale' => 'en',
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\StructBuilder\LayoutStructBuilder::newLayoutUpdateStruct
     */
    public function testNewLayoutUpdateStruct(): void
    {
        $struct = $this->structBuilder->newLayoutUpdateStruct(
            $this->layoutService->loadLayoutDraft(1)
        );

        self::assertSame(
            [
                'name' => 'My layout',
                'description' => 'My layout description',
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\StructBuilder\LayoutStructBuilder::newLayoutUpdateStruct
     */
    public function testNewLayoutUpdateStructWithNoLayout(): void
    {
        $struct = $this->structBuilder->newLayoutUpdateStruct();

        self::assertSame(
            [
                'name' => null,
                'description' => null,
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\StructBuilder\LayoutStructBuilder::newLayoutCopyStruct
     */
    public function testNewLayoutCopyStruct(): void
    {
        $struct = $this->structBuilder->newLayoutCopyStruct(
            $this->layoutService->loadLayoutDraft(1)
        );

        self::assertSame(
            [
                'name' => 'My layout (copy)',
                'description' => null,
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\StructBuilder\LayoutStructBuilder::newLayoutCopyStruct
     */
    public function testNewLayoutCopyStructWithNoLayout(): void
    {
        $struct = $this->structBuilder->newLayoutCopyStruct();

        self::assertSame(
            [
                'name' => null,
                'description' => null,
            ],
            $this->exportObject($struct)
        );
    }
}

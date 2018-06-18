<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service\StructBuilder;

use Netgen\BlockManager\API\Values\Layout\LayoutCopyStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct;
use Netgen\BlockManager\Core\Service\StructBuilder\LayoutStructBuilder;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;
use Netgen\BlockManager\Tests\TestCase\ExportObjectVarsTrait;

abstract class LayoutStructBuilderTest extends ServiceTestCase
{
    use ExportObjectVarsTrait;

    /**
     * @var \Netgen\BlockManager\Core\Service\StructBuilder\LayoutStructBuilder
     */
    private $structBuilder;

    public function setUp(): void
    {
        parent::setUp();

        $this->layoutService = $this->createLayoutService();

        $this->structBuilder = new LayoutStructBuilder();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\LayoutStructBuilder::newLayoutCreateStruct
     */
    public function testNewLayoutCreateStruct(): void
    {
        $layoutType = new LayoutType(['identifier' => '4_zones_a']);

        $struct = $this->structBuilder->newLayoutCreateStruct(
            $layoutType,
            'New layout',
            'en'
        );

        $this->assertInstanceOf(LayoutCreateStruct::class, $struct);

        $this->assertSame(
            [
                'layoutType' => $layoutType,
                'name' => 'New layout',
                'description' => null,
                'shared' => false,
                'mainLocale' => 'en',
            ],
            $this->exportObjectVars($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\LayoutStructBuilder::newLayoutUpdateStruct
     */
    public function testNewLayoutUpdateStruct(): void
    {
        $struct = $this->structBuilder->newLayoutUpdateStruct(
            $this->layoutService->loadLayoutDraft(1)
        );

        $this->assertInstanceOf(LayoutUpdateStruct::class, $struct);

        $this->assertSame(
            [
                'name' => 'My layout',
                'description' => 'My layout description',
            ],
            $this->exportObjectVars($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\LayoutStructBuilder::newLayoutUpdateStruct
     */
    public function testNewLayoutUpdateStructWithNoLayout(): void
    {
        $struct = $this->structBuilder->newLayoutUpdateStruct();

        $this->assertInstanceOf(LayoutUpdateStruct::class, $struct);

        $this->assertSame(
            [
                'name' => null,
                'description' => null,
            ],
            $this->exportObjectVars($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\LayoutStructBuilder::newLayoutCopyStruct
     */
    public function testNewLayoutCopyStruct(): void
    {
        $struct = $this->structBuilder->newLayoutCopyStruct(
            $this->layoutService->loadLayoutDraft(1)
        );

        $this->assertInstanceOf(LayoutCopyStruct::class, $struct);

        $this->assertSame(
            [
                'name' => 'My layout (copy)',
                'description' => null,
            ],
            $this->exportObjectVars($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\LayoutStructBuilder::newLayoutCopyStruct
     */
    public function testNewLayoutCopyStructWithNoLayout(): void
    {
        $struct = $this->structBuilder->newLayoutCopyStruct();

        $this->assertInstanceOf(LayoutCopyStruct::class, $struct);

        $this->assertSame(
            [
                'name' => null,
                'description' => null,
            ],
            $this->exportObjectVars($struct)
        );
    }
}

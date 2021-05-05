<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\StructBuilder;

use Netgen\Layouts\Core\StructBuilder\LayoutStructBuilder;
use Netgen\Layouts\Layout\Type\LayoutType;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use Ramsey\Uuid\Uuid;

abstract class LayoutStructBuilderTest extends CoreTestCase
{
    use ExportObjectTrait;

    private LayoutStructBuilder $structBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->structBuilder = new LayoutStructBuilder();
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\LayoutStructBuilder::newLayoutCreateStruct
     */
    public function testNewLayoutCreateStruct(): void
    {
        $layoutType = LayoutType::fromArray(['identifier' => '4_zones_a']);

        $struct = $this->structBuilder->newLayoutCreateStruct(
            $layoutType,
            'New layout',
            'en',
        );

        self::assertSame(
            [
                'uuid' => null,
                'layoutType' => $layoutType,
                'name' => 'New layout',
                'description' => '',
                'shared' => false,
                'mainLocale' => 'en',
            ],
            $this->exportObject($struct),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\LayoutStructBuilder::newLayoutUpdateStruct
     */
    public function testNewLayoutUpdateStruct(): void
    {
        $struct = $this->structBuilder->newLayoutUpdateStruct(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136')),
        );

        self::assertSame(
            [
                'name' => 'My layout',
                'description' => 'My layout description',
            ],
            $this->exportObject($struct),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\LayoutStructBuilder::newLayoutUpdateStruct
     */
    public function testNewLayoutUpdateStructWithNoLayout(): void
    {
        $struct = $this->structBuilder->newLayoutUpdateStruct();

        self::assertSame(
            [
                'name' => null,
                'description' => null,
            ],
            $this->exportObject($struct),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\LayoutStructBuilder::newLayoutCopyStruct
     */
    public function testNewLayoutCopyStruct(): void
    {
        $struct = $this->structBuilder->newLayoutCopyStruct(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136')),
        );

        self::assertSame(
            [
                'name' => 'My layout (copy)',
                'description' => null,
            ],
            $this->exportObject($struct),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\LayoutStructBuilder::newLayoutCopyStruct
     */
    public function testNewLayoutCopyStructWithNoLayout(): void
    {
        $struct = $this->structBuilder->newLayoutCopyStruct();

        self::assertSame(
            [
                'name' => 'Layout (copy)',
                'description' => null,
            ],
            $this->exportObject($struct),
        );
    }
}

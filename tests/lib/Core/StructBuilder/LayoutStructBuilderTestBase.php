<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\StructBuilder;

use Netgen\Layouts\Core\StructBuilder\LayoutStructBuilder;
use Netgen\Layouts\Layout\Type\LayoutType;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use Ramsey\Uuid\Uuid;

abstract class LayoutStructBuilderTestBase extends CoreTestCase
{
    use ExportObjectTrait;

    private LayoutStructBuilder $structBuilder;

    final protected function setUp(): void
    {
        parent::setUp();

        $this->structBuilder = new LayoutStructBuilder();
    }

    final public function testNewLayoutCreateStruct(): void
    {
        $layoutType = LayoutType::fromArray(['identifier' => 'test_layout_1']);

        $struct = $this->structBuilder->newLayoutCreateStruct(
            $layoutType,
            'New layout',
            'en',
        );

        self::assertSame(
            [
                'description' => '',
                'isShared' => false,
                'layoutType' => $layoutType,
                'mainLocale' => 'en',
                'name' => 'New layout',
                'uuid' => null,
            ],
            $this->exportObject($struct),
        );
    }

    final public function testNewLayoutUpdateStruct(): void
    {
        $struct = $this->structBuilder->newLayoutUpdateStruct(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136')),
        );

        self::assertSame(
            [
                'description' => 'My layout description',
                'name' => 'My layout',
            ],
            $this->exportObject($struct),
        );
    }

    final public function testNewLayoutUpdateStructWithNoLayout(): void
    {
        $struct = $this->structBuilder->newLayoutUpdateStruct();

        self::assertSame(
            [
                'description' => null,
                'name' => null,
            ],
            $this->exportObject($struct),
        );
    }

    final public function testNewLayoutCopyStruct(): void
    {
        $struct = $this->structBuilder->newLayoutCopyStruct(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136')),
        );

        self::assertSame(
            [
                'description' => null,
                'name' => 'My layout (copy)',
            ],
            $this->exportObject($struct),
        );
    }

    final public function testNewLayoutCopyStructWithNoLayout(): void
    {
        $struct = $this->structBuilder->newLayoutCopyStruct();

        self::assertSame(
            [
                'description' => null,
                'name' => 'Layout (copy)',
            ],
            $this->exportObject($struct),
        );
    }
}

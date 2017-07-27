<?php

namespace Netgen\BlockManager\Tests\Core\Service\StructBuilder;

use Netgen\BlockManager\API\Values\Layout\LayoutCopyStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct;
use Netgen\BlockManager\Core\Service\StructBuilder\LayoutStructBuilder;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;

abstract class LayoutStructBuilderTest extends ServiceTestCase
{
    /**
     * @var \Netgen\BlockManager\Core\Service\StructBuilder\LayoutStructBuilder
     */
    protected $structBuilder;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        parent::setUp();

        $this->layoutService = $this->createLayoutService(
            $this->createMock(LayoutValidator::class)
        );

        $this->structBuilder = new LayoutStructBuilder();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\LayoutStructBuilder::newLayoutCreateStruct
     */
    public function testNewLayoutCreateStruct()
    {
        $this->assertEquals(
            new LayoutCreateStruct(
                array(
                    'layoutType' => new LayoutType(array('identifier' => '4_zones_a')),
                    'name' => 'New layout',
                    'mainLocale' => 'en',
                )
            ),
            $this->structBuilder->newLayoutCreateStruct(
                new LayoutType(array('identifier' => '4_zones_a')),
                'New layout',
                'en'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\LayoutStructBuilder::newLayoutUpdateStruct
     */
    public function testNewLayoutUpdateStruct()
    {
        $this->assertEquals(
            new LayoutUpdateStruct(
                array(
                    'name' => 'My layout',
                    'description' => 'My layout description',
                )
            ),
            $this->structBuilder->newLayoutUpdateStruct(
                $this->layoutService->loadLayoutDraft(1)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\LayoutStructBuilder::newLayoutUpdateStruct
     */
    public function testNewLayoutUpdateStructWithNoLayout()
    {
        $this->assertEquals(
            new LayoutUpdateStruct(),
            $this->structBuilder->newLayoutUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\LayoutStructBuilder::newLayoutCopyStruct
     */
    public function testNewLayoutCopyStruct()
    {
        $this->assertEquals(
            new LayoutCopyStruct(
                array(
                    'name' => 'My layout (copy)',
                    'description' => null,
                )
            ),
            $this->structBuilder->newLayoutCopyStruct(
                $this->layoutService->loadLayoutDraft(1)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\LayoutStructBuilder::newLayoutCopyStruct
     */
    public function testNewLayoutCopyStructWithNoLayout()
    {
        $this->assertEquals(
            new LayoutCopyStruct(),
            $this->structBuilder->newLayoutCopyStruct()
        );
    }
}

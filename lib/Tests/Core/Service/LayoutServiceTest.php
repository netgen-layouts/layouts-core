<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\Zone;

abstract class LayoutServiceTest extends ServiceTest
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockValidatorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutValidatorMock;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->blockValidatorMock = $this->getMockBuilder('Netgen\BlockManager\Core\Service\Validator\BlockValidator')
            ->disableOriginalConstructor()
            ->getMock();

        $this->layoutValidatorMock = $this->getMockBuilder('Netgen\BlockManager\Core\Service\Validator\LayoutValidator')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::__construct
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayout
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::buildDomainLayoutObject
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::buildDomainZoneObject
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createDateTime
     */
    public function testLoadLayout()
    {
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);

        $layout = $layoutService->loadLayout(1);

        self::assertInstanceOf('Netgen\BlockManager\API\Values\Page\Layout', $layout);

        self::assertEquals(1, $layout->getId());
        self::assertNull($layout->getParentId());
        self::assertEquals('3_zones_a', $layout->getIdentifier());
        self::assertEquals('My layout', $layout->getName());
        self::assertEquals(Layout::STATUS_PUBLISHED, $layout->getStatus());

        self::assertInstanceOf('DateTime', $layout->getCreated());
        self::assertGreaterThan(0, $layout->getCreated()->getTimestamp());

        self::assertInstanceOf('DateTime', $layout->getModified());
        self::assertGreaterThan(0, $layout->getModified()->getTimestamp());

        self::assertEquals(
            array(
                'top_left' => new Zone(
                    array(
                        'id' => 1,
                        'layoutId' => $layout->getId(),
                        'identifier' => 'top_left',
                        'status' => Layout::STATUS_PUBLISHED,
                        'blocks' => array(),
                    )
                ),
                'top_right' => new Zone(
                    array(
                        'id' => 2,
                        'layoutId' => $layout->getId(),
                        'identifier' => 'top_right',
                        'status' => Layout::STATUS_PUBLISHED,
                        'blocks' => array(
                            new Block(
                                array(
                                    'id' => 1,
                                    'zoneId' => 2,
                                    'definitionIdentifier' => 'paragraph',
                                    'parameters' => array(
                                        'some_param' => 'some_value',
                                    ),
                                    'viewType' => 'default',
                                    'name' => 'My block',
                                    'status' => Layout::STATUS_PUBLISHED,
                                )
                            ),
                            new Block(
                                array(
                                    'id' => 2,
                                    'zoneId' => 2,
                                    'definitionIdentifier' => 'title',
                                    'parameters' => array(
                                        'other_param' => 'other_value',
                                    ),
                                    'viewType' => 'small',
                                    'name' => 'My other block',
                                    'status' => Layout::STATUS_PUBLISHED,
                                )
                            ),
                        ),
                    )
                ),
                'bottom' => new Zone(
                    array(
                        'id' => 3,
                        'layoutId' => $layout->getId(),
                        'identifier' => 'bottom',
                        'status' => Layout::STATUS_PUBLISHED,
                        'blocks' => array(),
                    )
                ),
            ),
            $layout->getZones()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayout
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testLoadLayoutThrowsInvalidArgumentExceptionOnInvalidId()
    {
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);
        $layoutService->loadLayout(42.24);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayout
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testLoadLayoutThrowsInvalidArgumentExceptionOnEmptyId()
    {
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);
        $layoutService->loadLayout('');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayout
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadLayoutThrowsNotFoundException()
    {
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);
        $layoutService->loadLayout(PHP_INT_MAX);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     */
    public function testLoadZone()
    {
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);

        self::assertEquals(
            new Zone(
                array(
                    'id' => 1,
                    'layoutId' => 1,
                    'identifier' => 'top_left',
                    'status' => Layout::STATUS_PUBLISHED,
                    'blocks' => array(),
                )
            ),
            $layoutService->loadZone(1)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testLoadZoneThrowsInvalidArgumentExceptionOnInvalidId()
    {
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);
        $layoutService->loadZone(42.24);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testLoadZoneThrowsInvalidArgumentExceptionOnEmptyId()
    {
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);
        $layoutService->loadZone('');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadZoneThrowsNotFoundException()
    {
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);
        $layoutService->loadZone(PHP_INT_MAX);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     */
    public function testCreateLayout()
    {
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);

        $layoutCreateStruct = $layoutService->newLayoutCreateStruct(
            '3_zones_a',
            array('left', 'right', 'bottom'),
            'My layout'
        );

        $createdLayout = $layoutService->createLayout($layoutCreateStruct);

        self::assertInstanceOf('Netgen\BlockManager\API\Values\Page\Layout', $createdLayout);

        self::assertEquals(3, $createdLayout->getId());
        self::assertNull($createdLayout->getParentId());
        self::assertEquals('3_zones_a', $createdLayout->getIdentifier());
        self::assertEquals('My layout', $createdLayout->getName());
        self::assertEquals(Layout::STATUS_DRAFT, $createdLayout->getStatus());

        self::assertInstanceOf('DateTime', $createdLayout->getCreated());
        self::assertGreaterThan(0, $createdLayout->getCreated()->getTimestamp());

        self::assertInstanceOf('DateTime', $createdLayout->getModified());
        self::assertGreaterThan(0, $createdLayout->getModified()->getTimestamp());

        self::assertEquals(
            array(
                'left' => new Zone(
                    array(
                        'id' => 7,
                        'layoutId' => $createdLayout->getId(),
                        'identifier' => 'left',
                        'status' => Layout::STATUS_DRAFT,
                        'blocks' => array(),
                    )
                ),
                'right' => new Zone(
                    array(
                        'id' => 8,
                        'layoutId' => $createdLayout->getId(),
                        'identifier' => 'right',
                        'status' => Layout::STATUS_DRAFT,
                        'blocks' => array(),
                    )
                ),
                'bottom' => new Zone(
                    array(
                        'id' => 9,
                        'layoutId' => $createdLayout->getId(),
                        'identifier' => 'bottom',
                        'status' => Layout::STATUS_DRAFT,
                        'blocks' => array(),
                    )
                ),
            ),
            $createdLayout->getZones()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     */
    public function testCreateLayoutWithParentId()
    {
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);

        $layoutCreateStruct = $layoutService->newLayoutCreateStruct(
            '3_zones_a',
            array('left', 'right', 'bottom'),
            'My layout'
        );

        $parentLayout = $layoutService->loadLayout(1);
        $createdLayout = $layoutService->createLayout(
            $layoutCreateStruct,
            $parentLayout
        );

        self::assertInstanceOf('Netgen\BlockManager\API\Values\Page\Layout', $createdLayout);

        self::assertEquals(3, $createdLayout->getId());
        self::assertEquals($parentLayout->getId(), $createdLayout->getParentId());
        self::assertEquals('3_zones_a', $createdLayout->getIdentifier());
        self::assertEquals('My layout', $createdLayout->getName());
        self::assertEquals(Layout::STATUS_DRAFT, $createdLayout->getStatus());

        self::assertInstanceOf('DateTime', $createdLayout->getCreated());
        self::assertGreaterThan(0, $createdLayout->getCreated()->getTimestamp());

        self::assertInstanceOf('DateTime', $createdLayout->getModified());
        self::assertGreaterThan(0, $createdLayout->getModified()->getTimestamp());

        self::assertEquals(
            array(
                'left' => new Zone(
                    array(
                        'id' => 7,
                        'layoutId' => $createdLayout->getId(),
                        'identifier' => 'left',
                        'status' => Layout::STATUS_DRAFT,
                        'blocks' => array(),
                    )
                ),
                'right' => new Zone(
                    array(
                        'id' => 8,
                        'layoutId' => $createdLayout->getId(),
                        'identifier' => 'right',
                        'status' => Layout::STATUS_DRAFT,
                        'blocks' => array(),
                    )
                ),
                'bottom' => new Zone(
                    array(
                        'id' => 9,
                        'layoutId' => $createdLayout->getId(),
                        'identifier' => 'bottom',
                        'status' => Layout::STATUS_DRAFT,
                        'blocks' => array(),
                    )
                ),
            ),
            $createdLayout->getZones()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::copyLayout
     */
    public function testCopyLayout()
    {
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);

        $layout = $layoutService->loadLayout(1);
        $copiedLayout = $layoutService->copyLayout($layout);

        self::assertInstanceOf('Netgen\BlockManager\API\Values\Page\Layout', $copiedLayout);

        self::assertEquals(3, $copiedLayout->getId());
        self::assertNull($copiedLayout->getParentId());
        self::assertEquals('3_zones_a', $copiedLayout->getIdentifier());
        self::assertEquals('My layout', $copiedLayout->getName());
        self::assertEquals(Layout::STATUS_DRAFT, $copiedLayout->getStatus());

        self::assertInstanceOf('DateTime', $copiedLayout->getCreated());
        self::assertGreaterThan(0, $copiedLayout->getCreated()->getTimestamp());

        self::assertInstanceOf('DateTime', $copiedLayout->getModified());
        self::assertGreaterThan(0, $copiedLayout->getModified()->getTimestamp());

        self::assertEquals(
            array(
                'top_left' => new Zone(
                    array(
                        'id' => 7,
                        'layoutId' => $copiedLayout->getId(),
                        'identifier' => 'top_left',
                        'status' => Layout::STATUS_DRAFT,
                        'blocks' => array(),
                    )
                ),
                'top_right' => new Zone(
                    array(
                        'id' => 8,
                        'layoutId' => $copiedLayout->getId(),
                        'identifier' => 'top_right',
                        'status' => Layout::STATUS_DRAFT,
                        'blocks' => array(),
                    )
                ),
                'bottom' => new Zone(
                    array(
                        'id' => 9,
                        'layoutId' => $copiedLayout->getId(),
                        'identifier' => 'bottom',
                        'status' => Layout::STATUS_DRAFT,
                        'blocks' => array(),
                    )
                ),
            ),
            $copiedLayout->getZones()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::deleteLayout
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testDeleteLayout()
    {
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);
        $blockService = $this->createBlockService($this->blockValidatorMock);

        $layout = $layoutService->loadLayout(1);

        // We need to delete the blocks and block items from zones
        // to be able to delete the zones themselves
        foreach ($layout->getZones() as $zone) {
            $zoneBlocks = $blockService->loadZoneBlocks($zone);
            foreach ($zoneBlocks as $block) {
                $blockService->deleteBlock($block);
            }
        }

        $layoutService->deleteLayout($layout);

        $layoutService->loadLayout($layout->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::newLayoutCreateStruct
     */
    public function testNewLayoutCreateStruct()
    {
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);

        self::assertEquals(
            new LayoutCreateStruct(
                array(
                    'identifier' => '3_zones_a',
                    'zoneIdentifiers' => array('left', 'right', 'bottom'),
                    'name' => 'New layout',
                )
            ),
            $layoutService->newLayoutCreateStruct('3_zones_a', array('left', 'right', 'bottom'), 'New layout')
        );
    }
}

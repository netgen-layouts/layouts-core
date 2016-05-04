<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\API\Exception\NotFoundException;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\Zone;
use DateTime;

abstract class LayoutServiceTest extends ServiceTest
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutValidatorMock;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->layoutValidatorMock = $this->getMockBuilder(LayoutValidator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->layoutService = $this->createLayoutService($this->layoutValidatorMock);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::__construct
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayout
     */
    public function testLoadLayout()
    {
        $this->layoutValidatorMock
            ->expects($this->at(0))
            ->method('validateId')
            ->with($this->equalTo(1), $this->equalTo('layoutId'));

        $layout = $this->layoutService->loadLayout(1);

        self::assertInstanceOf(Layout::class, $layout);

        self::assertEquals(1, $layout->getId());
        self::assertNull($layout->getParentId());
        self::assertEquals('3_zones_a', $layout->getIdentifier());
        self::assertEquals('My layout', $layout->getName());
        self::assertEquals(Layout::STATUS_PUBLISHED, $layout->getStatus());

        self::assertInstanceOf(DateTime::class, $layout->getCreated());
        self::assertGreaterThan(0, $layout->getCreated()->getTimestamp());

        self::assertInstanceOf(DateTime::class, $layout->getModified());
        self::assertGreaterThan(0, $layout->getModified()->getTimestamp());

        self::assertEquals(
            array(
                'bottom' => new Zone(
                    array(
                        'identifier' => 'bottom',
                        'layoutId' => $layout->getId(),
                        'status' => Layout::STATUS_PUBLISHED,
                        'blocks' => array(),
                    )
                ),
                'top_left' => new Zone(
                    array(
                        'identifier' => 'top_left',
                        'layoutId' => $layout->getId(),
                        'status' => Layout::STATUS_PUBLISHED,
                        'blocks' => array(),
                    )
                ),
                'top_right' => new Zone(
                    array(
                        'identifier' => 'top_right',
                        'layoutId' => $layout->getId(),
                        'status' => Layout::STATUS_PUBLISHED,
                        'blocks' => array(
                            new Block(
                                array(
                                    'id' => 1,
                                    'layoutId' => 1,
                                    'zoneIdentifier' => 'top_right',
                                    'position' => 0,
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
                                    'layoutId' => 1,
                                    'zoneIdentifier' => 'top_right',
                                    'position' => 1,
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
            ),
            $layout->getZones()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayout
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadLayoutThrowsNotFoundException()
    {
        $this->layoutService->loadLayout(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     */
    public function testLoadZone()
    {
        $this->layoutValidatorMock
            ->expects($this->at(0))
            ->method('validateId')
            ->with($this->equalTo(1), $this->equalTo('layoutId'));

        $this->layoutValidatorMock
            ->expects($this->at(1))
            ->method('validateIdentifier')
            ->with($this->equalTo('top_left'), $this->equalTo('identifier'));

        self::assertEquals(
            new Zone(
                array(
                    'identifier' => 'top_left',
                    'layoutId' => 1,
                    'status' => Layout::STATUS_PUBLISHED,
                    'blocks' => array(),
                )
            ),
            $this->layoutService->loadZone(1, 'top_left')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadZoneThrowsNotFoundExceptionOnNonExistingLayout()
    {
        $this->layoutService->loadZone(999999, 'bottom');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadZoneThrowsNotFoundExceptionOnNonExistingZone()
    {
        $this->layoutService->loadZone(1, 'not_existing');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     */
    public function testCreateLayout()
    {
        $layoutCreateStruct = $this->layoutService->newLayoutCreateStruct(
            '3_zones_a',
            'My layout',
            array('left', 'right', 'bottom')
        );

        $createdLayout = $this->layoutService->createLayout($layoutCreateStruct);

        self::assertInstanceOf(Layout::class, $createdLayout);

        self::assertEquals(3, $createdLayout->getId());
        self::assertNull($createdLayout->getParentId());
        self::assertEquals('3_zones_a', $createdLayout->getIdentifier());
        self::assertEquals('My layout', $createdLayout->getName());
        self::assertEquals(Layout::STATUS_DRAFT, $createdLayout->getStatus());

        self::assertInstanceOf(DateTime::class, $createdLayout->getCreated());
        self::assertGreaterThan(0, $createdLayout->getCreated()->getTimestamp());

        self::assertInstanceOf(DateTime::class, $createdLayout->getModified());
        self::assertGreaterThan(0, $createdLayout->getModified()->getTimestamp());

        self::assertEquals(
            array(
                'bottom' => new Zone(
                    array(
                        'identifier' => 'bottom',
                        'layoutId' => $createdLayout->getId(),
                        'status' => Layout::STATUS_DRAFT,
                        'blocks' => array(),
                    )
                ),
                'left' => new Zone(
                    array(
                        'identifier' => 'left',
                        'layoutId' => $createdLayout->getId(),
                        'status' => Layout::STATUS_DRAFT,
                        'blocks' => array(),
                    )
                ),
                'right' => new Zone(
                    array(
                        'identifier' => 'right',
                        'layoutId' => $createdLayout->getId(),
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
        $layoutCreateStruct = $this->layoutService->newLayoutCreateStruct(
            '3_zones_a',
            'My layout',
            array('left', 'right', 'bottom')
        );

        $parentLayout = $this->layoutService->loadLayout(1);
        $createdLayout = $this->layoutService->createLayout(
            $layoutCreateStruct,
            $parentLayout
        );

        self::assertInstanceOf(Layout::class, $createdLayout);

        self::assertEquals(3, $createdLayout->getId());
        self::assertEquals($parentLayout->getId(), $createdLayout->getParentId());
        self::assertEquals('3_zones_a', $createdLayout->getIdentifier());
        self::assertEquals('My layout', $createdLayout->getName());
        self::assertEquals(Layout::STATUS_DRAFT, $createdLayout->getStatus());

        self::assertInstanceOf(DateTime::class, $createdLayout->getCreated());
        self::assertGreaterThan(0, $createdLayout->getCreated()->getTimestamp());

        self::assertInstanceOf(DateTime::class, $createdLayout->getModified());
        self::assertGreaterThan(0, $createdLayout->getModified()->getTimestamp());

        self::assertEquals(
            array(
                'bottom' => new Zone(
                    array(
                        'identifier' => 'bottom',
                        'layoutId' => $createdLayout->getId(),
                        'status' => Layout::STATUS_DRAFT,
                        'blocks' => array(),
                    )
                ),
                'left' => new Zone(
                    array(
                        'identifier' => 'left',
                        'layoutId' => $createdLayout->getId(),
                        'status' => Layout::STATUS_DRAFT,
                        'blocks' => array(),
                    )
                ),
                'right' => new Zone(
                    array(
                        'identifier' => 'right',
                        'layoutId' => $createdLayout->getId(),
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
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayoutStatus
     */
    public function testCreateLayoutStatus()
    {
        $layout = $this->layoutService->loadLayout(1);
        $copiedLayout = $this->layoutService->createLayoutStatus($layout, Layout::STATUS_ARCHIVED);

        self::assertInstanceOf(Layout::class, $copiedLayout);

        self::assertEquals(1, $copiedLayout->getId());
        self::assertNull($copiedLayout->getParentId());
        self::assertEquals('3_zones_a', $copiedLayout->getIdentifier());
        self::assertEquals('My layout', $copiedLayout->getName());
        self::assertEquals(Layout::STATUS_ARCHIVED, $copiedLayout->getStatus());

        self::assertInstanceOf(DateTime::class, $copiedLayout->getCreated());
        self::assertGreaterThan(0, $copiedLayout->getCreated()->getTimestamp());

        self::assertInstanceOf(DateTime::class, $copiedLayout->getModified());
        self::assertGreaterThan(0, $copiedLayout->getModified()->getTimestamp());

        self::assertEquals(
            array(
                'bottom' => new Zone(
                    array(
                        'identifier' => 'bottom',
                        'layoutId' => 1,
                        'status' => Layout::STATUS_ARCHIVED,
                        'blocks' => array(),
                    )
                ),
                'top_left' => new Zone(
                    array(
                        'identifier' => 'top_left',
                        'layoutId' => 1,
                        'status' => Layout::STATUS_ARCHIVED,
                        'blocks' => array(),
                    )
                ),
                'top_right' => new Zone(
                    array(
                        'identifier' => 'top_right',
                        'layoutId' => 1,
                        'status' => Layout::STATUS_ARCHIVED,
                        'blocks' => array(
                            new Block(
                                array(
                                    'id' => 1,
                                    'layoutId' => 1,
                                    'zoneIdentifier' => 'top_right',
                                    'position' => 0,
                                    'definitionIdentifier' => 'paragraph',
                                    'parameters' => array(
                                        'some_param' => 'some_value',
                                    ),
                                    'viewType' => 'default',
                                    'name' => 'My block',
                                    'status' => Layout::STATUS_ARCHIVED,
                                )
                            ),
                            new Block(
                                array(
                                    'id' => 2,
                                    'layoutId' => 1,
                                    'zoneIdentifier' => 'top_right',
                                    'position' => 1,
                                    'definitionIdentifier' => 'title',
                                    'parameters' => array(
                                        'other_param' => 'other_value',
                                    ),
                                    'viewType' => 'small',
                                    'name' => 'My other block',
                                    'status' => Layout::STATUS_ARCHIVED,
                                )
                            ),
                        ),
                    )
                ),
            ),
            $copiedLayout->getZones()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayoutStatus
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCreateLayoutStatusThrowsBadStateException()
    {
        $layout = $this->layoutService->loadLayout(1);
        $this->layoutService->createLayoutStatus($layout, Layout::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::publishLayout
     */
    public function testPublishLayout()
    {
        $layout = $this->layoutService->loadLayout(1, Layout::STATUS_DRAFT);
        $publishedLayout = $this->layoutService->publishLayout($layout);

        self::assertInstanceOf(Layout::class, $publishedLayout);
        self::assertEquals(Layout::STATUS_PUBLISHED, $publishedLayout->getStatus());

        $archivedLayout = $this->layoutService->loadLayout($layout->getId(), Layout::STATUS_ARCHIVED);
        self::assertInstanceOf(Layout::class, $archivedLayout);

        try {
            $this->layoutService->loadLayout($layout->getId(), Layout::STATUS_DRAFT);
            self::fail('Draft layout still exists after publishing.');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::publishLayout
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testPublishLayoutThrowsBadStateException()
    {
        $layout = $this->layoutService->loadLayout(1, Layout::STATUS_PUBLISHED);
        $this->layoutService->publishLayout($layout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::deleteLayout
     */
    public function testDeleteLayout()
    {
        $layout = $this->layoutService->loadLayout(1, Layout::STATUS_DRAFT);

        $this->layoutService->deleteLayout($layout);

        try {
            $this->layoutService->loadLayout($layout->getId(), Layout::STATUS_DRAFT);
            self::fail('Draft layout still exists after deleting it');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $publishedLayout = $this->layoutService->loadLayout($layout->getId());
        self::assertInstanceOf(Layout::class, $publishedLayout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::deleteLayout
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testDeleteCompleteLayout()
    {
        $layout = $this->layoutService->loadLayout(1);

        $this->layoutService->deleteLayout($layout, true);

        $this->layoutService->loadLayout($layout->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::newLayoutCreateStruct
     */
    public function testNewLayoutCreateStruct()
    {
        self::assertEquals(
            new LayoutCreateStruct(
                array(
                    'identifier' => '3_zones_a',
                    'name' => 'New layout',
                    'zoneIdentifiers' => array('left', 'right', 'bottom'),
                )
            ),
            $this->layoutService->newLayoutCreateStruct('3_zones_a', 'New layout', array('left', 'right', 'bottom'))
        );
    }
}

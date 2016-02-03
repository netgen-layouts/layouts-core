<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\API\Exception\NotFoundException;
use Netgen\BlockManager\API\Service\Validator\LayoutValidator;
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
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->layoutValidatorMock = $this->getMockBuilder(LayoutValidator::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::__construct
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayout
     */
    public function testLoadLayout()
    {
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);

        $layout = $layoutService->loadLayout(1);

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
                    'identifier' => 'top_left',
                    'layoutId' => 1,
                    'status' => Layout::STATUS_PUBLISHED,
                    'blocks' => array(),
                )
            ),
            $layoutService->loadZone(1, 'top_left')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testLoadZoneThrowsInvalidArgumentExceptionOnInvalidLayoutId()
    {
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);
        $layoutService->loadZone(42.24, 'zone');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testLoadZoneThrowsInvalidArgumentExceptionOnEmptyLayoutId()
    {
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);
        $layoutService->loadZone('', 'zone');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testLoadZoneThrowsInvalidArgumentExceptionOnInvalidIdentifier()
    {
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);
        $layoutService->loadZone(1, 42.24);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testLoadZoneThrowsInvalidArgumentExceptionOnEmptyIdentifier()
    {
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);
        $layoutService->loadZone(1, '');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadZoneThrowsNotFoundExceptionOnNonExistingLayout()
    {
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);
        $layoutService->loadZone(PHP_INT_MAX, 'bottom');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadZoneThrowsNotFoundExceptionOnNonExistingZone()
    {
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);
        $layoutService->loadZone(1, 'not_existing');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     */
    public function testCreateLayout()
    {
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);

        $layoutCreateStruct = $layoutService->newLayoutCreateStruct(
            '3_zones_a',
            'My layout',
            array('left', 'right', 'bottom')
        );

        $createdLayout = $layoutService->createLayout($layoutCreateStruct);

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
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);

        $layoutCreateStruct = $layoutService->newLayoutCreateStruct(
            '3_zones_a',
            'My layout',
            array('left', 'right', 'bottom')
        );

        $parentLayout = $layoutService->loadLayout(1);
        $createdLayout = $layoutService->createLayout(
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
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);

        $layout = $layoutService->loadLayout(1);
        $copiedLayout = $layoutService->createLayoutStatus($layout, Layout::STATUS_ARCHIVED);

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
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);

        $layout = $layoutService->loadLayout(1);
        $layoutService->createLayoutStatus($layout, Layout::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::publishLayout
     */
    public function testPublishLayout()
    {
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);

        $layout = $layoutService->loadLayout(1, Layout::STATUS_DRAFT);
        $publishedLayout = $layoutService->publishLayout($layout);

        self::assertInstanceOf(Layout::class, $publishedLayout);
        self::assertEquals(Layout::STATUS_PUBLISHED, $publishedLayout->getStatus());

        $archivedLayout = $layoutService->loadLayout($layout->getId(), Layout::STATUS_ARCHIVED);
        self::assertInstanceOf(Layout::class, $archivedLayout);

        try {
            $layoutService->loadLayout($layout->getId(), Layout::STATUS_DRAFT);
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
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);

        $layout = $layoutService->loadLayout(1, Layout::STATUS_PUBLISHED);
        $layoutService->publishLayout($layout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::deleteLayout
     */
    public function testDeleteLayout()
    {
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);

        $layout = $layoutService->loadLayout(1, Layout::STATUS_DRAFT);

        $layoutService->deleteLayout($layout);

        try {
            $layoutService->loadLayout($layout->getId(), Layout::STATUS_DRAFT);
            self::fail('Draft layout still exists after deleting it');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $publishedLayout = $layoutService->loadLayout($layout->getId());
        self::assertInstanceOf(Layout::class, $publishedLayout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::deleteLayout
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testDeleteCompleteLayout()
    {
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);

        $layout = $layoutService->loadLayout(1);

        $layoutService->deleteLayout($layout, true);

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
                    'name' => 'New layout',
                    'zoneIdentifiers' => array('left', 'right', 'bottom'),
                )
            ),
            $layoutService->newLayoutCreateStruct('3_zones_a', 'New layout', array('left', 'right', 'bottom'))
        );
    }
}

<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\Core\Values\Page\Zone;
use Netgen\BlockManager\API\Exception\NotFoundException;

abstract class LayoutServiceTest extends ServiceTest
{
    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::__construct
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayout
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::buildDomainLayoutObject
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::buildDomainZoneObject
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createDateTime
     */
    public function testLoadLayout()
    {
        $layoutService = $this->createLayoutService();

        $layout = $layoutService->loadLayout(1);

        self::assertInstanceOf('Netgen\BlockManager\API\Values\Page\Layout', $layout);

        self::assertEquals(1, $layout->getId());
        self::assertNull($layout->getParentId());
        self::assertEquals('3_zones_a', $layout->getIdentifier());
        self::assertEquals('My layout', $layout->getName());

        self::assertInstanceOf('DateTime', $layout->getCreated());
        self::assertGreaterThan(0, $layout->getCreated()->getTimestamp());

        self::assertInstanceOf('DateTime', $layout->getModified());
        self::assertGreaterThan(0, $layout->getModified()->getTimestamp());

        self::assertEquals(
            array(
                new Zone(
                    array(
                        'id' => 1,
                        'layoutId' => $layout->getId(),
                        'identifier' => 'top_left',
                    )
                ),
                new Zone(
                    array(
                        'id' => 2,
                        'layoutId' => $layout->getId(),
                        'identifier' => 'top_right',
                    )
                ),
                new Zone(
                    array(
                        'id' => 3,
                        'layoutId' => $layout->getId(),
                        'identifier' => 'bottom',
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
        $layoutService = $this->createLayoutService();
        $layoutService->loadLayout(42.24);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayout
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testLoadLayoutThrowsInvalidArgumentExceptionOnEmptyId()
    {
        $layoutService = $this->createLayoutService();
        $layoutService->loadLayout('');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayout
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadLayoutThrowsNotFoundException()
    {
        $layoutService = $this->createLayoutService();
        $layoutService->loadLayout(PHP_INT_MAX);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     */
    public function testLoadZone()
    {
        $layoutService = $this->createLayoutService();

        self::assertEquals(
            new Zone(
                array(
                    'id' => 1,
                    'layoutId' => 1,
                    'identifier' => 'top_left',
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
        $layoutService = $this->createLayoutService();
        $layoutService->loadZone(42.24);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testLoadZoneThrowsInvalidArgumentExceptionOnEmptyId()
    {
        $layoutService = $this->createLayoutService();
        $layoutService->loadZone('');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadZoneThrowsNotFoundException()
    {
        $layoutService = $this->createLayoutService();
        $layoutService->loadZone(PHP_INT_MAX);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     */
    public function testCreateLayout()
    {
        $layoutService = $this->createLayoutService();

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

        self::assertInstanceOf('DateTime', $createdLayout->getCreated());
        self::assertGreaterThan(0, $createdLayout->getCreated()->getTimestamp());

        self::assertInstanceOf('DateTime', $createdLayout->getModified());
        self::assertGreaterThan(0, $createdLayout->getModified()->getTimestamp());

        self::assertEquals(
            array(
                new Zone(
                    array(
                        'id' => 7,
                        'layoutId' => $createdLayout->getId(),
                        'identifier' => 'left',
                    )
                ),
                new Zone(
                    array(
                        'id' => 8,
                        'layoutId' => $createdLayout->getId(),
                        'identifier' => 'right',
                    )
                ),
                new Zone(
                    array(
                        'id' => 9,
                        'layoutId' => $createdLayout->getId(),
                        'identifier' => 'bottom',
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
        $layoutService = $this->createLayoutService();

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

        self::assertInstanceOf('DateTime', $createdLayout->getCreated());
        self::assertGreaterThan(0, $createdLayout->getCreated()->getTimestamp());

        self::assertInstanceOf('DateTime', $createdLayout->getModified());
        self::assertGreaterThan(0, $createdLayout->getModified()->getTimestamp());

        self::assertEquals(
            array(
                new Zone(
                    array(
                        'id' => 7,
                        'layoutId' => $createdLayout->getId(),
                        'identifier' => 'left',
                    )
                ),
                new Zone(
                    array(
                        'id' => 8,
                        'layoutId' => $createdLayout->getId(),
                        'identifier' => 'right',
                    )
                ),
                new Zone(
                    array(
                        'id' => 9,
                        'layoutId' => $createdLayout->getId(),
                        'identifier' => 'bottom',
                    )
                ),
            ),
            $createdLayout->getZones()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testCreateLayoutZoneThrowsInvalidArgumentExceptionOnInvalidIdentifier()
    {
        $layoutService = $this->createLayoutService();

        $layoutCreateStruct = $layoutService->newLayoutCreateStruct(42, array('left'), 'New layout');
        $layoutService->createLayout($layoutCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testCreateLayoutZoneThrowsInvalidArgumentExceptionOnEmptyIdentifier()
    {
        $layoutService = $this->createLayoutService();

        $layoutCreateStruct = $layoutService->newLayoutCreateStruct('', array('left'), 'New layout');
        $layoutService->createLayout($layoutCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testCreateLayoutZoneThrowsInvalidArgumentExceptionOnInvalidName()
    {
        $layoutService = $this->createLayoutService();

        $layoutCreateStruct = $layoutService->newLayoutCreateStruct('3_zones_a', array('left'), 42);
        $layoutService->createLayout($layoutCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testCreateLayoutZoneThrowsInvalidArgumentExceptionOnEmptyName()
    {
        $layoutService = $this->createLayoutService();

        $layoutCreateStruct = $layoutService->newLayoutCreateStruct('3_zones_a', array('left'), '');
        $layoutService->createLayout($layoutCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testCreateLayoutZoneThrowsInvalidArgumentExceptionOnEmptyZoneIdentifiers()
    {
        $layoutService = $this->createLayoutService();

        $layoutCreateStruct = $layoutService->newLayoutCreateStruct('new_layout', array(), 'New layout');
        $layoutService->createLayout($layoutCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testCreateLayoutZoneThrowsInvalidArgumentExceptionOnInvalidZoneIdentifier()
    {
        $layoutService = $this->createLayoutService();

        $layoutCreateStruct = $layoutService->newLayoutCreateStruct('new_layout', array('left', 42), 'New layout');
        $layoutService->createLayout($layoutCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testCreateLayoutZoneThrowsInvalidArgumentExceptionOnEmptyZoneIdentifier()
    {
        $layoutService = $this->createLayoutService();

        $layoutCreateStruct = $layoutService->newLayoutCreateStruct('new_layout', array('left', ''), 'New layout');
        $layoutService->createLayout($layoutCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::copyLayout
     */
    public function testCopyLayout()
    {
        $layoutService = $this->createLayoutService();

        $layout = $layoutService->loadLayout(1);
        $copiedLayout = $layoutService->copyLayout($layout);

        self::assertInstanceOf('Netgen\BlockManager\API\Values\Page\Layout', $copiedLayout);

        self::assertEquals(3, $copiedLayout->getId());
        self::assertNull($copiedLayout->getParentId());
        self::assertEquals('3_zones_a', $copiedLayout->getIdentifier());
        self::assertEquals('My layout', $copiedLayout->getName());

        self::assertInstanceOf('DateTime', $copiedLayout->getCreated());
        self::assertGreaterThan(0, $copiedLayout->getCreated()->getTimestamp());

        self::assertInstanceOf('DateTime', $copiedLayout->getModified());
        self::assertGreaterThan(0, $copiedLayout->getModified()->getTimestamp());

        self::assertEquals(
            array(
                new Zone(
                    array(
                        'id' => 7,
                        'layoutId' => $copiedLayout->getId(),
                        'identifier' => 'top_left',
                    )
                ),
                new Zone(
                    array(
                        'id' => 8,
                        'layoutId' => $copiedLayout->getId(),
                        'identifier' => 'top_right',
                    )
                ),
                new Zone(
                    array(
                        'id' => 9,
                        'layoutId' => $copiedLayout->getId(),
                        'identifier' => 'bottom',
                    )
                ),
            ),
            $copiedLayout->getZones()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::deleteLayout
     */
    public function testDeleteLayout()
    {
        $layoutService = $this->createLayoutService();
        $blockService = $this->createBlockService();

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

        foreach ($layout->getZones() as $zone) {
            try {
                $layoutService->loadZone($zone->getId());
                $this->fail('Zone ' . $zone->getId() . ' not deleted when deleting layout.');
            } catch (NotFoundException $e) {
                // Do nothing
            }
        }

        try {
            $layoutService->loadLayout($layout->getId());
            $this->fail('Layout was not deleted.');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::newLayoutCreateStruct
     */
    public function testNewLayoutCreateStruct()
    {
        $layoutService = $this->createLayoutService();

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

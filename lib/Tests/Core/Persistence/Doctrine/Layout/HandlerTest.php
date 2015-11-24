<?php

namespace Netgen\BlockManager\Tests\Core\Persistence\Doctrine\Layout;

use Netgen\BlockManager\Tests\Core\Persistence\Doctrine\TestCase;
use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Values\Page\Layout;
use Netgen\BlockManager\Persistence\Values\Page\Zone;

class HandlerTest extends \PHPUnit_Framework_TestCase
{
    use TestCase;

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::__construct
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadLayout
     */
    public function testLoadLayout()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(
            new Layout(
                array(
                    'id' => 1,
                    'parentId' => null,
                    'identifier' => '3_zones_a',
                    'name' => 'My layout',
                    'created' => 1447065813,
                    'modified' => 1447065813,
                )
            ),
            $handler->loadLayout(1)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadLayout
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadLayoutThrowsNotFoundException()
    {
        $handler = $this->createLayoutHandler();
        $handler->loadLayout(PHP_INT_MAX);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadLayoutByIdentifier
     */
    public function testLoadLayoutByIdentifier()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(
            new Layout(
                array(
                    'id' => 1,
                    'parentId' => null,
                    'identifier' => '3_zones_a',
                    'name' => 'My layout',
                    'created' => 1447065813,
                    'modified' => 1447065813,
                )
            ),
            $handler->loadLayoutByIdentifier('3_zones_a')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadLayoutByIdentifier
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadLayoutByIdentifierThrowsNotFoundException()
    {
        $handler = $this->createLayoutHandler();
        $handler->loadLayoutByIdentifier('non_existing_layout');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadZone
     */
    public function testLoadZone()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(
            new Zone(
                array(
                    'id' => 1,
                    'layoutId' => 1,
                    'identifier' => 'top_left',
                )
            ),
            $handler->loadZone(1)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadZone
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadZoneThrowsNotFoundException()
    {
        $handler = $this->createLayoutHandler();
        $handler->loadZone(PHP_INT_MAX);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadLayoutZones
     */
    public function testLoadLayoutZones()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(
            array(
                new Zone(
                    array(
                        'id' => 1,
                        'layoutId' => 1,
                        'identifier' => 'top_left',
                    )
                ),
                new Zone(
                    array(
                        'id' => 2,
                        'layoutId' => 1,
                        'identifier' => 'top_right',
                    )
                ),
                new Zone(
                    array(
                        'id' => 3,
                        'layoutId' => 1,
                        'identifier' => 'bottom',
                    )
                ),
            ),
            $handler->loadLayoutZones(1)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadLayoutZones
     */
    public function testLoadLayoutZonesForNonExistingLayout()
    {
        $handler = $this->createLayoutHandler();
        self::assertEquals(array(), $handler->loadLayoutZones(PHP_INT_MAX));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createLayout
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createLayoutInsertQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createZoneInsertQuery
     */
    public function testCreateLayout()
    {
        $handler = $this->createLayoutHandler();

        $layoutCreateStruct = new LayoutCreateStruct();
        $layoutCreateStruct->layoutIdentifier = 'new_layout';
        $layoutCreateStruct->name = 'New layout';
        $layoutCreateStruct->zoneIdentifiers = array('first_zone', 'second_zone');

        $createdLayout = $handler->createLayout($layoutCreateStruct);

        self::assertInstanceOf('Netgen\BlockManager\Persistence\Values\Page\Layout', $createdLayout);

        self::assertEquals(3, $createdLayout->id);
        self::assertNull($createdLayout->parentId);
        self::assertEquals('new_layout', $createdLayout->identifier);
        self::assertEquals('New layout', $createdLayout->name);

        self::assertInternalType('int', $createdLayout->created);
        self::assertGreaterThan(0, $createdLayout->created);

        self::assertInternalType('int', $createdLayout->modified);
        self::assertGreaterThan(0, $createdLayout->modified);

        self::assertEquals(
            array(
                new Zone(
                    array(
                        'id' => 7,
                        'layoutId' => 3,
                        'identifier' => 'first_zone',
                    )
                ),
                new Zone(
                    array(
                        'id' => 8,
                        'layoutId' => 3,
                        'identifier' => 'second_zone',
                    )
                ),
            ),
            $handler->loadLayoutZones($createdLayout->id)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createLayout
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createLayoutInsertQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createZoneInsertQuery
     */
    public function testCreateLayoutWithParentLayout()
    {
        $handler = $this->createLayoutHandler();

        $layoutCreateStruct = new LayoutCreateStruct();
        $layoutCreateStruct->layoutIdentifier = 'new_layout';
        $layoutCreateStruct->name = 'New layout';
        $layoutCreateStruct->zoneIdentifiers = array('first_zone', 'second_zone');

        $createdLayout = $handler->createLayout($layoutCreateStruct, 1);

        self::assertInstanceOf('Netgen\BlockManager\Persistence\Values\Page\Layout', $createdLayout);

        self::assertEquals(3, $createdLayout->id);
        self::assertEquals(1, $createdLayout->parentId);
        self::assertEquals('new_layout', $createdLayout->identifier);
        self::assertEquals('New layout', $createdLayout->name);

        self::assertInternalType('int', $createdLayout->created);
        self::assertGreaterThan(0, $createdLayout->created);

        self::assertInternalType('int', $createdLayout->modified);
        self::assertGreaterThan(0, $createdLayout->modified);

        self::assertEquals(
            array(
                new Zone(
                    array(
                        'id' => 7,
                        'layoutId' => 3,
                        'identifier' => 'first_zone',
                    )
                ),
                new Zone(
                    array(
                        'id' => 8,
                        'layoutId' => 3,
                        'identifier' => 'second_zone',
                    )
                ),
            ),
            $handler->loadLayoutZones($createdLayout->id)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::copyLayout
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createLayoutInsertQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createZoneInsertQuery
     */
    public function testCopyLayout()
    {
        $handler = $this->createLayoutHandler();

        $copiedLayout = $handler->copyLayout(1, 'new_layout_identifier');

        self::assertInstanceOf('Netgen\BlockManager\Persistence\Values\Page\Layout', $copiedLayout);

        self::assertEquals(3, $copiedLayout->id);
        self::assertNull($copiedLayout->parentId);
        self::assertEquals('new_layout_identifier', $copiedLayout->identifier);
        self::assertEquals('My layout', $copiedLayout->name);

        self::assertInternalType('int', $copiedLayout->created);
        self::assertGreaterThan(0, $copiedLayout->created);

        self::assertInternalType('int', $copiedLayout->modified);
        self::assertGreaterThan(0, $copiedLayout->modified);

        self::assertEquals(
            array(
                new Zone(
                    array(
                        'id' => 7,
                        'layoutId' => 3,
                        'identifier' => 'top_left',
                    )
                ),
                new Zone(
                    array(
                        'id' => 8,
                        'layoutId' => 3,
                        'identifier' => 'top_right',
                    )
                ),
                new Zone(
                    array(
                        'id' => 9,
                        'layoutId' => 3,
                        'identifier' => 'bottom',
                    )
                ),
            ),
            $handler->loadLayoutZones($copiedLayout->id)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::deleteLayout
     */
    public function testDeleteLayout()
    {
        $handler = $this->createLayoutHandler();

        $layoutZones = $handler->loadLayoutZones(1);

        // We need to delete the blocks and block items from zones
        // to be able to delete the zones themselves
        foreach ($layoutZones as $layoutZone) {
            $query = $this->databaseConnection->createQueryBuilder();
            $query->delete('ngbm_block')
                ->where(
                    $query->expr()->eq('zone_id', ':zone_id')
                )
                ->setParameter('zone_id', $layoutZone->id);
            $query->execute();
        }

        $handler->deleteLayout(1);

        foreach ($layoutZones as $zone) {
            try {
                $handler->loadZone($zone->id);
                $this->fail('Zone ' . $zone->id . ' not deleted when deleting layout.');
            } catch (NotFoundException $e) {
                // Do nothing
            }
        }

        try {
            $handler->loadLayout(1);
            $this->fail('Layout was not deleted.');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }
}

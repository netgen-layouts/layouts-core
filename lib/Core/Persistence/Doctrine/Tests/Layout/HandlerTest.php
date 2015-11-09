<?php

namespace Netgen\BlockManager\Core\Persistence\Tests\Doctrine\Layout;

use Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler;
use Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Mapper;
use Netgen\BlockManager\Core\Persistence\Doctrine\Tests\TestCase;
use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\Exceptions\NotFoundException;
use Netgen\BlockManager\Persistence\Values\Page\Layout;
use Netgen\BlockManager\Persistence\Values\Page\Zone;

class HandlerTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadLayout
     */
    public function testLoadLayout()
    {
        $handler = $this->createHandler();

        self::assertEquals(
            new Layout(
                array(
                    'id' => 1,
                    'parentId' => null,
                    'identifier' => '3_zones_a',
                    'created' => 1447065813,
                    'modified' => 1447065813,
                )
            ),
            $handler->loadLayout(1)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadZone
     */
    public function testLoadZone()
    {
        $handler = $this->createHandler();

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
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadLayoutZones
     */
    public function testLoadLayoutZones()
    {
        $handler = $this->createHandler();

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
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createLayout
     */
    public function testCreateLayout()
    {
        $handler = $this->createHandler();

        $layoutCreateStruct = new LayoutCreateStruct();
        $layoutCreateStruct->layoutIdentifier = 'new_layout';
        $layoutCreateStruct->zoneIdentifiers = array('first_zone', 'second_zone');

        $createdLayout = $handler->createLayout($layoutCreateStruct);

        self::assertInstanceOf('Netgen\BlockManager\Persistence\Values\Page\Layout', $createdLayout);

        self::assertEquals(3, $createdLayout->id);
        self::assertNull($createdLayout->parentId);
        self::assertEquals('new_layout', $createdLayout->identifier);

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
     */
    public function testCreateLayoutWithParentLayout()
    {
        $handler = $this->createHandler();

        $layoutCreateStruct = new LayoutCreateStruct();
        $layoutCreateStruct->layoutIdentifier = 'new_layout';
        $layoutCreateStruct->zoneIdentifiers = array('first_zone', 'second_zone');

        $createdLayout = $handler->createLayout($layoutCreateStruct, 1);

        self::assertInstanceOf('Netgen\BlockManager\Persistence\Values\Page\Layout', $createdLayout);

        self::assertEquals(3, $createdLayout->id);
        self::assertEquals(1, $createdLayout->parentId);
        self::assertEquals('new_layout', $createdLayout->identifier);

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
     */
    public function testCopyLayout()
    {
        $handler = $this->createHandler();

        $copiedLayout = $handler->copyLayout(1);

        self::assertInstanceOf('Netgen\BlockManager\Persistence\Values\Page\Layout', $copiedLayout);

        self::assertEquals(3, $copiedLayout->id);
        self::assertNull($copiedLayout->parentId);
        self::assertEquals('3_zones_a', $copiedLayout->identifier);

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
        $handler = $this->createHandler();

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

    /**
     * Returns the layout handler under test.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\Layout
     */
    protected function createHandler()
    {
        return new Handler($this->databaseConnection, new Mapper());
    }
}

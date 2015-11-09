<?php

namespace Netgen\BlockManager\Core\Service\Tests;

use Netgen\BlockManager\Core\Values\Page\Zone;

abstract class LayoutServiceTest extends ServiceTest
{
    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::__construct
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadLayout
     */
    public function testLoadLayout()
    {
        $layoutService = $this->createLayoutService();

        $layout = $layoutService->loadLayout(1);

        self::assertInstanceOf('Netgen\BlockManager\API\Values\Page\Layout', $layout);

        self::assertEquals(1, $layout->getId());
        self::assertNull($layout->getParentId());
        self::assertEquals('3_zones_a', $layout->getIdentifier());

        self::assertInstanceOf('DateTime', $layout->getCreated());
        self::assertGreaterThan(0, $layout->getCreated()->getTimestamp());

        self::assertInstanceOf('DateTime', $layout->getModified());
        self::assertGreaterThan(0, $layout->getModified()->getTimestamp());

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
            $layout->getZones()
        );
    }
}

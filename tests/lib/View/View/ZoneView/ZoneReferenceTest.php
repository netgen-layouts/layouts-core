<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\View\ZoneView;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\API\Values\Layout\ZoneList;
use Netgen\BlockManager\View\View\ZoneView\ZoneReference;
use PHPUnit\Framework\TestCase;

final class ZoneReferenceTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Layout\Layout
     */
    private $layout;

    /**
     * @var \Netgen\BlockManager\API\Values\Layout\Zone
     */
    private $zone;

    /**
     * @var \Netgen\BlockManager\View\View\ZoneView\ZoneReference
     */
    private $zoneReference;

    public function setUp(): void
    {
        $this->zone = Zone::fromArray(['identifier' => 'left']);
        $this->layout = Layout::fromArray(['zones' => new ZoneList(['left' => $this->zone])]);
        $this->zoneReference = new ZoneReference($this->layout, 'left');
    }

    /**
     * @covers \Netgen\BlockManager\View\View\ZoneView\ZoneReference::__construct
     * @covers \Netgen\BlockManager\View\View\ZoneView\ZoneReference::getLayout
     */
    public function testGetLayout(): void
    {
        self::assertSame($this->layout, $this->zoneReference->getLayout());
    }

    /**
     * @covers \Netgen\BlockManager\View\View\ZoneView\ZoneReference::getZone
     */
    public function testGetZone(): void
    {
        self::assertSame($this->zone, $this->zoneReference->getZone());
    }
}

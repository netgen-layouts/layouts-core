<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\View\ZoneView;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\API\Values\Layout\ZoneList;
use Netgen\Layouts\View\View\ZoneView\ZoneReference;
use PHPUnit\Framework\TestCase;

final class ZoneReferenceTest extends TestCase
{
    private Layout $layout;

    private Zone $zone;

    private ZoneReference $zoneReference;

    protected function setUp(): void
    {
        $this->zone = Zone::fromArray(['identifier' => 'left']);
        $this->layout = Layout::fromArray(['zones' => new ZoneList(['left' => $this->zone])]);
        $this->zoneReference = new ZoneReference($this->layout, 'left');
    }

    /**
     * @covers \Netgen\Layouts\View\View\ZoneView\ZoneReference::__construct
     * @covers \Netgen\Layouts\View\View\ZoneView\ZoneReference::getLayout
     */
    public function testGetLayout(): void
    {
        self::assertSame($this->layout, $this->zoneReference->getLayout());
    }

    /**
     * @covers \Netgen\Layouts\View\View\ZoneView\ZoneReference::getZone
     */
    public function testGetZone(): void
    {
        self::assertSame($this->zone, $this->zoneReference->getZone());
    }
}

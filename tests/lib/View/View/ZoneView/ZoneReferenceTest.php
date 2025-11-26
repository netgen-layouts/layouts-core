<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\View\ZoneView;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\API\Values\Layout\ZoneList;
use Netgen\Layouts\View\View\ZoneView\ZoneReference;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ZoneReference::class)]
final class ZoneReferenceTest extends TestCase
{
    private Layout $layout;

    private Zone $zone;

    private ZoneReference $zoneReference;

    protected function setUp(): void
    {
        $this->zone = Zone::fromArray(['identifier' => 'left']);
        $this->layout = Layout::fromArray(['zones' => ZoneList::fromArray(['left' => $this->zone])]);
        $this->zoneReference = new ZoneReference($this->layout, 'left');
    }

    public function testGetLayout(): void
    {
        self::assertSame($this->layout, $this->zoneReference->getLayout());
    }

    public function testGetZone(): void
    {
        self::assertSame($this->zone, $this->zoneReference->getZone());
    }
}

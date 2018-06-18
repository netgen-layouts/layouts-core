<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\Layout;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Layout\Zone;
use PHPUnit\Framework\TestCase;

final class ZoneTest extends TestCase
{
    public function testInstance(): void
    {
        $this->assertInstanceOf(Value::class, new Zone());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Layout\Zone::__construct
     * @covers \Netgen\BlockManager\Core\Values\Layout\Zone::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Layout\Zone::getLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Layout\Zone::getLinkedZone
     * @covers \Netgen\BlockManager\Core\Values\Layout\Zone::hasLinkedZone
     */
    public function testSetProperties(): void
    {
        $linkedZone = new Zone();

        $zone = new Zone(
            [
                'identifier' => 'left',
                'layoutId' => 84,
                'linkedZone' => $linkedZone,
            ]
        );

        $this->assertSame('left', $zone->getIdentifier());
        $this->assertSame(84, $zone->getLayoutId());
        $this->assertTrue($zone->hasLinkedZone());
        $this->assertSame($linkedZone, $zone->getLinkedZone());
    }
}

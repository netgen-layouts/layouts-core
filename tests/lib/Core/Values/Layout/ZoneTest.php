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
        self::assertInstanceOf(Value::class, new Zone());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Layout\Zone::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Layout\Zone::getLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Layout\Zone::getLinkedZone
     * @covers \Netgen\BlockManager\Core\Values\Layout\Zone::hasLinkedZone
     */
    public function testSetProperties(): void
    {
        $linkedZone = new Zone();

        $zone = Zone::fromArray(
            [
                'identifier' => 'left',
                'layoutId' => 84,
                'linkedZone' => $linkedZone,
            ]
        );

        self::assertSame('left', $zone->getIdentifier());
        self::assertSame(84, $zone->getLayoutId());
        self::assertTrue($zone->hasLinkedZone());
        self::assertSame($linkedZone, $zone->getLinkedZone());
    }
}

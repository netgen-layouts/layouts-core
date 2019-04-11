<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Layout;

use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\API\Values\Value;
use PHPUnit\Framework\TestCase;

final class ZoneTest extends TestCase
{
    public function testInstance(): void
    {
        self::assertInstanceOf(Value::class, new Zone());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Layout\Zone::getIdentifier
     * @covers \Netgen\Layouts\API\Values\Layout\Zone::getLayoutId
     * @covers \Netgen\Layouts\API\Values\Layout\Zone::getLinkedZone
     * @covers \Netgen\Layouts\API\Values\Layout\Zone::hasLinkedZone
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

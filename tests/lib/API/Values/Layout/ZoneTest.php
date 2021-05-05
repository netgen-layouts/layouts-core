<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Layout;

use Netgen\Layouts\API\Values\Layout\Zone;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class ZoneTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\Layout\Zone::getIdentifier
     * @covers \Netgen\Layouts\API\Values\Layout\Zone::getLayoutId
     * @covers \Netgen\Layouts\API\Values\Layout\Zone::getLinkedZone
     * @covers \Netgen\Layouts\API\Values\Layout\Zone::hasLinkedZone
     */
    public function testSetProperties(): void
    {
        $linkedZone = new Zone();

        $layoutUuid = Uuid::uuid4();

        $zone = Zone::fromArray(
            [
                'identifier' => 'left',
                'layoutId' => $layoutUuid,
                'linkedZone' => $linkedZone,
            ],
        );

        self::assertSame('left', $zone->getIdentifier());
        self::assertSame($layoutUuid, $zone->getLayoutId());
        self::assertTrue($zone->hasLinkedZone());
        self::assertSame($linkedZone, $zone->getLinkedZone());
    }
}

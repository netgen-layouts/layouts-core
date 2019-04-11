<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Layout;

use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\API\Values\Layout\ZoneList;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;

final class ZoneListTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\Layout\ZoneList::__construct
     */
    public function testConstructorWithInvalidType(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            sprintf(
                'Argument 1 passed to %s::%s\{closure}() must be an instance of %s, instance of %s given',
                ZoneList::class,
                str_replace('\ZoneList', '', ZoneList::class),
                Zone::class,
                stdClass::class
            )
        );

        new ZoneList([new Zone(), new stdClass(), new Zone()]);
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Layout\ZoneList::__construct
     * @covers \Netgen\Layouts\API\Values\Layout\ZoneList::getZones
     */
    public function testGetZones(): void
    {
        $zones = [new Zone(), new Zone()];

        self::assertSame($zones, (new ZoneList($zones))->getZones());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Layout\ZoneList::getZoneIdentifiers
     */
    public function testGetZoneIdentifiers(): void
    {
        $zones = [Zone::fromArray(['identifier' => 'left']), Zone::fromArray(['identifier' => 'right'])];

        self::assertSame(['left', 'right'], (new ZoneList($zones))->getZoneIdentifiers());
    }
}

<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Layout;

use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\API\Values\Layout\ZoneList;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;

use function sprintf;
use function str_replace;

final class ZoneListTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\Layout\ZoneList::__construct
     */
    public function testConstructorWithInvalidType(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessageMatches(
            sprintf(
                '/(must be an instance of|must be of type) %s, (instance of )?%s given/',
                str_replace('\\', '\\\\', Zone::class),
                stdClass::class,
            ),
        );

        new ZoneList(['one' => new Zone(), 'two' => new stdClass(), 'three' => new Zone()]);
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Layout\ZoneList::__construct
     * @covers \Netgen\Layouts\API\Values\Layout\ZoneList::getZones
     */
    public function testGetZones(): void
    {
        $zones = ['one' => new Zone(), 'two' => new Zone()];

        self::assertSame($zones, (new ZoneList($zones))->getZones());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Layout\ZoneList::getZoneIdentifiers
     */
    public function testGetZoneIdentifiers(): void
    {
        $zones = [
            'left' => Zone::fromArray(['identifier' => 'left']),
            'right' => Zone::fromArray(['identifier' => 'right']),
        ];

        self::assertSame(['left', 'right'], (new ZoneList($zones))->getZoneIdentifiers());
    }
}

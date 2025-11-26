<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Layout;

use Netgen\Layouts\API\Values\LazyCollection;

use function array_map;
use function array_values;

/**
 * @extends \Netgen\Layouts\API\Values\LazyCollection<string, \Netgen\Layouts\API\Values\Layout\Zone>
 */
final class ZoneList extends LazyCollection
{
    /**
     * @return array<string, \Netgen\Layouts\API\Values\Layout\Zone>
     */
    public function getZones(): array
    {
        return $this->toArray();
    }

    /**
     * @return string[]
     */
    public function getZoneIdentifiers(): array
    {
        return array_values(
            array_map(
                static fn (Zone $zone): string => $zone->identifier,
                $this->getZones(),
            ),
        );
    }
}

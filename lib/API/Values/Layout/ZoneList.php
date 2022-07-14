<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Layout;

use Doctrine\Common\Collections\ArrayCollection;

use function array_filter;
use function array_map;
use function array_values;

/**
 * @extends \Doctrine\Common\Collections\ArrayCollection<string, \Netgen\Layouts\API\Values\Layout\Zone>
 */
final class ZoneList extends ArrayCollection
{
    /**
     * @param array<string, \Netgen\Layouts\API\Values\Layout\Zone> $zones
     */
    public function __construct(array $zones = [])
    {
        parent::__construct(
            array_filter(
                $zones,
                static fn (Zone $zone): bool => true,
            ),
        );
    }

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
                static fn (Zone $zone): string => $zone->getIdentifier(),
                $this->getZones(),
            ),
        );
    }
}

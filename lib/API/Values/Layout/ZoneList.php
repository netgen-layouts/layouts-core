<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Layout;

use Doctrine\Common\Collections\ArrayCollection;

final class ZoneList extends ArrayCollection
{
    public function __construct(array $zones = [])
    {
        parent::__construct(
            array_filter(
                $zones,
                static function (Zone $zone) {
                    return true;
                }
            )
        );
    }

    /**
     * @return \Netgen\BlockManager\API\Values\Layout\Zone[]
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
        return array_map(
            static function (Zone $zone) {
                return $zone->getIdentifier();
            },
            $this->getZones()
        );
    }
}

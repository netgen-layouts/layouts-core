<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

use function array_filter;

/**
 * @implements \IteratorAggregate<string, string[]>
 */
final class ZoneMappings implements IteratorAggregate
{
    /**
     * @var array<string, string[]>
     */
    public private(set) array $zoneMappings = [];

    /**
     * @param string[] $destinationZones
     */
    public function addZoneMapping(string $sourceZone, array $destinationZones): self
    {
        $destinationZones = array_filter(
            $destinationZones,
            static fn (string $zoneIdentifier): bool => true,
        );

        $this->zoneMappings[$sourceZone] = $destinationZones;

        return $this;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->zoneMappings);
    }
}

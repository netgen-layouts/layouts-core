<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Type;

use Netgen\Layouts\Block\BlockDefinitionInterface;
use Netgen\Layouts\Exception\Layout\LayoutTypeException;
use Netgen\Layouts\Utils\HydratorTrait;

use function array_key_exists;
use function array_keys;
use function count;
use function in_array;

final class LayoutType implements LayoutTypeInterface
{
    use HydratorTrait;

    public private(set) string $identifier;

    public private(set) bool $isEnabled;

    public private(set) string $name;

    public private(set) ?string $icon;

    public private(set) array $zones = [];

    public array $zoneIdentifiers {
        get => array_keys($this->zones);
    }

    public function hasZone(string $zoneIdentifier): bool
    {
        return array_key_exists($zoneIdentifier, $this->zones);
    }

    public function getZone(string $zoneIdentifier): Zone
    {
        if (!$this->hasZone($zoneIdentifier)) {
            throw LayoutTypeException::noZone($this->identifier, $zoneIdentifier);
        }

        return $this->zones[$zoneIdentifier];
    }

    public function isBlockAllowedInZone(BlockDefinitionInterface $definition, string $zoneIdentifier): bool
    {
        if (!$this->hasZone($zoneIdentifier)) {
            return true;
        }

        $zone = $this->getZone($zoneIdentifier);

        if (count($zone->allowedBlockDefinitions) === 0) {
            return true;
        }

        return in_array($definition->getIdentifier(), $zone->allowedBlockDefinitions, true);
    }
}

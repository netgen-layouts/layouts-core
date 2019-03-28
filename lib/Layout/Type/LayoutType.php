<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Type;

use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Exception\Layout\LayoutTypeException;
use Netgen\BlockManager\Utils\HydratorTrait;

/**
 * @final
 */
class LayoutType implements LayoutTypeInterface
{
    use HydratorTrait;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var bool
     */
    private $isEnabled;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string|null
     */
    private $icon;

    /**
     * @var \Netgen\BlockManager\Layout\Type\Zone[]
     */
    private $zones = [];

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getZones(): array
    {
        return $this->zones;
    }

    public function getZoneIdentifiers(): array
    {
        return array_keys($this->zones);
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

        $allowedBlockDefinitions = $zone->getAllowedBlockDefinitions();
        if (count($allowedBlockDefinitions) === 0) {
            return true;
        }

        return in_array($definition->getIdentifier(), $allowedBlockDefinitions, true);
    }
}

<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Type;

use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Exception\Layout\LayoutTypeException;
use Netgen\BlockManager\Value;

/**
 * @final
 */
class LayoutType extends Value implements LayoutTypeInterface
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var bool
     */
    protected $isEnabled;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $icon;

    /**
     * @var \Netgen\BlockManager\Layout\Type\Zone[]
     */
    protected $zones = [];

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function isEnabled()
    {
        return $this->isEnabled;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function getZones()
    {
        return $this->zones;
    }

    public function getZoneIdentifiers()
    {
        return array_keys($this->zones);
    }

    public function hasZone($zoneIdentifier)
    {
        return array_key_exists($zoneIdentifier, $this->zones);
    }

    public function getZone($zoneIdentifier)
    {
        if (!$this->hasZone($zoneIdentifier)) {
            throw LayoutTypeException::noZone($this->identifier, $zoneIdentifier);
        }

        return $this->zones[$zoneIdentifier];
    }

    public function isBlockAllowedInZone(BlockDefinitionInterface $definition, $zoneIdentifier)
    {
        if (!$this->hasZone($zoneIdentifier)) {
            return true;
        }

        $zone = $this->getZone($zoneIdentifier);

        $allowedBlockDefinitions = $zone->getAllowedBlockDefinitions();
        if (empty($allowedBlockDefinitions)) {
            return true;
        }

        return in_array($definition->getIdentifier(), $allowedBlockDefinitions, true);
    }
}

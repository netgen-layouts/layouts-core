<?php

namespace Netgen\BlockManager\Configuration\LayoutType;

use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\ValueObject;

class LayoutType extends ValueObject
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Netgen\BlockManager\Configuration\LayoutType\Zone[]
     */
    protected $zones = array();

    /**
     * Returns the layout type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the layout type name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the layout type zones.
     *
     * @return \Netgen\BlockManager\Configuration\LayoutType\Zone[]
     */
    public function getZones()
    {
        return $this->zones;
    }

    /**
     * Returns the layout type zone identifiers.
     *
     * @return string[]
     */
    public function getZoneIdentifiers()
    {
        return array_keys($this->zones);
    }

    /**
     * Returns if the layout type has a zone with provided identifier.
     *
     * @param $zoneIdentifier
     *
     * @return bool
     */
    public function hasZone($zoneIdentifier)
    {
        return isset($this->zones[$zoneIdentifier]);
    }

    /**
     * Returns the zone with provided identifier.
     *
     * @param $zoneIdentifier
     *
     * @throws \InvalidArgumentException If zone does not exist
     *
     * @return \Netgen\BlockManager\Configuration\LayoutType\Zone
     */
    public function getZone($zoneIdentifier)
    {
        if (!$this->hasZone($zoneIdentifier)) {
            throw new InvalidArgumentException(
                'zoneIdentifier',
                sprintf(
                    'Zone "%s" does not exist in "%s" layout type.',
                    $zoneIdentifier,
                    $this->identifier
                )
            );
        }

        return $this->zones[$zoneIdentifier];
    }
}

<?php

namespace Netgen\BlockManager\Layout\Type;

use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\ValueObject;

class LayoutType extends ValueObject
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
     * @var \Netgen\BlockManager\Layout\Type\Zone[]
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
     * Returns if the layout type is enabled or not.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
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
     * @return \Netgen\BlockManager\Layout\Type\Zone[]
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
     * @return \Netgen\BlockManager\Layout\Type\Zone
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

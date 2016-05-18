<?php

namespace Netgen\BlockManager\Configuration\LayoutType;

class LayoutType
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Netgen\BlockManager\Configuration\LayoutType\Zone[]
     */
    protected $zones = array();

    /**
     * Constructor.
     *
     * @param string $identifier
     * @param bool $enabled
     * @param string $name
     * @param \Netgen\BlockManager\Configuration\LayoutType\Zone[] $zones
     */
    public function __construct($identifier, $enabled, $name, array $zones)
    {
        $this->identifier = $identifier;
        $this->enabled = $enabled;
        $this->name = $name;
        $this->zones = $zones;
    }

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
     * Returns if the layout type is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
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
     * @return \Netgen\BlockManager\Configuration\LayoutType\Zone
     */
    public function getZone($zoneIdentifier)
    {
        return $this->zones[$zoneIdentifier];
    }
}

<?php

namespace Netgen\BlockManager\Core\Values\Page;

use Netgen\BlockManager\API\Values\Page\Layout as APILayout;

class Layout extends LayoutInfo implements APILayout
{
    /**
     * @var \Netgen\BlockManager\API\Values\Page\Zone[]
     */
    protected $zones = array();

    /**
     * Returns all zones from the layout.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone[]
     */
    public function getZones()
    {
        return $this->zones;
    }

    /**
     * Returns the specified zone or null if zone does not exist.
     *
     * @param string $zoneIdentifier
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone
     */
    public function getZone($zoneIdentifier)
    {
        return isset($this->zones[$zoneIdentifier]) ? $this->zones[$zoneIdentifier] : null;
    }

    /**
     * Returns if layout has a specified zone.
     *
     * @param string $zoneIdentifier
     *
     * @return bool
     */
    public function hasZone($zoneIdentifier)
    {
        return isset($this->zones[$zoneIdentifier]);
    }
}

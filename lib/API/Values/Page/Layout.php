<?php

namespace Netgen\BlockManager\API\Values\Page;

interface Layout extends LayoutReference
{
    /**
     * @const string
     */
    const STATUS_DRAFT = 0;

    /**
     * @const string
     */
    const STATUS_PUBLISHED = 1;

    /**
     * @const string
     */
    const STATUS_ARCHIVED = 2;

    /**
     * Returns all zones from the layout.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone[]
     */
    public function getZones();

    /**
     * Returns the specified zone or null if zone does not exist.
     *
     * @param string $zoneIdentifier
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone
     */
    public function getZone($zoneIdentifier);

    /**
     * Returns if layout has a specified zone.
     *
     * @param string $zoneIdentifier
     *
     * @return bool
     */
    public function hasZone($zoneIdentifier);
}

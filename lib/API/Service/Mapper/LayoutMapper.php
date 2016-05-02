<?php

namespace Netgen\BlockManager\API\Service\Mapper;

use Netgen\BlockManager\Persistence\Values\Page\Zone;
use Netgen\BlockManager\Persistence\Values\Page\Layout;

interface LayoutMapper
{
    /**
     * Builds the API zone value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Zone $zone
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone
     */
    public function mapZone(Zone $zone);

    /**
     * Builds the API layout value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Layout $layout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function mapLayout(Layout $layout);
}

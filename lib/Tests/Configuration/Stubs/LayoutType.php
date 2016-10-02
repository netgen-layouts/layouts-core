<?php

namespace Netgen\BlockManager\Tests\Configuration\Stubs;

use Netgen\BlockManager\Configuration\LayoutType\LayoutType as BaseLayoutType;
use Netgen\BlockManager\Configuration\LayoutType\Zone;

class LayoutType extends BaseLayoutType
{
    /**
     * Constructor.
     *
     * @param string $identifier
     * @param array $zones
     * @param string $name
     */
    public function __construct($identifier, $zones = array(), $name = null)
    {
        $zoneObjects = array();

        foreach ($zones as $zone => $allowedBlocks) {
            $zoneObjects[$zone] = new Zone($zone, $zone, $allowedBlocks);
        }

        parent::__construct($identifier, $name ?: $identifier, $zoneObjects);
    }
}

<?php

namespace Netgen\BlockManager\Configuration\Factory;

use Netgen\BlockManager\Configuration\LayoutType\LayoutType;
use Netgen\BlockManager\Configuration\LayoutType\Zone;

class LayoutTypeFactory
{
    /**
     * Builds the layout type.
     *
     * @param string $identifier
     * @param array $config
     *
     * @return \Netgen\BlockManager\Configuration\LayoutType\LayoutType
     */
    public static function buildLayoutType($identifier, array $config)
    {
        $zones = array();

        foreach ($config['zones'] as $zoneIdentifier => $zoneConfig) {
            $zones[$zoneIdentifier] = new Zone(
                $zoneIdentifier,
                $zoneConfig['name'],
                $zoneConfig['allowed_block_definitions']
            );
        }

        return new LayoutType($identifier, $config['name'], $zones);
    }
}

<?php

namespace Netgen\BlockManager\Configuration\LayoutType;

class Factory
{
    /**
     * Builds the layout type.
     *
     * @param array $config
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Configuration\LayoutType\LayoutType
     */
    public static function buildLayoutType(array $config, $identifier)
    {
        $zones = array();

        foreach ($config['zones'] as $zoneIdentifier => $zoneConfig) {
            $zones[$zoneIdentifier] = new Zone(
                $zoneIdentifier,
                $config['zones'][$zoneIdentifier]['name'],
                $config['zones'][$zoneIdentifier]['allowed_block_types']
            );
        }

        return new LayoutType($identifier, $config['name'], $zones);
    }
}

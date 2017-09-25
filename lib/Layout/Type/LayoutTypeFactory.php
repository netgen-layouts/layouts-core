<?php

namespace Netgen\BlockManager\Layout\Type;

final class LayoutTypeFactory
{
    /**
     * Builds the layout type.
     *
     * @param string $identifier
     * @param array $config
     *
     * @return \Netgen\BlockManager\Layout\Type\LayoutType
     */
    public static function buildLayoutType($identifier, array $config)
    {
        $zones = array();

        foreach ($config['zones'] as $zoneIdentifier => $zoneConfig) {
            $zones[$zoneIdentifier] = new Zone(
                array(
                    'identifier' => $zoneIdentifier,
                    'name' => $zoneConfig['name'],
                    'allowedBlockDefinitions' => $zoneConfig['allowed_block_definitions'],
                )
            );
        }

        return new LayoutType(
            array(
                'identifier' => $identifier,
                'isEnabled' => $config['enabled'],
                'name' => $config['name'],
                'icon' => $config['icon'],
                'zones' => $zones,
            )
        );
    }
}

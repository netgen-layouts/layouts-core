<?php

namespace Netgen\BlockManager\Layout\Type;

use Netgen\BlockManager\Block\BlockDefinitionInterface;

final class NullLayoutType implements LayoutTypeInterface
{
    public function getIdentifier()
    {
        return 'null';
    }

    public function isEnabled()
    {
        return true;
    }

    public function getName()
    {
        return 'Invalid layout type';
    }

    public function getIcon()
    {
        return '';
    }

    public function getZones()
    {
        return [];
    }

    public function getZoneIdentifiers()
    {
        return [];
    }

    public function hasZone($zoneIdentifier)
    {
        return false;
    }

    public function getZone($zoneIdentifier)
    {
    }

    public function isBlockAllowedInZone(BlockDefinitionInterface $definition, $zoneIdentifier)
    {
        return true;
    }
}

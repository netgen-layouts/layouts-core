<?php

namespace Netgen\BlockManager\Layout\Type;

use Netgen\BlockManager\Block\BlockDefinitionInterface;

/**
 * This class and corresponding namespace represent an model of layout type
 * as specified in configuration. In addition to layout type identifier, name
 * and icon, it provides a list of zones available in the layout type.
 */
interface LayoutTypeInterface
{
    /**
     * Returns the layout type identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Returns if the layout type is enabled or not.
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * Returns the layout type name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the layout type icon.
     *
     * @return string
     */
    public function getIcon();

    /**
     * Returns the layout type zones.
     *
     * @return \Netgen\BlockManager\Layout\Type\Zone[]
     */
    public function getZones();

    /**
     * Returns the layout type zone identifiers.
     *
     * @return string[]
     */
    public function getZoneIdentifiers();

    /**
     * Returns if the layout type has a zone with provided identifier.
     *
     * @param $zoneIdentifier
     *
     * @return bool
     */
    public function hasZone($zoneIdentifier);

    /**
     * Returns the zone with provided identifier.
     *
     * @param $zoneIdentifier
     *
     * @throws \Netgen\BlockManager\Exception\Layout\LayoutTypeException If zone does not exist
     *
     * @return \Netgen\BlockManager\Layout\Type\Zone
     */
    public function getZone($zoneIdentifier);

    /**
     * Returns if the block is allowed within the provided zone.
     *
     * @param \Netgen\BlockManager\Block\BlockDefinitionInterface $definition
     * @param string $zoneIdentifier
     *
     * @return bool
     */
    public function isBlockAllowedInZone(BlockDefinitionInterface $definition, $zoneIdentifier);
}

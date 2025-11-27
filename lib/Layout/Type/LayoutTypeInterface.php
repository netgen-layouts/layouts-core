<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Type;

use Netgen\Layouts\Block\BlockDefinitionInterface;

/**
 * This class and corresponding namespace represent an model of layout type
 * as specified in configuration. In addition to layout type identifier, name
 * and icon, it provides a list of zones available in the layout type.
 */
interface LayoutTypeInterface
{
    /**
     * Returns the layout type identifier.
     */
    public string $identifier { get; }

    /**
     * Returns if the layout type is enabled or not.
     */
    public bool $isEnabled { get; }

    /**
     * Returns the layout type name.
     */
    public string $name { get; }

    /**
     * Returns the layout type icon.
     */
    public ?string $icon { get; }

    /**
     * Returns the layout type zones.
     *
     * @var \Netgen\Layouts\Layout\Type\Zone[]
     */
    public array $zones { get; }

    /**
     * Returns the layout type zone identifiers.
     *
     * @var string[]
     */
    public array $zoneIdentifiers { get; }

    /**
     * Returns if the layout type has a zone with provided identifier.
     */
    public function hasZone(string $zoneIdentifier): bool;

    /**
     * Returns the zone with provided identifier.
     *
     * @throws \Netgen\Layouts\Exception\Layout\LayoutTypeException If zone does not exist
     */
    public function getZone(string $zoneIdentifier): Zone;

    /**
     * Returns if the block is allowed within the provided zone.
     */
    public function isBlockAllowedInZone(BlockDefinitionInterface $definition, string $zoneIdentifier): bool;
}

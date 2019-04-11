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
    public function getIdentifier(): string;

    /**
     * Returns if the layout type is enabled or not.
     */
    public function isEnabled(): bool;

    /**
     * Returns the layout type name.
     */
    public function getName(): string;

    /**
     * Returns the layout type icon.
     */
    public function getIcon(): ?string;

    /**
     * Returns the layout type zones.
     *
     * @return \Netgen\Layouts\Layout\Type\Zone[]
     */
    public function getZones(): array;

    /**
     * Returns the layout type zone identifiers.
     *
     * @return string[]
     */
    public function getZoneIdentifiers(): array;

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

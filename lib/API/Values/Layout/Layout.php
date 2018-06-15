<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Layout;

use ArrayAccess;
use Countable;
use DateTimeInterface;
use IteratorAggregate;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Layout\Type\LayoutTypeInterface;

interface Layout extends Value, ArrayAccess, IteratorAggregate, Countable
{
    /**
     * Returns the layout ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the layout type.
     */
    public function getLayoutType(): LayoutTypeInterface;

    /**
     * Returns human readable name of the layout.
     */
    public function getName(): string;

    /**
     * Return human readable description of the layout.
     */
    public function getDescription(): ?string;

    /**
     * Returns when was the layout first created.
     */
    public function getCreated(): DateTimeInterface;

    /**
     * Returns when was the layout last updated.
     */
    public function getModified(): DateTimeInterface;

    /**
     * Returns if the layout is shared.
     */
    public function isShared(): bool;

    /**
     * Returns the main locale of the layout.
     * public function getMainLocale(): string;
     *
     * /**
     * Returns the list of all available locales in the layout.
     *
     * @return string[]
     */
    public function getAvailableLocales(): array;

    /**
     * Returns if the layout has the provided locale.
     */
    public function hasLocale(string $locale): bool;

    /**
     * Returns all zones from the layout.
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Zone[]
     */
    public function getZones(): array;

    /**
     * Returns the specified zone or null if zone does not exist.
     *
     * By default, this method will return the linked zone if the
     * requested zone has one. Set $ignoreLinkedZone to true to
     * always return the original zone.
     */
    public function getZone(string $zoneIdentifier, bool $ignoreLinkedZone = false): ?Zone;

    /**
     * Returns if layout has a specified zone.
     */
    public function hasZone(string $zoneIdentifier): bool;
}

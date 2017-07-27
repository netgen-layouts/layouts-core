<?php

namespace Netgen\BlockManager\API\Values\Layout;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\BlockManager\API\Values\Value;

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
     *
     * @return \Netgen\BlockManager\Layout\Type\LayoutType
     */
    public function getLayoutType();

    /**
     * Returns the layout human readable name.
     *
     * @return string
     */
    public function getName();

    /**
     * Return human readable description of the layout.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Returns when was the layout created.
     *
     * @return \DateTime
     */
    public function getCreated();

    /**
     * Returns when was the layout last updated.
     *
     * @return \DateTime
     */
    public function getModified();

    /**
     * Returns if the layout is published.
     *
     * @return bool
     */
    public function isPublished();

    /**
     * Returns if the layout is shared.
     *
     * @return bool
     */
    public function isShared();

    /**
     * Returns the main locale for the layout.
     *
     * @return string
     */
    public function getMainLocale();

    /**
     * Returns the list of all available locales in the layout.
     *
     * @return string[]
     */
    public function getAvailableLocales();

    /**
     * Returns all zones from the layout.
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Zone[]
     */
    public function getZones();

    /**
     * Returns the specified zone or null if zone does not exist.
     *
     * By default, this method will return the linked zone if the requested zone has one.
     *
     * @param string $zoneIdentifier
     * @param bool $ignoreLinkedZone
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Zone
     */
    public function getZone($zoneIdentifier, $ignoreLinkedZone = false);

    /**
     * Returns if layout has a specified zone.
     *
     * @param string $zoneIdentifier
     *
     * @return bool
     */
    public function hasZone($zoneIdentifier);
}

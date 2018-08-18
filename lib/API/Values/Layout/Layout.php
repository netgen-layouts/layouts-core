<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Layout;

use ArrayAccess;
use Countable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use IteratorAggregate;
use Netgen\BlockManager\API\Values\Layout\Zone as APIZone;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\API\Values\ValueStatusTrait;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Layout\Type\LayoutTypeInterface;
use Netgen\BlockManager\Utils\HydratorTrait;

final class Layout implements Value, ArrayAccess, IteratorAggregate, Countable
{
    use HydratorTrait;
    use ValueStatusTrait;

    /**
     * @var int|string
     */
    private $id;

    /**
     * @var \Netgen\BlockManager\Layout\Type\LayoutTypeInterface
     */
    private $layoutType;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var \DateTimeInterface
     */
    private $created;

    /**
     * @var \DateTimeInterface
     */
    private $modified;

    /**
     * @var bool
     */
    private $shared;

    /**
     * @var string
     */
    private $mainLocale;

    /**
     * @var string[]
     */
    private $availableLocales = [];

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $zones;

    public function __construct()
    {
        $this->zones = $this->zones ?? new ArrayCollection();
    }

    /**
     * Returns the layout ID.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the layout type.
     */
    public function getLayoutType(): LayoutTypeInterface
    {
        return $this->layoutType;
    }

    /**
     * Returns human readable name of the layout.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Return human readable description of the layout.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Returns when was the layout first created.
     */
    public function getCreated(): DateTimeInterface
    {
        return $this->created;
    }

    /**
     * Returns when was the layout last updated.
     */
    public function getModified(): DateTimeInterface
    {
        return $this->modified;
    }

    /**
     * Returns if the layout is shared.
     */
    public function isShared(): bool
    {
        return $this->shared;
    }

    /**
     * Returns the main locale of the layout.
     */
    public function getMainLocale(): string
    {
        return $this->mainLocale;
    }

    /**
     * Returns the list of all available locales in the layout.
     *
     * @return string[]
     */
    public function getAvailableLocales(): array
    {
        return $this->availableLocales;
    }

    /**
     * Returns if the layout has the provided locale.
     */
    public function hasLocale(string $locale): bool
    {
        return in_array($locale, $this->availableLocales, true);
    }

    /**
     * Returns all zones from the layout.
     *
     * @return \Netgen\BlockManager\API\Values\Layout\ZoneList
     */
    public function getZones(): ZoneList
    {
        return new ZoneList($this->zones->toArray());
    }

    /**
     * Returns the specified zone or null if zone does not exist.
     *
     * By default, this method will return the linked zone if the
     * requested zone has one. Set $ignoreLinkedZone to true to
     * always return the original zone.
     */
    public function getZone(string $zoneIdentifier, bool $ignoreLinkedZone = false): ?APIZone
    {
        if ($this->hasZone($zoneIdentifier)) {
            if (!$ignoreLinkedZone && $this->zones->get($zoneIdentifier)->hasLinkedZone()) {
                return $this->zones->get($zoneIdentifier)->getLinkedZone();
            }

            return $this->zones->get($zoneIdentifier);
        }

        return null;
    }

    /**
     * Returns if layout has a specified zone.
     */
    public function hasZone(string $zoneIdentifier): bool
    {
        return $this->zones->containsKey($zoneIdentifier);
    }

    public function getIterator()
    {
        return $this->zones->getIterator();
    }

    public function count()
    {
        return $this->zones->count();
    }

    public function offsetExists($offset)
    {
        return $this->zones->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->zones->offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('Method call not supported.');
    }

    public function offsetUnset($offset)
    {
        throw new RuntimeException('Method call not supported.');
    }
}

<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Layout;

use ArrayAccess;
use Countable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use IteratorAggregate;
use Netgen\Layouts\API\Values\Layout\Zone as APIZone;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Exception\API\LayoutException;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Layout\Type\LayoutTypeInterface;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;
use Traversable;

final class Layout implements Value, ArrayAccess, IteratorAggregate, Countable
{
    use HydratorTrait;
    use ValueStatusTrait;

    /**
     * @var \Ramsey\Uuid\UuidInterface
     */
    private $id;

    /**
     * @var \Netgen\Layouts\Layout\Type\LayoutTypeInterface
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
     * Returns the layout UUID.
     */
    public function getId(): UuidInterface
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
     */
    public function getZones(): ZoneList
    {
        return new ZoneList($this->zones->toArray());
    }

    /**
     * Returns the specified zone.
     *
     * @throws \Netgen\Layouts\Exception\API\LayoutException If the zone does not exist
     */
    public function getZone(string $zoneIdentifier): APIZone
    {
        if ($this->hasZone($zoneIdentifier)) {
            return $this->zones->get($zoneIdentifier);
        }

        throw LayoutException::noZone($zoneIdentifier);
    }

    /**
     * Returns if layout has a specified zone.
     */
    public function hasZone(string $zoneIdentifier): bool
    {
        return $this->zones->containsKey($zoneIdentifier);
    }

    public function getIterator(): Traversable
    {
        return $this->zones->getIterator();
    }

    public function count(): int
    {
        return $this->zones->count();
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->zones->offsetExists($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->zones->offsetGet($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        throw new RuntimeException('Method call not supported.');
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        throw new RuntimeException('Method call not supported.');
    }
}

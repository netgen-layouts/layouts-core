<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Layout;

use ArrayAccess;
use Countable;
use DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use IteratorAggregate;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Exception\API\LayoutException;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Layout\Type\LayoutTypeInterface;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;
use Traversable;

use function in_array;

/**
 * @implements \IteratorAggregate<string, \Netgen\Layouts\API\Values\Layout\Zone>
 * @implements \ArrayAccess<string, \Netgen\Layouts\API\Values\Layout\Zone>
 */
final class Layout implements Value, ArrayAccess, IteratorAggregate, Countable
{
    use HydratorTrait;
    use ValueStatusTrait;

    private UuidInterface $id;

    private LayoutTypeInterface $layoutType;

    private string $name;

    private string $description;

    private DateTimeInterface $created;

    private DateTimeInterface $modified;

    private bool $shared;

    private string $mainLocale;

    /**
     * @var string[]
     */
    private array $availableLocales;

    /**
     * @var \Doctrine\Common\Collections\Collection<string, \Netgen\Layouts\API\Values\Layout\Zone>
     */
    private Collection $zones;

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
    public function getZone(string $zoneIdentifier): Zone
    {
        $zone = $this->zones->get($zoneIdentifier);
        if ($zone === null) {
            throw LayoutException::noZone($zoneIdentifier);
        }

        return $zone;
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
     */
    public function offsetExists($offset): bool
    {
        return $this->zones->offsetExists($offset);
    }

    /**
     * @param mixed $offset
     */
    public function offsetGet($offset): ?Zone
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

<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Values\Layout;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Values\Layout\Layout as APILayout;
use Netgen\BlockManager\API\Values\Layout\Zone as APIZone;
use Netgen\BlockManager\API\Values\Layout\ZoneList;
use Netgen\BlockManager\Core\Values\ValueStatusTrait;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Layout\Type\LayoutTypeInterface;
use Netgen\BlockManager\Utils\HydratorTrait;

final class Layout implements APILayout
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

    public function getId()
    {
        return $this->id;
    }

    public function getLayoutType(): LayoutTypeInterface
    {
        return $this->layoutType;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCreated(): DateTimeInterface
    {
        return $this->created;
    }

    public function getModified(): DateTimeInterface
    {
        return $this->modified;
    }

    public function isShared(): bool
    {
        return $this->shared;
    }

    public function getMainLocale(): string
    {
        return $this->mainLocale;
    }

    public function getAvailableLocales(): array
    {
        return $this->availableLocales;
    }

    public function hasLocale(string $locale): bool
    {
        return in_array($locale, $this->availableLocales, true);
    }

    public function getZones(): ZoneList
    {
        return new ZoneList($this->zones->toArray());
    }

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

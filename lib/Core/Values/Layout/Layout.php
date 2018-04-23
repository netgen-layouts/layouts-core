<?php

namespace Netgen\BlockManager\Core\Values\Layout;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Values\Layout\Layout as APILayout;
use Netgen\BlockManager\Core\Values\Value;
use Netgen\BlockManager\Exception\RuntimeException;

final class Layout extends Value implements APILayout
{
    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var \Netgen\BlockManager\Layout\Type\LayoutTypeInterface
     */
    protected $layoutType;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var \DateTimeInterface
     */
    protected $created;

    /**
     * @var \DateTimeInterface
     */
    protected $modified;

    /**
     * @var bool
     */
    protected $shared;

    /**
     * @var string
     */
    protected $mainLocale;

    /**
     * @var string[]
     */
    protected $availableLocales = [];

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $zones;

    public function __construct(array $properties = [])
    {
        parent::__construct($properties);

        if ($this->zones === null) {
            $this->zones = new ArrayCollection();
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLayoutType()
    {
        return $this->layoutType;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getModified()
    {
        return $this->modified;
    }

    public function isShared()
    {
        return $this->shared;
    }

    public function getMainLocale()
    {
        return $this->mainLocale;
    }

    public function getAvailableLocales()
    {
        return $this->availableLocales;
    }

    public function hasLocale($locale)
    {
        return in_array($locale, $this->availableLocales, true);
    }

    public function getZones()
    {
        return $this->zones->toArray();
    }

    public function getZone($zoneIdentifier, $ignoreLinkedZone = false)
    {
        if ($this->hasZone($zoneIdentifier)) {
            if (!$ignoreLinkedZone && $this->zones->get($zoneIdentifier)->hasLinkedZone()) {
                return $this->zones->get($zoneIdentifier)->getLinkedZone();
            }

            return $this->zones->get($zoneIdentifier);
        }
    }

    public function hasZone($zoneIdentifier)
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

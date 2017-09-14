<?php

namespace Netgen\BlockManager\Core\Values\Layout;

use ArrayIterator;
use Netgen\BlockManager\API\Values\Layout\Layout as APILayout;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\ValueObject;

class Layout extends ValueObject implements APILayout
{
    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var \Netgen\BlockManager\Layout\Type\LayoutType
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
     * @var \DateTime
     */
    protected $created;

    /**
     * @var \DateTime
     */
    protected $modified;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var bool
     */
    protected $published;

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
    protected $availableLocales = array();

    /**
     * @var \Netgen\BlockManager\API\Values\Layout\Zone[]
     */
    protected $zones = array();

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

    public function getStatus()
    {
        return $this->status;
    }

    public function isPublished()
    {
        return $this->published;
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
        return $this->zones;
    }

    public function getZone($zoneIdentifier, $ignoreLinkedZone = false)
    {
        if (isset($this->zones[$zoneIdentifier])) {
            $linkedZone = $this->zones[$zoneIdentifier]->getLinkedZone();
            if ($linkedZone instanceof Zone && !$ignoreLinkedZone) {
                return $linkedZone;
            }

            return $this->zones[$zoneIdentifier];
        }
    }

    public function hasZone($zoneIdentifier)
    {
        return isset($this->zones[$zoneIdentifier]);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->zones);
    }

    public function count()
    {
        return count($this->zones);
    }

    public function offsetExists($offset)
    {
        return isset($this->zones[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->zones[$offset];
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

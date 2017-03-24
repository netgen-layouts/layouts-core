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
     * @var \Netgen\BlockManager\API\Values\Layout\Zone[]
     */
    protected $zones = array();

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
     *
     * @return \Netgen\BlockManager\Layout\Type\LayoutType
     */
    public function getLayoutType()
    {
        return $this->layoutType;
    }

    /**
     * Returns the layout human readable name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns when was the layout created.
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Returns when was the layout last updated.
     *
     * @return \DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Returns the status of the layout.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns if the layout is published.
     *
     * @return bool
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * Returns if the layout is shared.
     *
     * @return bool
     */
    public function isShared()
    {
        return $this->shared;
    }

    /**
     * Returns all zones from the layout.
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Zone[]
     */
    public function getZones()
    {
        return $this->zones;
    }

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

    /**
     * Returns if layout has a specified zone.
     *
     * @param string $zoneIdentifier
     *
     * @return bool
     */
    public function hasZone($zoneIdentifier)
    {
        return isset($this->zones[$zoneIdentifier]);
    }

    /**
     * Retrieve an external iterator.
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->zones);
    }

    /**
     * Count elements of an object.
     *
     * @return int
     */
    public function count()
    {
        return count($this->zones);
    }

    /**
     * Whether a offset exists.
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->zones[$offset]);
    }

    /**
     * Offset to retrieve.
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->zones[$offset];
    }

    /**
     * Offset to set.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('Method call not supported.');
    }

    /**
     * Offset to unset.
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        throw new RuntimeException('Method call not supported.');
    }
}

<?php

namespace Netgen\BlockManager\Core\Values\Page;

use ArrayIterator;
use Netgen\BlockManager\API\Values\Page\Zone as APIZone;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\ValueObject;

class Zone extends ValueObject implements APIZone
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var int|string
     */
    protected $layoutId;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var bool
     */
    protected $published;

    /**
     * @var \Netgen\BlockManager\API\Values\Page\Zone
     */
    protected $linkedZone;

    /**
     * @var array
     */
    protected $blocks = array();

    /**
     * Returns zone identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the layout ID to which this zone belongs.
     *
     * @return int|string
     */
    public function getLayoutId()
    {
        return $this->layoutId;
    }

    /**
     * Returns the status of the zone.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns if the zone is published.
     *
     * @return bool
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * Returns the linked zone.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone
     */
    public function getLinkedZone()
    {
        return $this->linkedZone;
    }

    /**
     * Returns zone blocks.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block[]
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * Retrieve an external iterator.
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->blocks);
    }

    /**
     * Count elements of an object.
     *
     * @return int
     */
    public function count()
    {
        return count($this->blocks);
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
        return isset($this->blocks[$offset]);
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
        return $this->blocks[$offset];
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

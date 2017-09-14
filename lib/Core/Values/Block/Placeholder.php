<?php

namespace Netgen\BlockManager\Core\Values\Block;

use ArrayIterator;
use Netgen\BlockManager\API\Values\Block\Placeholder as APIPlaceholder;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\ValueObject;

/**
 * Placeholder represents a set of blocks inside a container block.
 *
 * Each container block can have multiple placeholders, allowing to render
 * each block set separately.
 */
class Placeholder extends ValueObject implements APIPlaceholder
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var \Netgen\BlockManager\API\Values\Block\Block[]
     */
    protected $blocks = array();

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getBlocks()
    {
        return $this->blocks;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->blocks);
    }

    public function count()
    {
        return count($this->blocks);
    }

    public function offsetExists($offset)
    {
        return isset($this->blocks[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->blocks[$offset];
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

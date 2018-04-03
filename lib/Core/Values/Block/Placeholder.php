<?php

namespace Netgen\BlockManager\Core\Values\Block;

use Netgen\BlockManager\API\Values\Block\Placeholder as APIPlaceholder;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Value;

/**
 * Placeholder represents a set of blocks inside a container block.
 *
 * Each container block can have multiple placeholders, allowing to render
 * each block set separately.
 */
final class Placeholder extends Value implements APIPlaceholder
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $blocks;

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
        return $this->getBlocks()->getIterator();
    }

    public function count()
    {
        return $this->blocks->count();
    }

    public function offsetExists($offset)
    {
        return $this->blocks->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->blocks->offsetGet($offset);
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

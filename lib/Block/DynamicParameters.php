<?php

namespace Netgen\BlockManager\Block;

use ArrayAccess;
use Countable;

class DynamicParameters implements ArrayAccess, Countable
{
    /**
     * @var array
     */
    protected $dynamicParameters = array();

    /**
     * Count elements of an object.
     *
     * @return int
     */
    public function count()
    {
        return count($this->dynamicParameters);
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
        return array_key_exists($offset, $this->dynamicParameters);
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
        if (!$this->offsetExists($offset)) {
            return null;
        }

        if (!is_callable($this->dynamicParameters[$offset])) {
            return $this->dynamicParameters[$offset];
        }

        return $this->dynamicParameters[$offset]();
    }

    /**
     * Offset to set.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->dynamicParameters[$offset] = $value;
    }

    /**
     * Offset to unset.
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        if (!$this->offsetExists($offset)) {
            return;
        }

        unset($this->dynamicParameters[$offset]);
    }
}

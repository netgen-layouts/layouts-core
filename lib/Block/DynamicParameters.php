<?php

namespace Netgen\BlockManager\Block;

use ArrayAccess;
use Countable;

final class DynamicParameters implements ArrayAccess, Countable
{
    /**
     * @var array
     */
    private $dynamicParameters = [];

    public function count()
    {
        return count($this->dynamicParameters);
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->dynamicParameters);
    }

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

    public function offsetSet($offset, $value)
    {
        $this->dynamicParameters[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if (!$this->offsetExists($offset)) {
            return;
        }

        unset($this->dynamicParameters[$offset]);
    }
}

<?php

namespace Netgen\BlockManager\Tests\Item\Stubs;

use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Item\ValueLoaderInterface;

class ValueLoader implements ValueLoaderInterface
{
    protected $throwException = false;

    /**
     * Constructor.
     *
     * @param bool $throwException
     */
    public function __construct($throwException = false)
    {
        $this->throwException = $throwException;
    }

    /**
     * Loads the value from provided ID.
     *
     * @param int|string $id
     *
     * @throws \Netgen\BlockManager\Exception\Item\ItemException If value cannot be loaded
     *
     * @return mixed
     */
    public function load($id)
    {
        if ($this->throwException) {
            throw ItemException::noValue($id);
        }

        return new Value($id);
    }
}

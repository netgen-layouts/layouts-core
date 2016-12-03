<?php

namespace Netgen\BlockManager\Tests\Item\Stubs;

use Netgen\BlockManager\Exception\InvalidItemException;
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
     * Returns the value type this loader loads.
     *
     * @return string
     */
    public function getValueType()
    {
        return 'value';
    }

    /**
     * Loads the value from provided ID.
     *
     * @param int|string $id
     *
     * @throws \Netgen\BlockManager\Exception\InvalidItemException If value cannot be loaded
     *
     * @return mixed
     */
    public function load($id)
    {
        if ($this->throwException) {
            throw new InvalidItemException();
        }

        return new Value($id);
    }
}

<?php

namespace Netgen\BlockManager\Tests\Item\Stubs;

use Netgen\BlockManager\Item\ValueLoaderInterface;
use RuntimeException;

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
     * @throws \RuntimeException If value cannot be loaded
     *
     * @return mixed
     */
    public function load($id)
    {
        if ($this->throwException) {
            throw new RuntimeException();
        }

        return new Value($id);
    }
}

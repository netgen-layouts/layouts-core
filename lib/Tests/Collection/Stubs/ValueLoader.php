<?php

namespace Netgen\BlockManager\Tests\Collection\Stubs;

use Netgen\BlockManager\Collection\ValueLoaderInterface;

class ValueLoader implements ValueLoaderInterface
{
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
     * @return mixed
     */
    public function load($id)
    {
        return new Value($id);
    }
}

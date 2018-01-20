<?php

namespace Netgen\BlockManager\Tests\Item\Stubs;

use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Item\ValueLoaderInterface;

final class ValueLoader implements ValueLoaderInterface
{
    private $throwException = false;

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

        return new Value($id, '');
    }

    /**
     * Loads the value from provided remote ID.
     *
     * @param int|string $remoteId
     *
     * @throws \Netgen\BlockManager\Exception\Item\ItemException If value cannot be loaded
     *
     * @return mixed
     */
    public function loadByRemoteId($remoteId)
    {
        if ($this->throwException) {
            throw ItemException::noValue($remoteId);
        }

        return new Value(0, $remoteId);
    }
}

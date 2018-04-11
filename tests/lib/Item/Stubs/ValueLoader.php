<?php

namespace Netgen\BlockManager\Tests\Item\Stubs;

use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Item\ValueLoaderInterface;

final class ValueLoader implements ValueLoaderInterface
{
    private $throwException = false;

    public function __construct($throwException = false)
    {
        $this->throwException = $throwException;
    }

    public function load($id)
    {
        if ($this->throwException) {
            throw ItemException::noValue($id);
        }

        return new Value($id, '');
    }

    public function loadByRemoteId($remoteId)
    {
        if ($this->throwException) {
            throw ItemException::noValue($remoteId);
        }

        return new Value(0, $remoteId);
    }
}

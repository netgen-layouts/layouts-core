<?php

namespace Netgen\BlockManager\Serializer\Values;

/**
 * Represents a serialized entity.
 */
interface ValueInterface
{
    /**
     * Returns the value.
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Returns the status code.
     *
     * @return int
     */
    public function getStatusCode();
}

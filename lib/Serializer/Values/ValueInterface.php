<?php

namespace Netgen\BlockManager\Serializer\Values;

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

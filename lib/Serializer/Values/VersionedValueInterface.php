<?php

namespace Netgen\BlockManager\Serializer\Values;

interface VersionedValueInterface extends ValueInterface
{
    /**
     * Returns the API version.
     *
     * @return int
     */
    public function getVersion();
}

<?php

namespace Netgen\BlockManager\API\Values;

interface Value
{
    /**
     * @const int
     */
    const STATUS_DRAFT = 0;

    /**
     * @const int
     */
    const STATUS_PUBLISHED = 1;

    /**
     * @const int
     */
    const STATUS_ARCHIVED = 2;

    /**
     * Returns the status of the value.
     *
     * @return int
     */
    public function getStatus();
}

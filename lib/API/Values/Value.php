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
     * A value can have one of three statuses: draft, published or archived.
     *
     * @return int
     */
    public function getStatus();

    /**
     * Returns if the value is published.
     *
     * @return bool
     */
    public function isPublished();
}

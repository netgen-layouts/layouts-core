<?php

namespace Netgen\BlockManager\Core\Values;

use Netgen\BlockManager\API\Values\Value as APIValue;
use Netgen\BlockManager\Value as BaseValue;

abstract class Value extends BaseValue implements APIValue
{
    /**
     * @var int
     */
    protected $status;

    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns if the value is published.
     *
     * @return bool
     */
    public function isPublished()
    {
        return $this->status === self::STATUS_PUBLISHED;
    }
}

<?php

declare(strict_types=1);

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

    public function isDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isPublished()
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isArchived()
    {
        return $this->status === self::STATUS_ARCHIVED;
    }
}

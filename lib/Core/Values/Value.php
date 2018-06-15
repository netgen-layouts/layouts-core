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

    public function getStatus(): int
    {
        return $this->status;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }
}

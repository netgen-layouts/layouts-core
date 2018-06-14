<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values;

use Netgen\BlockManager\Value as BaseValue;

/**
 * Classes extending this class represent a versionable entity.
 */
abstract class Value extends BaseValue
{
    /**
     * @const int
     */
    public const STATUS_DRAFT = 0;

    /**
     * @const int
     */
    public const STATUS_PUBLISHED = 1;

    /**
     * @const int
     */
    public const STATUS_ARCHIVED = 2;
}

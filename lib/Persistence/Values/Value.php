<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values;

use Netgen\BlockManager\Value as BaseValue;

/**
 * Classes extending this class represent a versionable entity.
 */
abstract class Value extends BaseValue
{
    public const STATUS_DRAFT = 0;

    public const STATUS_PUBLISHED = 1;

    public const STATUS_ARCHIVED = 2;

    /**
     * Status of the value. One of self::STATUS_* flags.
     *
     * @var int
     */
    public $status;
}

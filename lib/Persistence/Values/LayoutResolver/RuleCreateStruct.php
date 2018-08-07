<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\LayoutResolver;

use Netgen\BlockManager\Utils\HydratorTrait;

final class RuleCreateStruct
{
    use HydratorTrait;

    /**
     * ID of the layout mapped to new rule.
     *
     * @var int|string|null
     */
    public $layoutId;

    /**
     * Priority of the new rule.
     *
     * @var int|null
     */
    public $priority;

    /**
     * Flag indicating if the new rule will be enabled.
     *
     * @var bool
     */
    public $enabled;

    /**
     * Human readable comment of the rule.
     *
     * @var string|null
     */
    public $comment;

    /**
     * Rule status. One of self::STATUS_* flags.
     *
     * @var int
     */
    public $status;
}

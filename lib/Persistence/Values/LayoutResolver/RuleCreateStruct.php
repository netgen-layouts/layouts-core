<?php

namespace Netgen\BlockManager\Persistence\Values\LayoutResolver;

use Netgen\BlockManager\Value;

final class RuleCreateStruct extends Value
{
    /**
     * ID of the layout mapped to new rule.
     *
     * @var int|string
     */
    public $layoutId;

    /**
     * Priority of the new rule.
     *
     * @var int
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
     * @var string
     */
    public $comment;

    /**
     * Rule status. One of self::STATUS_* flags.
     *
     * @var int
     */
    public $status;
}

<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Utils\HydratorTrait;

final class RuleCreateStruct
{
    use HydratorTrait;

    /**
     * Rule UUID. If specified, rule will be created with this UUID if not
     * already taken by an existing rule.
     *
     * @var string|null
     */
    public $uuid;

    /**
     * UUID of the layout mapped to new rule.
     *
     * @var string|null
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

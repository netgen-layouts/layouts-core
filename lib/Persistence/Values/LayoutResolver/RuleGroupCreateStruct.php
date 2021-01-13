<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Utils\HydratorTrait;

final class RuleGroupCreateStruct
{
    use HydratorTrait;

    /**
     * Rule group UUID. If specified, rule group will be created with this UUID if not
     * already taken by an existing rule group.
     *
     * @var string|null
     */
    public $uuid;

    /**
     * Priority of the new rule group.
     *
     * @var int|null
     */
    public $priority;

    /**
     * Flag indicating if the new rule group will be enabled.
     *
     * @var bool
     */
    public $enabled;

    /**
     * Human readable comment of the rule group.
     *
     * @var string|null
     */
    public $comment;

    /**
     * Rule group status. One of self::STATUS_* flags.
     *
     * @var int
     */
    public $status;
}

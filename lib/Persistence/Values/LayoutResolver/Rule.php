<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Persistence\Values\Value;
use Netgen\Layouts\Utils\HydratorTrait;

final class Rule extends Value
{
    use HydratorTrait;

    /**
     * Rule ID.
     *
     * @var int
     */
    public $id;

    /**
     * Rule UUID.
     *
     * @var string
     */
    public $uuid;

    /**
     * ID of the rule group where this rule belongs.
     *
     * @var int
     */
    public $ruleId;

    /**
     * UUID of the rule group where this rule belongs.
     *
     * @var string
     */
    public $ruleGroupUuid;

    /**
     * UUID of the layout mapped to this rule. Can be null if there's no mapped layout.
     *
     * @var string|null
     */
    public $layoutUuid;

    /**
     * A flag indicating if the rule is enabled or not.
     *
     * @var bool
     */
    public $enabled;

    /**
     * Rule priority.
     *
     * @var int
     */
    public $priority;

    /**
     * Human readable comment of the rule.
     *
     * @var string
     */
    public $comment;
}

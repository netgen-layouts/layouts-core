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
     * @var int|string
     */
    public $id;

    /**
     * ID of the layout mapped to this rule. Can be null if there's no mapped layout.
     *
     * @var int|null
     */
    public $layoutId;

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

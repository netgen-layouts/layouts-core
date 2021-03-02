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
     */
    public int $id;

    /**
     * Rule UUID.
     */
    public string $uuid;

    /**
     * ID of the rule group where this rule belongs.
     */
    public int $ruleGroupId;

    /**
     * UUID of the layout mapped to this rule. Can be null if there's no mapped layout.
     */
    public ?string $layoutUuid;

    /**
     * A flag indicating if the rule is enabled or not.
     */
    public bool $enabled;

    /**
     * Rule priority.
     */
    public int $priority;

    /**
     * Human readable description of the rule.
     */
    public string $description;
}

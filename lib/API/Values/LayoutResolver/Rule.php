<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;

final class Rule implements Value
{
    use HydratorTrait;
    use ValueStatusTrait;

    public private(set) UuidInterface $id;

    /**
     * Returns the UUID of the rule group where this rule belongs.
     */
    public private(set) UuidInterface $ruleGroupId;

    /**
     * Returns the layout mapped to this rule.
     */
    public private(set) ?Layout $layout;

    /**
     * Returns if the rule is enabled.
     */
    public private(set) bool $isEnabled;

    /**
     * Returns the rule priority.
     */
    public private(set) int $priority;

    /**
     * Returns the rule description.
     */
    public private(set) string $description;

    /**
     * Returns all the targets in the rule.
     */
    public private(set) TargetList $targets {
        get => TargetList::fromArray($this->targets->toArray());
    }

    /**
     * Returns all conditions in the rule.
     */
    public private(set) ConditionList $conditions {
        get => ConditionList::fromArray($this->conditions->toArray());
    }
}

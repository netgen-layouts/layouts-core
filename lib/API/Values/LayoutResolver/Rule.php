<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Closure;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LazyPropertyTrait;
use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;

final class Rule implements Value
{
    use HydratorTrait;
    use LazyPropertyTrait;
    use ValueStatusTrait;

    public private(set) UuidInterface $id;

    public private(set) Status $status;

    /**
     * Returns the UUID of the rule group where this rule belongs.
     */
    public private(set) UuidInterface $ruleGroupId;

    /**
     * Returns if the rule is enabled.
     */
    public private(set) bool $enabled;

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

    private Layout|Closure|null $layout;

    /**
     * Returns the layout mapped to this rule.
     */
    public function getLayout(): ?Layout
    {
        return $this->getLazyProperty($this->layout);
    }
}

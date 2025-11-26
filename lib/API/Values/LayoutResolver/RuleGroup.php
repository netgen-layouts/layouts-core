<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;

final class RuleGroup implements Value
{
    use HydratorTrait;
    use ValueStatusTrait;

    /**
     * The UUID of the root rule group.
     *
     * Only one root rule group can exist and all other rule groups are below it.
     */
    public const string ROOT_UUID = '00000000-0000-0000-0000-000000000000';

    public private(set) UuidInterface $id;

    public private(set) Status $status;

    /**
     * Returns the UUID of the parent rule group where this rule group is located.
     *
     * If rule group does not have a parent rule group, null is returned.
     */
    public private(set) ?UuidInterface $parentId;

    /**
     * Returns human readable name of the rule group.
     */
    public private(set) string $name;

    /**
     * Return human readable description of the rule group.
     */
    public private(set) string $description;

    /**
     * Returns if the rule group is enabled.
     */
    public private(set) bool $enabled;

    /**
     * Returns the rule group priority.
     */
    public private(set) int $priority;

    /**
     * Returns all the rules in the rule group.
     */
    public private(set) RuleList $rules {
        get => RuleList::fromArray($this->rules->toArray());
    }

    /**
     * Returns all conditions in the rule group.
     */
    public private(set) ConditionList $conditions {
        get => ConditionList::fromArray($this->conditions->toArray());
    }
}

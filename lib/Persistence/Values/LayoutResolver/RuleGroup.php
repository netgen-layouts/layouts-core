<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Persistence\Values\Value;
use Netgen\Layouts\Utils\HydratorTrait;

final class RuleGroup extends Value
{
    use HydratorTrait;

    /**
     * The UUID of the root rule group.
     *
     * Only one root rule group can exist and all other rule groups are below it.
     */
    public const ROOT_UUID = '00000000-0000-0000-0000-000000000000';

    /**
     * Rule group ID.
     */
    public int $id;

    /**
     * Rule group UUID.
     */
    public string $uuid;

    /**
     * The depth of the rule group in the tree.
     */
    public int $depth;

    /**
     * Materialized path of the rule group.
     */
    public string $path;

    /**
     * ID of the parent rule group or null if rule group has no parent.
     */
    public ?int $parentId;

    /**
     * UUID of the parent rule group or null if rule group has no parent.
     */
    public ?string $parentUuid;

    /**
     * Human readable rule group name.
     */
    public string $name;

    /**
     * Human readable description of the rule group.
     */
    public string $description;

    /**
     * A flag indicating if the rule group is enabled or not.
     */
    public bool $enabled;

    /**
     * Rule group priority.
     */
    public int $priority;
}

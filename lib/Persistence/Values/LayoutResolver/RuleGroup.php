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
     *
     * @var int
     */
    public $id;

    /**
     * Rule group UUID.
     *
     * @var string
     */
    public $uuid;

    /**
     * The depth of the rule group in the tree.
     *
     * @var int
     */
    public $depth;

    /**
     * Materialized path of the rule group.
     *
     * @var string
     */
    public $path;

    /**
     * ID of the parent rule group or null if rule group has no parent.
     *
     * @var int|null
     */
    public $parentId;

    /**
     * UUID of the parent rule group or null if rule group has no parent.
     *
     * @var string|null
     */
    public $parentUuid;

    /**
     * A flag indicating if the rule group is enabled or not.
     *
     * @var bool
     */
    public $enabled;

    /**
     * Rule group priority.
     *
     * @var int
     */
    public $priority;

    /**
     * Human readable comment of the rule group.
     *
     * @var string
     */
    public $comment;
}

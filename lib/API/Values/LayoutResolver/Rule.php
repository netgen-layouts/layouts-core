<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Value;

interface Rule extends Value
{
    /**
     * Returns the rule ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the layout mapped to this rule.
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout|null
     */
    public function getLayout(): ?Layout;

    /**
     * Returns if the rule is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Returns the rule priority.
     *
     * @return int
     */
    public function getPriority(): int;

    /**
     * Returns the rule comment.
     *
     * @return string
     */
    public function getComment(): ?string;

    /**
     * Returns all the targets in the rule.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Target[]
     */
    public function getTargets(): array;

    /**
     * Returns all conditions in the rule.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition[]
     */
    public function getConditions(): array;

    /**
     * Returns if the rule can be enabled.
     *
     * Rule can be enabled if it is published and has a mapped layout and at least one target.
     *
     * @return bool
     */
    public function canBeEnabled(): bool;
}

<?php

namespace Netgen\BlockManager\API\Values\LayoutResolver;

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
    public function getLayout();

    /**
     * Returns if the rule is enabled.
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * Returns the rule priority.
     *
     * @return int
     */
    public function getPriority();

    /**
     * Returns the rule comment.
     *
     * @return string
     */
    public function getComment();

    /**
     * Returns all the targets in the rule.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Target[]
     */
    public function getTargets();

    /**
     * Returns all conditions in the rule.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition[]
     */
    public function getConditions();

    /**
     * Returns if the rule can be enabled.
     *
     * Rule can be enabled if it is published and has a mapped layout and at least one target.
     *
     * @return bool
     */
    public function canBeEnabled();
}

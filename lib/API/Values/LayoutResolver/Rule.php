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
     * Returns resolved layout.
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function getLayout();

    /**
     * Returns if the rule is published.
     *
     * @return bool
     */
    public function isPublished();

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
     * Returns the targets this rule applies to.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Target[]
     */
    public function getTargets();

    /**
     * Returns rule conditions.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition[]
     */
    public function getConditions();

    /**
     * Returns if the rule can be enabled.
     *
     * @return bool
     */
    public function canBeEnabled();
}

<?php

namespace Netgen\BlockManager\API\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\Value;

interface Rule extends Value
{
    const STATUS_DRAFT = 0;

    const STATUS_PUBLISHED = 1;

    const STATUS_ARCHIVED = 2;

    /**
     * Returns the rule ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the rule status.
     *
     * @return int
     */
    public function getStatus();

    /**
     * Returns resolved layout.
     *
     * @return \Netgen\BlockManager\API\Values\Page\LayoutReference
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
}

<?php

namespace Netgen\BlockManager\API\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\Value;

interface Condition extends Value
{
    /**
     * Returns the condition ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the rule ID to which this condition belongs to.
     *
     * @return int|string
     */
    public function getRuleId();

    /**
     * Returns the condition type.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface
     */
    public function getConditionType();

    /**
     * Returns if the condition is published.
     *
     * @return bool
     */
    public function isPublished();

    /**
     * Returns the condition value.
     *
     * @return mixed
     */
    public function getValue();
}

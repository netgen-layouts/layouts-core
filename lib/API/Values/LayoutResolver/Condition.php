<?php

declare(strict_types=1);

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
     * Returns the ID of the rule to which this condition belongs to.
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
     * Returns the condition value.
     *
     * Value of the condition can be a scalar, an associative array, numeric array or a nested
     * combination of these.
     *
     * @return mixed
     */
    public function getValue();
}

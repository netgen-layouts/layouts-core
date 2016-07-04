<?php

namespace Netgen\BlockManager\API\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\Value;

interface Target extends Value
{
    /**
     * Returns the target ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the target status.
     *
     * @return int
     */
    public function getStatus();

    /**
     * Returns the rule ID where this target belongs.
     *
     * @return int|string
     */
    public function getRuleId();

    /**
     * Returns the target type.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns the target value.
     *
     * @return mixed
     */
    public function getValue();
}

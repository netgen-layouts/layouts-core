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
     * Returns the rule ID where this target belongs.
     *
     * @return int|string
     */
    public function getRuleId();

    /**
     * Returns the target type.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface
     */
    public function getTargetType();

    /**
     * Returns if the target is published.
     *
     * @return bool
     */
    public function isPublished();

    /**
     * Returns the target value.
     *
     * @return mixed
     */
    public function getValue();
}

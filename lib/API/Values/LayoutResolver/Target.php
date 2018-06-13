<?php

declare(strict_types=1);

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
     * Returns the ID of the rule where this target belongs.
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
     * Returns the target value.
     *
     * Target value is always a scalar.
     *
     * @return mixed
     */
    public function getValue();
}

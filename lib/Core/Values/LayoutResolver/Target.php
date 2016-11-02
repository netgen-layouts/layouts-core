<?php

namespace Netgen\BlockManager\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\LayoutResolver\Target as APITarget;
use Netgen\BlockManager\ValueObject;

class Target extends ValueObject implements APITarget
{
    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var int|string
     */
    protected $ruleId;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface
     */
    protected $targetType;

    /**
     * @var bool
     */
    protected $published;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * Returns the target ID.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the target status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns the rule ID where this target belongs.
     *
     * @return int|string
     */
    public function getRuleId()
    {
        return $this->ruleId;
    }

    /**
     * Returns the target type.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface
     */
    public function getTargetType()
    {
        return $this->targetType;
    }

    /**
     * Returns if the target is published.
     *
     * @return bool
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * Returns the target value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}

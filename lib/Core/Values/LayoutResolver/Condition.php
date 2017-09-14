<?php

namespace Netgen\BlockManager\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\LayoutResolver\Condition as APICondition;
use Netgen\BlockManager\ValueObject;

class Condition extends ValueObject implements APICondition
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
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface
     */
    protected $conditionType;

    /**
     * @var bool
     */
    protected $published;

    /**
     * @var mixed
     */
    protected $value;

    public function getId()
    {
        return $this->id;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getRuleId()
    {
        return $this->ruleId;
    }

    public function getConditionType()
    {
        return $this->conditionType;
    }

    public function isPublished()
    {
        return $this->published;
    }

    public function getValue()
    {
        return $this->value;
    }
}

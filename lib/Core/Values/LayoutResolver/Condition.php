<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\LayoutResolver\Condition as APICondition;
use Netgen\BlockManager\Core\Values\Value;
use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;

final class Condition extends Value implements APICondition
{
    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var int|string
     */
    protected $ruleId;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface
     */
    protected $conditionType;

    /**
     * @var mixed
     */
    protected $value;

    public function getId()
    {
        return $this->id;
    }

    public function getRuleId()
    {
        return $this->ruleId;
    }

    public function getConditionType(): ConditionTypeInterface
    {
        return $this->conditionType;
    }

    public function getValue()
    {
        return $this->value;
    }
}

<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\API\Values\ValueStatusTrait;
use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Netgen\BlockManager\Utils\HydratorTrait;

final class Condition implements Value
{
    use HydratorTrait;
    use ValueStatusTrait;

    /**
     * @var int|string
     */
    private $id;

    /**
     * @var int|string
     */
    private $ruleId;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface
     */
    private $conditionType;

    /**
     * @var mixed
     */
    private $value;

    /**
     * Returns the condition ID.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the ID of the rule to which this condition belongs to.
     *
     * @return int|string
     */
    public function getRuleId()
    {
        return $this->ruleId;
    }

    /**
     * Returns the condition type.
     */
    public function getConditionType(): ConditionTypeInterface
    {
        return $this->conditionType;
    }

    /**
     * Returns the condition value.
     *
     * Value of the condition can be a scalar, an associative array, numeric array or a nested
     * combination of these.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}

<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Layout\Resolver\ConditionTypeInterface;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;

final class Condition implements Value
{
    use HydratorTrait;
    use ValueStatusTrait;

    /**
     * @var \Ramsey\Uuid\UuidInterface
     */
    private $id;

    /**
     * @var \Ramsey\Uuid\UuidInterface
     */
    private $ruleId;

    /**
     * @var \Netgen\Layouts\Layout\Resolver\ConditionTypeInterface
     */
    private $conditionType;

    /**
     * @var mixed
     */
    private $value;

    /**
     * Returns the condition UUID.
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * Returns the UUID of the rule to which this condition belongs to.
     */
    public function getRuleId(): UuidInterface
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

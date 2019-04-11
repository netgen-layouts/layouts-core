<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Layout\Resolver\TargetTypeInterface;
use Netgen\Layouts\Utils\HydratorTrait;

final class Target implements Value
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
     * @var \Netgen\Layouts\Layout\Resolver\TargetTypeInterface
     */
    private $targetType;

    /**
     * @var mixed
     */
    private $value;

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
     * Returns the ID of the rule where this target belongs.
     *
     * @return int|string
     */
    public function getRuleId()
    {
        return $this->ruleId;
    }

    /**
     * Returns the target type.
     */
    public function getTargetType(): TargetTypeInterface
    {
        return $this->targetType;
    }

    /**
     * Returns the target value.
     *
     * Target value is always a scalar.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}

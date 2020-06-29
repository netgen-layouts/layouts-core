<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Layout\Resolver\TargetTypeInterface;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;

final class Target implements Value
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
     * @var \Netgen\Layouts\Layout\Resolver\TargetTypeInterface
     */
    private $targetType;

    /**
     * @var mixed
     */
    private $value;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * Returns the UUID of the rule where this target belongs.
     */
    public function getRuleId(): UuidInterface
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

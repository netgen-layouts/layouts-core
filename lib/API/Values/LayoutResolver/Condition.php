<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\Layout\Resolver\ConditionTypeInterface;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;

abstract class Condition implements Value
{
    use HydratorTrait;

    protected UuidInterface $id;

    protected ConditionTypeInterface $conditionType;

    /**
     * @var int|string|array<mixed>
     */
    protected int|string|array $value;

    public function getId(): UuidInterface
    {
        return $this->id;
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
     * Can be a scalar or a multidimensional array of scalars.
     *
     * @return int|string|array<mixed>
     */
    public function getValue(): int|string|array
    {
        return $this->value;
    }
}

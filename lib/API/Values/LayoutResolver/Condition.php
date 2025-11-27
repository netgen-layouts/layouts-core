<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Layout\Resolver\ConditionTypeInterface;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;

abstract class Condition implements Value
{
    use HydratorTrait;
    use ValueStatusTrait;

    final public protected(set) UuidInterface $id;

    /**
     * Returns the condition type.
     */
    final public protected(set) ConditionTypeInterface $conditionType;

    /**
     * Returns the condition value.
     *
     * Can be a scalar or a multidimensional array of scalars.
     *
     * @var int|string|array<mixed>
     */
    final public protected(set) int|string|array $value;
}

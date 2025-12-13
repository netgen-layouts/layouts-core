<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Layout\Resolver\ConditionTypeInterface;
use Netgen\Layouts\Utils\HydratorTrait;
use Symfony\Component\Uid\Uuid;

abstract class Condition implements Value
{
    use HydratorTrait;
    use ValueStatusTrait;

    public private(set) Uuid $id;

    /**
     * Returns the condition type.
     */
    public private(set) ConditionTypeInterface $conditionType;

    /**
     * Returns the condition value.
     *
     * Can be a scalar or a multidimensional array of scalars.
     *
     * @var int|string|mixed[]
     */
    public private(set) int|string|array $value;
}

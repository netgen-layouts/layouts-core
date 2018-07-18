<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\LayoutResolver;

use Netgen\BlockManager\Utils\HydratorTrait;

final class ConditionCreateStruct
{
    use HydratorTrait;

    /**
     * Identifier of the condition type.
     *
     * @var string
     */
    public $type;

    /**
     * Condition value. Can be a scalar or a multidimensional array of scalars.
     *
     * @var mixed
     */
    public $value;
}

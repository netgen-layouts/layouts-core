<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Utils\HydratorTrait;

final class ConditionCreateStruct
{
    use HydratorTrait;

    /**
     * Identifier of the condition type.
     */
    public string $type;

    /**
     * Condition value. Can be a scalar or a multidimensional array of scalars.
     *
     * @var mixed
     */
    public $value;
}

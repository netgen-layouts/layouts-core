<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

abstract class ConditionStruct
{
    /**
     * The value of the condition. Can be a scalar or a multidimensional array of scalars.
     *
     * @var mixed
     */
    public $value;
}

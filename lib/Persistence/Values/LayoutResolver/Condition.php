<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Persistence\Values\Value;
use Netgen\Layouts\Utils\HydratorTrait;

abstract class Condition extends Value
{
    use HydratorTrait;

    /**
     * Condition ID.
     */
    public int $id;

    /**
     * Condition UUID.
     */
    public string $uuid;

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

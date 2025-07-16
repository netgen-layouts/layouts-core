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
     *
     * @final
     */
    public int $id;

    /**
     * Condition UUID.
     *
     * @final
     */
    public string $uuid;

    /**
     * Identifier of the condition type.
     *
     * @final
     */
    public string $type;

    /**
     * Condition value. Can be a scalar or a multidimensional array of scalars.
     *
     * @final
     *
     * @var mixed
     */
    public $value;
}

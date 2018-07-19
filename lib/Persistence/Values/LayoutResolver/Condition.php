<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\LayoutResolver;

use Netgen\BlockManager\Persistence\Values\Value;

final class Condition extends Value
{
    /**
     * Condition ID.
     *
     * @var int|string
     */
    public $id;

    /**
     * ID of the rule where the condition is located.
     *
     * @var int|string
     */
    public $ruleId;

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

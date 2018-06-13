<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\LayoutResolver;

use Netgen\BlockManager\Value;

final class TargetCreateStruct extends Value
{
    /**
     * Identifier of the type of the new target.
     *
     * @var string
     */
    public $type;

    /**
     * Value of the new target.
     *
     * @var int|string
     */
    public $value;
}

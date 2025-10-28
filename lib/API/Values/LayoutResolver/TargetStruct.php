<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

abstract class TargetStruct
{
    /**
     * The value of the target.
     */
    public int|string $value;
}

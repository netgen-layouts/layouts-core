<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

final class TargetCreateStruct extends TargetStruct
{
    /**
     * The type of the target.
     *
     * Required.
     */
    public string $type;
}

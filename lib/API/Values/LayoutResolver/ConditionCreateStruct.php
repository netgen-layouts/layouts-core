<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

final class ConditionCreateStruct extends ConditionStruct
{
    /**
     * The type of the condition.
     *
     * Required.
     */
    public string $type;
}

<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\LayoutResolver;

final class ConditionCreateStruct extends ConditionStruct
{
    /**
     * The type of the condition.
     *
     * Required.
     *
     * @var string
     */
    public $type;
}

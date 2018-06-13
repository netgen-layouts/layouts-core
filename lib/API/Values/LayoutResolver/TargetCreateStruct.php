<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\LayoutResolver;

final class TargetCreateStruct extends TargetStruct
{
    /**
     * The type of the target.
     *
     * Required.
     *
     * @var string
     */
    public $type;
}

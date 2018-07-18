<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\LayoutResolver;

use Netgen\BlockManager\Utils\HydratorTrait;

final class TargetUpdateStruct
{
    use HydratorTrait;

    /**
     * New value of the target.
     *
     * @var int|string
     */
    public $value;
}

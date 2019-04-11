<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Utils\HydratorTrait;

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

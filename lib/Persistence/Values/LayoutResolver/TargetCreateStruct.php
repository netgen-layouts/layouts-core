<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Utils\HydratorTrait;

final class TargetCreateStruct
{
    use HydratorTrait;

    /**
     * Identifier of the type of the new target.
     */
    public string $type;

    /**
     * Value of the new target.
     *
     * @var int|string
     */
    public $value;
}

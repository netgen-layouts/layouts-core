<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Collection;

use Netgen\Layouts\Utils\HydratorTrait;

final class SlotUpdateStruct
{
    use HydratorTrait;

    /**
     * New view type.
     *
     * @var string|null
     */
    public $viewType;
}

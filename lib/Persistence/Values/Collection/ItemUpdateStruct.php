<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Collection;

use Netgen\Layouts\Utils\HydratorTrait;

final class ItemUpdateStruct
{
    use HydratorTrait;

    /**
     * New item configuration.
     *
     * @var array|null
     */
    public $config;
}

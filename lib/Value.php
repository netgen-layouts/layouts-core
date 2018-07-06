<?php

declare(strict_types=1);

namespace Netgen\BlockManager;

use Netgen\BlockManager\Utils\HydratorTrait;

abstract class Value
{
    use HydratorTrait;

    /**
     * Creates the object and hydrates it with property values provided in $data array.
     */
    public function __construct(array $data = [])
    {
        $this->hydrate($data);
    }
}

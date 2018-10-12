<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Bundle\FixturesBundle\Item;

final class Value
{
    /**
     * @var int
     */
    public $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
}

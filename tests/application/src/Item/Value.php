<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\App\Item;

final class Value
{
    public int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
}

<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\App\Item;

final class Value
{
    public function __construct(
        public private(set) int $id,
    ) {}
}

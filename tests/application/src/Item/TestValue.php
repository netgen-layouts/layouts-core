<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\App\Item;

final class TestValue
{
    public function __construct(
        public private(set) int $id,
    ) {}
}

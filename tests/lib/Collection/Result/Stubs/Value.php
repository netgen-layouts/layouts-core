<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Result\Stubs;

final class Value
{
    public function __construct(
        public private(set) ?int $value,
    ) {}
}

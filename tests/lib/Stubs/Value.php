<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Stubs;

use Netgen\Layouts\Utils\HydratorTrait;

final class Value
{
    use HydratorTrait;

    public string $a;

    public protected(set) string $b;

    public private(set) string $c;
}

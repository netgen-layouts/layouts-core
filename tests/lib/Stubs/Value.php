<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Stubs;

use Netgen\Layouts\Utils\HydratorTrait;

final class Value
{
    use HydratorTrait;

    public string $a;

    protected string $b;

    private string $c;

    public function getA(): string
    {
        return $this->a;
    }

    public function getB(): string
    {
        return $this->b;
    }

    public function getC(): string
    {
        return $this->c;
    }
}

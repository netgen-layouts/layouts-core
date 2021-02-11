<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Result\Stubs;

final class Value
{
    private ?int $value;

    public function __construct(?int $value)
    {
        $this->value = $value;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }
}

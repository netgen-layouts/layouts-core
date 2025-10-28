<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Result\Stubs;

final class Value
{
    public function __construct(
        private ?int $value,
    ) {}

    public function getValue(): ?int
    {
        return $this->value;
    }
}

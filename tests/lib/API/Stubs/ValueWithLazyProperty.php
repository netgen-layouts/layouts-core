<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Stubs;

use Netgen\Layouts\API\Values\LazyPropertyTrait;

final class ValueWithLazyProperty
{
    use LazyPropertyTrait;

    public mixed $value;

    public function __construct(callable $callable)
    {
        $this->value = $callable;
    }

    public function getValue(): mixed
    {
        return $this->getLazyProperty($this->value);
    }
}

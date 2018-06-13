<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Stubs;

use Netgen\BlockManager\Core\Values\LazyPropertyTrait;

final class ValueWithLazyProperty
{
    use LazyPropertyTrait;

    /**
     * @var callable
     */
    public $value;

    public function __construct(callable $callable)
    {
        $this->value = $callable;
    }

    public function getValue()
    {
        return $this->getLazyProperty($this->value);
    }
}

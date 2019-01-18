<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\API\Stubs;

use Netgen\BlockManager\API\Values\LazyPropertyTrait;

final class ValueWithLazyProperty
{
    use LazyPropertyTrait;

    /**
     * @var mixed
     */
    public $value;

    public function __construct(callable $callable)
    {
        $this->value = $callable;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->getLazyProperty($this->value);
    }
}

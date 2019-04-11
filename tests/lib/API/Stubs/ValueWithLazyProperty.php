<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Stubs;

use Netgen\Layouts\API\Values\LazyPropertyTrait;

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

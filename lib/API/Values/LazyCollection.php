<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values;

use Closure;
use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @template TKey of array-key
 * @template TValue of object
 *
 * @extends \Doctrine\Common\Collections\AbstractLazyCollection<TKey, TValue>
 */
abstract class LazyCollection extends AbstractLazyCollection
{
    private Closure $closure;

    private function __construct(callable $callable)
    {
        $this->closure = $callable(...);
    }

    final public static function fromCallable(callable $callable): static
    {
        return new static($callable);
    }

    /**
     * @param array<TKey, TValue> $array
     */
    final public static function fromArray(array $array): static
    {
        return new static(static fn (): array => $array);
    }

    final protected function doInitialize(): void
    {
        $this->collection = new ArrayCollection(($this->closure)());
    }
}

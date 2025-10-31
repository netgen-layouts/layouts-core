<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values;

use Closure;
use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @extends \Doctrine\Common\Collections\AbstractLazyCollection<array-key, object>
 */
final class LazyCollection extends AbstractLazyCollection
{
    private Closure $closure;

    public function __construct(callable $callable)
    {
        $this->closure = $callable(...);
    }

    protected function doInitialize(): void
    {
        $this->collection = new ArrayCollection(($this->closure)());
    }
}

<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values;

use Closure;
use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;

use function call_user_func;

/**
 * @extends \Doctrine\Common\Collections\AbstractLazyCollection<array-key, object>
 */
final class LazyCollection extends AbstractLazyCollection
{
    private Closure $closure;

    public function __construct(callable $callable)
    {
        $this->closure = Closure::fromCallable($callable);
    }

    protected function doInitialize(): void
    {
        $this->collection = new ArrayCollection(call_user_func($this->closure));
    }
}

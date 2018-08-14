<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;

final class LazyCollection extends AbstractLazyCollection
{
    /**
     * @var callable
     */
    private $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    protected function doInitialize(): void
    {
        $this->collection = new ArrayCollection(call_user_func($this->callable));
    }
}

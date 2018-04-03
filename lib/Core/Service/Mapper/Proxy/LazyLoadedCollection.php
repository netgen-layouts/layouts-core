<?php

namespace Netgen\BlockManager\Core\Service\Mapper\Proxy;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;

final class LazyLoadedCollection extends AbstractLazyCollection
{
    /**
     * @var callable
     */
    private $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    protected function doInitialize()
    {
        $this->collection = new ArrayCollection(call_user_func($this->callable));
    }
}
